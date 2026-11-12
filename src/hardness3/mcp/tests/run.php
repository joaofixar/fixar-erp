<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

$entrypoint = file_get_contents(dirname(__DIR__) . '/public/index.php');
if ($entrypoint === false || strpos($entrypoint, "ini_set('display_errors', '0')") === false) {
    throw new RuntimeException('MCP entrypoint must disable PHP error display before loading application code.');
}
mcpAssert(strpos((string) $entrypoint, 'session_start') === false && strpos((string) $entrypoint, 'index-framework.php') === false, 'public MCP entrypoint must not start an ERP session or load the Framework');

use HardnessMcp\Core\Bootstrap;
use HardnessMcp\Core\McpException;
use HardnessMcp\Core\Request;
use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Database\ConnectionFactory;
use HardnessMcp\Endpoint\McpEndpointResolver;
use HardnessMcp\Auth\ApiKeyGenerator;
use HardnessMcp\Auth\ApiKeyParser;
use HardnessMcp\Auth\ApiKeyVerifier;
use HardnessMcp\Admin\McpConnectionAdminService;
use HardnessMcp\Audit\McpAuditService;
use HardnessMcp\Admin\OAuthAdminService;
use HardnessMcp\OAuth\DynamicClientRegistrationService;
use HardnessMcp\OAuth\DcrValidationException;
use HardnessMcp\OAuth\DcrDiagnostic;
use HardnessMcp\Core\HttpDiagnostic;
use HardnessMcp\Scopes\ScopeCatalog;
use HardnessMcp\RateLimit\ApcuRateLimiter;
use HardnessMcp\RateLimit\RateLimiterInterface;
use HardnessMcp\RateLimit\RateLimitDecision;
use HardnessMcp\RateLimit\RateLimitPolicy;
use HardnessMcp\RateLimit\RateLimiterUnavailableException;
use HardnessMcp\Tenant\TenantResolver;
use HardnessMcp\Tenant\ResolvedTenantContext;
use HardnessMcp\Legacy\LegacyActorContext;
use HardnessMcp\Legacy\LegacyActorResolverInterface;
use HardnessMcp\Legacy\LegacyContextResolutionException;
use HardnessMcp\Legacy\LegacyExecutionContextAdapter;
use HardnessMcp\Legacy\McpFrameworkBridge;
use HardnessMcp\Legacy\LegacyBridgeException;
use HardnessMcp\Protocol\JsonRpcServer;
use HardnessMcp\Tools\Catalog\CompanyToolsModule;
use HardnessMcp\Tools\Catalog\ProductToolsModule;
use HardnessMcp\Tools\Catalog\QuoteToolsModule;
use HardnessMcp\Tools\Catalog\SalesToolsModule;
use HardnessMcp\Tools\Catalog\FinanceToolsModule;
use HardnessMcp\Tools\Catalog\OrderToolsModule;
use HardnessMcp\Tools\Catalog\ItemReportToolsModule;
use HardnessMcp\Tools\Catalog\CommercialToolsModule;
use HardnessMcp\Tools\Catalog\ToolCatalogConsistencyValidator;
use HardnessMcp\Tools\Catalog\ToolCatalogFactory;
use HardnessMcp\Tools\Catalog\ToolModuleInterface;
use HardnessMcp\Tools\Catalog\SystemToolsModule;
use HardnessMcp\Tools\Catalog\IdeSmtToolsModule;
use HardnessMcp\Tools\Finance\Domain\FinanceSummaryRepositoryInterface;
use HardnessMcp\Tools\System\ContextPilot\ContextPilotGatewayInterface;
use HardnessMcp\Tools\Quote\Create\QuoteCreateGatewayInterface;
use HardnessMcp\Tools\Quote\Pdf\QuotePdfGatewayInterface;
use HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination\IdeSmtGenerateOrderGatewayInterface;
use HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination\IdeSmtOrderDestination;
use HardnessMcp\Tools\Company\Domain\CompanyRepositoryInterface;
use HardnessMcp\Tools\Partner\Domain\PartnerRepositoryInterface;
use HardnessMcp\Tools\Product\Domain\ProductRepositoryInterface;
use HardnessMcp\Tools\Product\PartnerReference\Domain\PartnerReferenceProductRepositoryInterface;
use HardnessMcp\Tools\Quote\Domain\QuoteRepositoryInterface;
use HardnessMcp\Tools\Quote\Summary\Domain\QuoteSummaryRepositoryInterface;
use HardnessMcp\Tools\Sales\Domain\SalesSummaryRepositoryInterface;
use HardnessMcp\Tools\Order\Domain\OrderBacklogRepositoryInterface;
use HardnessMcp\Tools\ToolExecutor;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use HardnessMcp\Tools\ToolRegistry;
use HardnessMcp\Tools\Validation\ToolArgumentsValidator;

class TestRateLimiter implements RateLimiterInterface
{
    /** @var string[] */ public array $keys = [];
    private bool $deny;
    public function __construct(bool $deny = false) { $this->deny = $deny; }
    public function consume(string $key, int $limit, int $windowSeconds): RateLimitDecision { $this->keys[] = $key; return new RateLimitDecision(!$this->deny, $this->deny ? 12 : 0); }
}

class TestPdo extends PDO
{
    public function __construct() {}
}

final class TestLegacyActorResolver implements LegacyActorResolverInterface
{
    public ?LegacyActorContext $actor;
    public function __construct(?LegacyActorContext $actor) { $this->actor = $actor; }
    public function resolve(int $companyId, int $userId): ?LegacyActorContext { return $this->actor; }
}

final class TestContextPilotGateway implements ContextPilotGatewayInterface
{
    public ?ExecutionContext $receivedContext = null;
    public bool $fail = false;
    /** @return array<string,int|string> */
    public function execute(ExecutionContext $executionContext): array
    {
        $this->receivedContext = $executionContext;
        if ($this->fail) { throw new RuntimeException('bridge credentials/path must not escape'); }
        return ['implementation' => 'internal-only', 'company_id' => $executionContext->getCompanyId(), 'user_id' => $executionContext->getResponsibleUserId() ?? 0, 'profile_id' => 12, 'approval_level' => 2];
    }
}

final class TestQuoteCreateGateway implements QuoteCreateGatewayInterface
{
    /** @var array<string,mixed>|null */ public ?array $arguments = null;
    public ?ExecutionContext $context = null;
    public bool $fail = false;
    public ?string $failCode = null;
    public function create(array $arguments, ExecutionContext $context): array
    {
        $this->arguments = $arguments; $this->context = $context;
        if ($this->fail) { throw new RuntimeException($this->failCode ?? 'legacy sql/path must not escape'); }
        return ['success' => true, 'quote_id' => 77, 'items_count' => count($arguments['items'] ?? [])];
    }
}

final class TestIdeSmtGenerateOrderGateway implements IdeSmtGenerateOrderGatewayInterface
{
    public ?int $quoteId = null;
    public ?string $destination = null;
    public ?ExecutionContext $context = null;
    public ?LegacyBridgeException $failure = null;
    public bool $postProcessingComplete = true;

    public function generate(int $quoteId, string $destination, ExecutionContext $context): array
    {
        $this->quoteId = $quoteId;
        $this->destination = $destination;
        $this->context = $context;
        if ($this->failure !== null) { throw $this->failure; }
        return [
            'success' => true,
            'quote_id' => $quoteId,
            'order_id' => 9001,
            'type' => IdeSmtOrderDestination::typeFor($destination),
            'order_status' => 6,
            'order_status_name' => 'Análise de crédito',
            'credit_review' => true,
            'post_processing_complete' => $this->postProcessingComplete,
            'destination' => $destination,
        ];
    }
}

final class TestQuotePdfGateway implements QuotePdfGatewayInterface
{
    public ?int $quoteId = null;
    public ?ExecutionContext $context = null;
    public bool $fail = false;
    public function generate(int $quoteId, ExecutionContext $context): array
    {
        $this->quoteId = $quoteId;
        $this->context = $context;
        if ($this->fail) { throw new RuntimeException('pdf generation failed'); }
        return ['pdf_url' => 'https://cliente.fastcrud.com.br/hardness3/dados_usuarios/tenant/tmp/orcamento-' . $quoteId . '.pdf'];
    }
}

class TestCompanyRepository implements CompanyRepositoryInterface
{
    /** @var array<string,mixed>|null */ public ?array $company = ['C004_Id' => 1, 'C004_Nome_Empresa' => 'Empresa Teste', 'C004_Nome_Abreviado' => 'Teste', 'C004_Cnpj' => '00000000000100', 'C004_Cidade' => 'São Paulo', 'C004_Uf' => 'SP', 'C004_Flag_Ativo' => 'S'];
    public int $calls = 0;
    public ?int $lastCompanyId = null;
    public bool $fail = false;
    /** @return array<string,mixed>|null */
    public function findBasicById(int $companyId): ?array
    {
        $this->calls++; $this->lastCompanyId = $companyId;
        if ($this->fail) { throw new RuntimeException('Repository failure.'); }
        return $this->company;
    }
}

class TestProductRepository implements ProductRepositoryInterface, PartnerReferenceProductRepositoryInterface
{
    public ?int $lastCompanyId = null; public int $calls = 0;
    /** @return array<int,array<string,mixed>> */
    public function search(int $companyId, string $query, int $limit): array
    {
        $this->lastCompanyId = $companyId; $this->calls++;
        return $companyId === 1 ? [['D001_Id' => 11, 'D001_Codigo_Produto' => 'ABC001', 'D001_Descricao_Produto' => 'Produto teste', 'D037_Unidade' => 'UN']] : [];
    }

    /** @return array<string,mixed>|null */
    public function findVisiblePartnerByCode(int $companyId, int $partnerCode, string $role): ?array
    {
        if ($companyId !== 1) { return null; }
        if ($partnerCode === 7 && $role === 'customer') { return ['D024_Id' => 7]; }
        if ($partnerCode === 8 && $role === 'supplier') { return ['D024_Id' => 8]; }
        return null;
    }

    /** @return array<int,array<string,mixed>> */
    public function findProductsByPartnerReference(int $companyId, int $partnerCode, string $role, string $referenceCode): array
    {
        $this->lastCompanyId = $companyId;
        if ($companyId !== 1) { return []; }
        if ($partnerCode === 7 && $role === 'customer' && $referenceCode === 'ABC-001') { return [['D001_Id' => 11, 'D001_Codigo_Produto' => 'ABC001', 'D001_Descricao_Produto' => 'Produto teste', 'D037_Unidade' => 'UN']]; }
        if ($partnerCode === 7 && $role === 'customer' && $referenceCode === 'MULTI') { return [['D001_Id' => 11, 'D001_Codigo_Produto' => 'ABC001', 'D001_Descricao_Produto' => 'Produto teste', 'D037_Unidade' => 'UN'], ['D001_Id' => 12, 'D001_Codigo_Produto' => 'ABC002', 'D001_Descricao_Produto' => 'Outro produto', 'D037_Unidade' => 'CX']]; }
        if ($partnerCode === 7 && $role === 'customer' && $referenceCode === 'X%_') { return [['D001_Id' => 13, 'D001_Codigo_Produto' => 'ESCAPE', 'D001_Descricao_Produto' => 'Código literal', 'D037_Unidade' => 'UN']]; }
        if ($partnerCode === 8 && $role === 'supplier' && $referenceCode === 'SUP-001') { return [['D001_Id' => 14, 'D001_Codigo_Produto' => 'SUP001', 'D001_Descricao_Produto' => 'Produto fornecedor', 'D037_Unidade' => 'UN']]; }
        return [];
    }
}

class TestPartnerRepository implements PartnerRepositoryInterface
{
    public ?int $lastCompanyId = null; public string $lastQuery = ''; public string $lastRole = ''; public int $lastLimit = 0;
    /** @return array<int,array<string,mixed>> */
    public function searchByDocument(int $companyId, string $query, string $role, int $limit): array
    {
        $this->lastCompanyId = $companyId; $this->lastQuery = $query; $this->lastRole = $role; $this->lastLimit = $limit;
        return $companyId === 1 ? [['D024_Id' => 7, 'D024_Nome_Empresa' => 'Atom Indústria Ltda', 'D024_Nome_Fantasia' => 'ATOM', 'D024_Cpf' => '', 'D024_Cnpj' => '12.345.678/0001-90', 'D024_Flag_Cliente' => 'S', 'D024_Flag_Fornecedor' => 'N', 'D024_Flag_Ativo' => 'S', 'D024_Cidade' => 'São Paulo', 'D024_Estado' => 'SP']] : [];
    }
    public function searchByName(int $companyId, string $query, string $role, int $limit): array { return $this->searchByDocument($companyId, $query, $role, $limit); }
    public function findVisibleById(int $companyId, int $partnerId): ?array { return $companyId===1&&$partnerId===7?['D024_Id'=>7,'D024_Nome_Empresa'=>'Atom Indústria Ltda','D024_Nome_Fantasia'=>'ATOM','D024_Flag_Pessoa_Fisica_Juridica'=>'J','D024_Cnpj'=>'12.345.678/0001-90','D024_Cpf'=>'','D024_Flag_Ativo'=>'S','D024_Flag_Cliente'=>'S','D024_Flag_Fornecedor'=>'N','D024_Endereco'=>'Rua Teste','D024_Numero'=>'10','D024_Complemento'=>'','D024_Bairro'=>'Centro','D024_Cidade'=>'São Paulo','D024_Estado'=>'SP','D024_Cep'=>'01000-000']:null; }
}

class TestQuoteRepository implements QuoteRepositoryInterface, QuoteSummaryRepositoryInterface
{
    public ?int $lastCompanyId = null; public ?string $lastOrigin = null; public ?int $lastSupervisorId = null; public int $searchCalls = 0; public int $findCalls = 0;
    /** @return array<int,array<string,mixed>> */
    public function search(int $companyId, string $issuedFrom, string $issuedTo, ?int $customerId, ?string $origin, int $limit): array
    {
        $this->lastCompanyId = $companyId; $this->lastOrigin = $origin; $this->searchCalls++;
        return $companyId === 1 ? [$this->header()] : [];
    }
    /** @return array{header:array<string,mixed>,items:array<int,array<string,mixed>>}|null */
    public function findById(int $companyId, int $quoteId): ?array
    {
        $this->lastCompanyId = $companyId; $this->findCalls++;
        if ($companyId !== 1 || $quoteId !== 50) { return null; }
        return ['header' => $this->header(), 'items' => [['T004_Item' => 1, 'D001_Id' => 11, 'D001_Codigo_Produto' => 'ABC001', 'T004_Descricao_Produto' => 'Produto histórico', 'T004_Codigo_Local' => 'SALA', 'D037_Unidade' => 'UN', 'T004_Quantidade' => '2.00000', 'T004_Valor_Preco_Unitario' => '10.0000000000', 'T004_Valor_Total_Preco' => '20.00']]];
    }
    /** @return array<string,mixed> */
    private function header(): array { return ['T003_Id' => 50, 'T003_Data_Emissao' => '2026-08-01', 'status_label' => 'Pendente', 'T003_Valor_Total' => '20.00', 'D024_Id' => 7, 'D024_Nome_Fantasia' => 'Cliente', 'D024_Nome_Empresa' => 'Cliente Ltda']; }
    public function summary(int $companyId, string $dateFrom, string $dateTo, ?int $customerId, ?int $sellerInternalId, ?int $supervisorId, ?string $status, ?string $origin): array { $this->lastCompanyId=$companyId; $this->lastSupervisorId=$supervisorId; $this->lastOrigin=$origin; return $companyId===1?['quoted_total'=>'200.00','quote_count'=>4]:['quoted_total'=>'0','quote_count'=>0]; }
    public function grouped(int $companyId, string $dateFrom, string $dateTo, string $groupBy, ?int $customerId, ?int $sellerInternalId, ?int $supervisorId, ?string $status, ?string $origin, int $limit): array { $this->lastCompanyId=$companyId; $this->lastSupervisorId=$supervisorId; $this->lastOrigin=$origin; if($companyId!==1)return []; if($groupBy==='customer')return [['customer_id'=>7,'customer_name'=>'Cliente','quoted_total'=>'120.00','quote_count'=>2],['customer_id'=>8,'customer_name'=>'Outro','quoted_total'=>'80.00','quote_count'=>2]]; if($groupBy==='seller_internal')return [['seller_internal_id'=>9,'seller_internal_name'=>'Vendedor','supervisor_id'=>4,'supervisor_name'=>'Gerente','quoted_total'=>'200.00','quote_count'=>4]]; if($groupBy==='supervisor')return [['supervisor_id'=>4,'supervisor_name'=>'Gerente','quoted_total'=>'200.00','quote_count'=>4]]; if($groupBy==='status')return [['status'=>'P','quoted_total'=>'150.00','quote_count'=>3],['status'=>'C','quoted_total'=>'50.00','quote_count'=>1]]; return [['date'=>'2026-08-01','quoted_total'=>'100.00','quote_count'=>2]]; }
}

class TestSalesSummaryRepository implements SalesSummaryRepositoryInterface
{
    public ?int $lastCompanyId = null;
    public function summary(int $companyId, string $dateFrom, string $dateTo, ?int $customerId, ?int $sellerInternalId): array
    {
        $this->lastCompanyId = $companyId;
        return $companyId === 1 ? ['invoiced_total' => '128450.75', 'invoice_count' => 83] : ['invoiced_total' => '0', 'invoice_count' => 0];
    }
    public function grouped(int $companyId, string $dateFrom, string $dateTo, string $groupBy, ?int $customerId, ?int $sellerInternalId, int $limit): array
    {
        $this->lastCompanyId = $companyId;
        if ($companyId !== 1) { return []; }
        if ($groupBy === 'customer') return [['customer_id' => 7, 'customer_name' => 'Cliente', 'invoiced_total' => '32000.00', 'invoice_count' => 14], ['customer_id' => 8, 'customer_name' => 'Outro', 'invoiced_total' => '27800.00', 'invoice_count' => 9]];
        return [['date' => '2026-08-01', 'invoiced_total' => '15200.00', 'invoice_count' => 12], ['date' => '2026-08-02', 'invoiced_total' => '18900.50', 'invoice_count' => 16]];
    }
}
class TestFinanceSummaryRepository implements FinanceSummaryRepositoryInterface { public function summary(string $kind,int $companyId,string $from,string $to,string $status,?int $partnerId,?int $carrierId,string $dateBasis='due_date'):array{return ['total_amount'=>'1000.00','settled_amount'=>'200.00','open_amount'=>'800.00','overdue_amount'=>'150.00','title_count'=>4];}public function grouped(string $kind,int $companyId,string $from,string $to,string $status,?int $partnerId,?int $carrierId,string $groupBy,int $limit,string $dateBasis='due_date'):array{return $groupBy==='carrier'?[['carrier_id'=>15,'carrier_name'=>'Banco','total_amount'=>'1000.00','settled_amount'=>'200.00','open_amount'=>'800.00','overdue_amount'=>'150.00','title_count'=>4]]:[['date'=>'2026-08-01','total_amount'=>'1000.00','settled_amount'=>'200.00','open_amount'=>'800.00','overdue_amount'=>'150.00','title_count'=>4]];}public function titles(string $kind,int $companyId,string $from,string $to,string $status,?int $partnerId,?int $carrierId,int $limit,string $dateBasis='due_date',?array $cursor=null):array{return [];}}
class TestOrderBacklogRepository implements OrderBacklogRepositoryInterface { public function summary(int $c,string $f,string $t,?int $customer,?int $seller,?int $status):array{return ['backlog_total'=>'300.00','order_count'=>3];} public function grouped(int $c,string $f,string $t,string $g,?int $customer,?int $seller,?int $status,int $limit,string $dateBasis='due_date'):array{return $g==='customer'?[['customer_id'=>7,'customer_name'=>'Cliente','backlog_total'=>'200.00','order_count'=>2],['customer_id'=>8,'customer_name'=>'Outro','backlog_total'=>'100.00','order_count'=>1]]:[['date'=>'2026-08-01','backlog_total'=>'300.00','order_count'=>3]];} }

final class TestSchemaTool implements ToolInterface
{
    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.test.schema_fixture', 'Test-only schema fixture.', [
            'type' => 'object',
            'properties' => [
                'term' => ['type' => 'string', 'minLength' => 2, 'maxLength' => 10],
                'page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50],
            ],
            'required' => ['term'],
            'additionalProperties' => false,
        ], ['company:read']);
    }

    public function execute(array $arguments, ExecutionContext $executionContext): ToolExecutionResult
    {
        return new ToolExecutionResult(['content' => [['type' => 'text', 'text' => 'Fixture executed.']], 'structuredContent' => $arguments], 'tools_call_success');
    }
}

class TestAdminStatement extends PDOStatement
{
    private TestAdminPdo $pdo;
    private string $sql;
    /** @var array<string,mixed>|false */ private $row = false;
    private int $column = 0;
    private int $affectedRows = 0;
    public function __construct(TestAdminPdo $pdo, string $sql) { $this->pdo = $pdo; $this->sql = $sql; }
    public function execute($parameters = null) { [$this->row, $this->column, $this->affectedRows] = $this->pdo->executeStatement($this->sql, is_array($parameters) ? $parameters : []); return true; }
    public function fetch($fetchStyle = null, $cursorOrientation = null, $cursorOffset = null) { return $this->row; }
    public function fetchColumn($column = 0) { return $this->column; }
    public function rowCount(): int { return $this->affectedRows; }
}

class TestAdminPdo extends PDO
{
    /** @var array<array{sql:string,params:array<string,mixed>}> */ public array $executed = [];
    private bool $transaction = false;
    private int $lastId = 100;
    public string $connectionStatus = 'active';
    public ?string $connectionPublicId = 'AbCdEfGhIjKlMnOpQrStUvWx';
    public bool $activeApiKey = true;
    public bool $validResponsibleUser = false;
    /** @var string[] */ public array $connectionScopes = ['mcp.read'];
    public function __construct() {}
    public function beginTransaction() { $this->transaction = true; return true; }
    public function commit() { $this->transaction = false; return true; }
    public function rollBack() { $this->transaction = false; return true; }
    public function inTransaction() { return $this->transaction; }
    public function lastInsertId($name = null) { return (string) $this->lastId; }
    public function prepare($statement, $options = []) { return new TestAdminStatement($this, $statement); }
    /** @param array<string,mixed> $params @return array{0:array<string,mixed>|false,1:int,2:int} */
    public function executeStatement(string $sql, array $params): array
    {
        $this->executed[] = ['sql' => $sql, 'params' => $params];
        if (strpos($sql, 'SELECT MCP001_Id FROM MCP001 WHERE MCP001_Public_Id') === 0) { return (($params['public_id'] ?? '') === $this->connectionPublicId && $this->connectionStatus === 'active') ? [['MCP001_Id' => 101], 0, 0] : [false, 0, 0]; }
        if (strpos($sql, 'SELECT MCP001_Id,MCP001_Public_Id FROM MCP001') === 0) { return [['MCP001_Id' => 101, 'MCP001_Public_Id' => $this->connectionPublicId], 0, 0]; }
        if (strpos($sql, 'UPDATE MCP001 SET MCP001_Public_Id=:public_id') === 0) { if ($this->connectionPublicId !== null) { return [false, 0, 0]; } $this->connectionPublicId = (string) ($params['public_id'] ?? ''); return [false, 0, 1]; }
        if (strpos($sql, 'SELECT C004_Id') === 0) { return [['C004_Id' => 1], 0, 0]; }
        if (strpos($sql, 'SELECT MCP001_Id,MCP001_C004_Id') === 0) { return (($params['company'] ?? 1) === 1) ? [['MCP001_Id' => 101, 'MCP001_C004_Id' => 1, 'MCP001_Status' => $this->connectionStatus, 'MCP001_Scopes' => json_encode($this->connectionScopes)], 0, 0] : [false, 0, 0]; }
        if (strpos($sql, 'SELECT 1 FROM MCP001 connection_row') === 0) { return [$this->validResponsibleUser ? ['1' => 1] : false, 0, 0]; }
        if (strpos($sql, 'SELECT 1 FROM MCP002') === 0) { return [$this->activeApiKey ? ['1' => 1] : false, 0, 0]; }
        if (strpos($sql, 'SELECT COUNT(*)') === 0) { return [false, 1, 0]; }
        if (strpos($sql, 'SELECT k.MCP002_Id') === 0) { return [['MCP002_Id' => 201, 'MCP001_Id' => 101, 'MCP001_C004_Id' => 1], 0, 0]; }
        if (strpos($sql, 'INSERT INTO MCP001') === 0) { $this->lastId = 101; }
        if (strpos($sql, 'INSERT INTO MCP002') === 0) { $this->lastId++; }
        return [false, 0, strpos($sql, 'UPDATE MCP002') === 0 ? 1 : 0];
    }
}

class TestBootstrap extends Bootstrap
{
    private bool $failConnection = false;
    private bool $authenticated = true;
    private bool $denyRateLimit = false;
    private ?TestAdminPdo $endpointPdo = null;
    private ?TestRateLimiter $rateLimiter = null;
    /** @var array<array<string,mixed>> */ private array $auditEvents = [];
    /** @var string[] */ private array $scopes = ['mcp.initialize'];
    private int $companyId = 1;
    private int $authenticatedConnectionId = 1;
    private ?TestCompanyRepository $companyRepository = null;
    private ?TestProductRepository $productRepository = null;
    private ?TestPartnerRepository $partnerRepository = null;
    private ?TestQuoteRepository $quoteRepository = null;
    private ?TestSalesSummaryRepository $salesSummaryRepository = null;
    private ?TestOrderBacklogRepository $orderBacklogRepository = null;

    public function failConnection(): void
    {
        $this->failConnection = true;
    }

    public function setAuthenticated(bool $authenticated): void { $this->authenticated = $authenticated; }
    public function setDenyRateLimit(bool $denyRateLimit): void { $this->denyRateLimit = $denyRateLimit; }
    public function setEndpointPdo(TestAdminPdo $endpointPdo): void { $this->endpointPdo = $endpointPdo; }
    /** @param string[] $scopes */ public function setScopes(array $scopes): void { $this->scopes = $scopes; }
    public function setCompanyId(int $companyId): void { $this->companyId = $companyId; }
    public function setAuthenticatedConnectionId(int $connectionId): void { $this->authenticatedConnectionId = $connectionId; }
    public function getCompanyRepository(): TestCompanyRepository { return $this->companyRepository ?? ($this->companyRepository = new TestCompanyRepository()); }
    public function getProductRepository(): TestProductRepository { return $this->productRepository ?? ($this->productRepository = new TestProductRepository()); }
    public function getPartnerRepository(): TestPartnerRepository { return $this->partnerRepository ?? ($this->partnerRepository = new TestPartnerRepository()); }
    public function getQuoteRepository(): TestQuoteRepository { return $this->quoteRepository ?? ($this->quoteRepository = new TestQuoteRepository()); }
    public function getSalesSummaryRepository(): TestSalesSummaryRepository { return $this->salesSummaryRepository ?? ($this->salesSummaryRepository = new TestSalesSummaryRepository()); }
    public function getOrderBacklogRepository(): TestOrderBacklogRepository { return $this->orderBacklogRepository ?? ($this->orderBacklogRepository = new TestOrderBacklogRepository()); }
    /** @return string[] */ public function getRateLimitKeys(): array { return $this->rateLimiter ? $this->rateLimiter->keys : []; }
    /** @return array<array<string,mixed>> */ public function getAuditEvents(): array { return $this->auditEvents; }

    /**
     * @param array<string, mixed> $config
     * @return array<string, string>
     */
    protected function resolveTenant(array $config, string $host): array
    {
        return ['host' => 'cliente.fastcrud.com.br', 'conf_file' => '/var/www/sites/cliente.fastcrud.com.br-confUsuario.php', 'dbDatabase' => 'tenant', 'dbHost' => 'db', 'dbUser' => 'user', 'dbPass' => 'pass', 'urlRaiz' => 'https://cliente.fastcrud.com.br/hardness3'];
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, string> $tenant
     */
    protected function connectTenant(array $config, array $tenant): PDO
    {
        if ($this->endpointPdo !== null) { return $this->endpointPdo; }
        if ($this->failConnection) {
            throw new McpException('Unable to connect to the tenant database.', -32603, null, 500);
        }
        return new TestPdo();
    }

    protected function authenticate(array $config, PDO $pdo, Request $request): ?ExecutionContext
    {
        return $this->authenticated ? new ExecutionContext($this->authenticatedConnectionId, 1, $this->companyId, null, $this->scopes) : null;
    }

    protected function createRateLimitPolicy(array $config): RateLimitPolicy
    {
        $config['rate_limit']['key_pepper'] = 'test-rate-limit-pepper';
        $this->rateLimiter = new TestRateLimiter($this->denyRateLimit);
        return new RateLimitPolicy($this->rateLimiter, $config);
    }

    protected function createJsonRpcServer(array $config, PDO $pdo): JsonRpcServer
    {
        $registry = (new ToolCatalogFactory([new CompanyToolsModule($this->getCompanyRepository(), $this->getPartnerRepository()), new ProductToolsModule($this->getProductRepository(), $this->getProductRepository()), new QuoteToolsModule($this->getQuoteRepository(), null, null, null, $this->getQuoteRepository()), new SalesToolsModule($this->getSalesSummaryRepository()), new FinanceToolsModule(new TestFinanceSummaryRepository()), new OrderToolsModule($this->getOrderBacklogRepository())]))->createRegistry();
        return new JsonRpcServer($config, new ToolExecutor($registry));
    }

    protected function auditRequest(PDO $pdo, string $requestId, string $host, string $ip, string $event, int $httpStatus, int $durationMs, ?ExecutionContext $context, ?string $safeCode = null): void
    {
        $this->auditEvents[] = ['event' => $event, 'status' => $httpStatus, 'context' => $context, 'safe_code' => $safeCode];
    }
}

class UnavailableRateBootstrap extends Bootstrap
{
    /** @param array<string, mixed> $config @return array<string, string> */
    protected function resolveTenant(array $config, string $host): array
    {
        return ['host' => 'cliente.fastcrud.com.br', 'conf_file' => '/var/www/sites/cliente.fastcrud.com.br-confUsuario.php', 'dbDatabase' => 'tenant', 'dbHost' => 'db', 'dbUser' => 'user', 'dbPass' => 'pass', 'urlRaiz' => 'https://cliente.fastcrud.com.br/hardness3'];
    }
}

class SchemaTestBootstrap extends TestBootstrap
{
    protected function createJsonRpcServer(array $config, PDO $pdo): JsonRpcServer
    {
        return new JsonRpcServer($config, new ToolExecutor(new ToolRegistry([new TestSchemaTool()])));
    }
}

class CompositionBootstrap extends Bootstrap
{
    /** @param array<string,mixed> $config */
    public function createServerForTest(array $config, PDO $pdo): JsonRpcServer
    {
        $config['partner'] = ['candidate_ref_pepper' => 'test-candidate-reference-pepper', 'candidate_ref_ttl_seconds' => 600];
        return $this->createJsonRpcServer($config, $pdo);
    }
}

function mcpAssert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

/** @param array<string,mixed> $schema */
function mcpSchemaIsValid(ToolArgumentsValidator $validator, string $json, array $schema): bool
{
    $value = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    $raw = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
    return is_array($value) && $validator->isValid($value, $raw, $schema);
}

/**
 * @return array<string, string>
 */
function mcpHeaders(array $extra = []): array
{
    return array_merge([
        'Accept' => 'application/json, text/event-stream',
        'Content-Type' => 'application/json',
    ], $extra);
}

$originalServer = $_SERVER;
$_SERVER = ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/mcp', 'HTTP_HOST' => 'cliente.fastcrud.com.br', 'CONTENT_TYPE' => 'application/json; charset=utf-8'];
$capturedContentTypeRequest = Request::capture();
$_SERVER = $originalServer;
mcpAssert($capturedContentTypeRequest->getHeader('content-type') === 'application/json; charset=utf-8', 'Request capture must read Apache/FastCGI CONTENT_TYPE');

$config = require dirname(__DIR__) . '/config/app.php';
$config['rate_limit']['key_pepper'] = 'test-rate-limit-pepper';
$config['auth']['credential_peppers']['v1'] = 'test-credential-pepper';
$bootstrap = new TestBootstrap(dirname(__DIR__));
$initializeBody = '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"test","version":"1"}}}';

$generatedKey = (new ApiKeyGenerator())->generate();
$parsedKey = (new ApiKeyParser())->parseAuthorization('Bearer ' . $generatedKey['key']);
mcpAssert($parsedKey !== null && $parsedKey->getPublicId() === $generatedKey['public_id'], 'API key generator and parser must preserve the public id');
mcpAssert((new ApiKeyParser())->parseAuthorization('Bearer malformed') === null, 'malformed API key must be rejected');
$verifier = new ApiKeyVerifier(['v1' => 'test-credential-pepper']);
$hash = $verifier->hash($generatedKey['secret'], 'v1');
mcpAssert($verifier->verify($generatedKey['secret'], $hash, 'v1'), 'correct HMAC must validate');
mcpAssert(!$verifier->verify($generatedKey['secret'] . 'x', $hash, 'v1'), 'incorrect secret must not validate');
try { (new ApiKeyVerifier([]))->hash($generatedKey['secret'], 'v1'); throw new RuntimeException('missing pepper must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Credential pepper is unavailable.', 'missing pepper must fail safely'); }
try { $verifier->verify($generatedKey['secret'], $hash, 'v2'); throw new RuntimeException('unknown pepper version must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Credential pepper is unavailable.', 'unknown pepper version must fail safely'); }
$verifierV2 = new ApiKeyVerifier(['v1' => 'old-pepper', 'v2' => 'current-pepper']);
$legacyHash = $verifierV2->hash($generatedKey['secret'], 'v1');
mcpAssert($verifierV2->verify($generatedKey['secret'], $legacyHash, 'v1'), 'credential created with v1 must remain verifiable after v2 exists');
mcpAssert($verifierV2->hash($generatedKey['secret'], 'v2') !== $legacyHash, 'v2 must be selected explicitly for current work');
$context = new ExecutionContext(1, 2, 3, null, ['mcp.read']);
$scopes = $context->getScopes(); $scopes[] = 'mcp.write';
mcpAssert($context->getScopes() === ['mcp.read'], 'ExecutionContext scopes must not be mutable through accessors');
$resolvedTenantContext = ResolvedTenantContext::fromResolvedTenant(['host' => 'cliente.fastcrud.com.br', 'conf_file' => '/var/www/sites/cliente.fastcrud.com.br-confUsuario.php', 'dbDatabase' => 'tenant', 'dbHost' => '127.0.0.1', 'dbUser' => 'tenant_user', 'dbPass' => 'tenant_pass', 'urlRaiz' => 'https://cliente.fastcrud.com.br/', 'mcpPdfRenderUrl' => 'http://erp-internal/']);
mcpAssert($resolvedTenantContext->getHost() === 'cliente.fastcrud.com.br' && $resolvedTenantContext->getDatabase() === 'tenant', 'resolved tenant context must retain only server-resolved tenant data');
mcpAssert($resolvedTenantContext->getDatabasePassword() === 'tenant_pass', 'resolved tenant context must retain credentials internally for the future legacy bridge');
mcpAssert($resolvedTenantContext->getRootUrl() === 'https://cliente.fastcrud.com.br/' && $resolvedTenantContext->getPdfRenderUrl() === 'http://erp-internal/', 'tenant context must keep distinct public and internal PDF origins');
$legacyAdapter = new LegacyExecutionContextAdapter(new TestLegacyActorResolver(new LegacyActorContext(3, 9, 12, 2, 'America/Sao_Paulo')), dirname(__DIR__));
$legacyContext = $legacyAdapter->adapt(new ExecutionContext(1, 2, 3, 9, ['mcp.write']), $resolvedTenantContext, ['remote_ip' => '203.0.113.10', 'company_id' => 999, 'user_id' => 998]);
mcpAssert($legacyContext->getCompanyId() === 3 && $legacyContext->getUserId() === 9 && $legacyContext->getProfileId() === 12 && $legacyContext->getApprovalLevel() === 2, 'legacy execution context must use only resolved company, user, profile and approval level');
mcpAssert($legacyContext->getCanonicalHost() === 'cliente.fastcrud.com.br' && $legacyContext->getDataPath() === dirname(__DIR__) . '/dados_usuarios/tenant/', 'legacy execution context must derive tenant paths internally');
mcpAssert($legacyContext->isDeveloperMode() === false && $legacyContext->isDeveloperAppMode() === false, 'legacy execution context must always disable developer modes');
$bridgeSource = file_get_contents(dirname(__DIR__) . '/src/Legacy/McpFrameworkBridge.php');
mcpAssert($bridgeSource !== false && strpos($bridgeSource, 'index-framework.php') === false && strpos($bridgeSource, 'confUsuario.php') === false && strpos($bridgeSource, 'session_start(') === false, 'legacy bridge must not load the front controller, tenant configuration, or native session');
mcpAssert(strpos((string) $bridgeSource, "legacy_session_shim.php") < strpos((string) $bridgeSource, "sistema/confSistema.php") && strpos((string) $bridgeSource, "sistema/confSistema.php") < strpos((string) $bridgeSource, "sistema/apps.php") && strpos((string) $bridgeSource, "sistema/apps.php") < strpos((string) $bridgeSource, "bibliotecas/funcoesGerais.php") && strpos((string) $bridgeSource, "bibliotecas/funcoesGerais.php") < strpos((string) $bridgeSource, "bibliotecas/restricoes.php") && strpos((string) $bridgeSource, "bibliotecas/restricoes.php") < strpos((string) $bridgeSource, "bibliotecas/classes/INDEX.php"), 'legacy bridge must load its reviewed dependencies in order');
mcpAssert(strpos((string) $bridgeSource, 'bibliotecas/restricoes.php') !== false && strpos((string) $bridgeSource, 'personalizacoes.php') === false, 'legacy bridge must preload only the proven restriction dependency, never personalization dependencies');
$erpRootPath = dirname(__DIR__, 2);
$bridge = new McpFrameworkBridge($erpRootPath);
$bridgeLegacyContext = new \HardnessMcp\Legacy\LegacyExecutionContext($resolvedTenantContext, $erpRootPath . '/', $erpRootPath . '/dados_usuarios/tenant/', 3, 9, 12, 2, '203.0.113.10', 'America/Sao_Paulo');
try {
    $wrongRootContext = new \HardnessMcp\Legacy\LegacyExecutionContext($resolvedTenantContext, dirname(__DIR__) . '/unexpected/', dirname(__DIR__) . '/unexpected/dados_usuarios/tenant/', 3, 9, 12, 2, '203.0.113.10', 'America/Sao_Paulo');
    $bridge->execute($wrongRootContext, static function (): void {});
    throw new RuntimeException('legacy bridge must reject a context outside its fixed ERP root');
} catch (LegacyBridgeException $exception) {
    mcpAssert($exception->getMessage() === 'Legacy execution is unavailable.', 'legacy bridge failures must not expose configuration or filesystem details');
}
$bridgeState = ['g' => ['sentinel' => true], 'conf' => ['sentinel' => true], 'session' => ['sentinel' => true], 'remote' => $_SERVER['REMOTE_ADDR'] ?? null, 'timezone' => date_default_timezone_get(), 'session_status' => session_status()];
$GLOBALS['g'] = $bridgeState['g'];
$GLOBALS['confUsuario'] = $bridgeState['conf'];
$GLOBALS['_SESSION'] = $bridgeState['session'];
try {
    $bridge->execute($bridgeLegacyContext, static function (): void {});
    throw new RuntimeException('test runtime must not connect with fabricated tenant credentials');
} catch (LegacyBridgeException $exception) {
    mcpAssert($exception->getMessage() === 'Legacy execution is unavailable.', 'legacy bootstrap error must remain protocol-safe');
}
mcpAssert($GLOBALS['g'] === $bridgeState['g'] && $GLOBALS['confUsuario'] === $bridgeState['conf'] && $GLOBALS['_SESSION'] === $bridgeState['session'], 'legacy bridge must restore global and in-memory session state after failure');
mcpAssert(($_SERVER['REMOTE_ADDR'] ?? null) === $bridgeState['remote'] && date_default_timezone_get() === $bridgeState['timezone'] && session_status() === $bridgeState['session_status'], 'legacy bridge must restore request state without opening a native session');
mcpAssert(function_exists('hardness\\session_start') && (new ReflectionFunction('hardness\\session_start'))->isInternal() === false && (new ReflectionFunction('session_start'))->isInternal() === true, 'legacy session shim must shadow only the hardness namespace, not PHP native session_start');
$sessionStatusBeforeShim = session_status();
$sessionCookieBeforeShim = $_COOKIE[session_name()] ?? null;
\hardness\session_start();
mcpAssert(session_status() === $sessionStatusBeforeShim && ($_COOKIE[session_name()] ?? null) === $sessionCookieBeforeShim, 'namespaced session shim must not activate PHP sessions or emit a session cookie');
$GLOBALS['confUsuario'] = ['dbDatabase' => 'tenant'];
$GLOBALS['g'] = ['pathRaiz' => dirname(__DIR__, 2) . '/', 'pathDados' => 'dados_usuarios/tenant/', 'empresaAtual' => 3, 'usuarioAtual' => 9, 'perfilUsuario' => 12, 'nivelAlcadaUsuarioSistema' => 2, 'modoDesenvolvedor' => false, 'modoDesenvolvedorApp' => false];
require_once dirname(__DIR__, 2) . '/bibliotecas/classes/MCP001.php';
$mcp001Pilot = (new \hardness\MCP001())->mcp001ContextPilot();
mcpAssert($mcp001Pilot === ['implementation' => 'standard', 'company_id' => 3, 'user_id' => 9, 'profile_id' => 12, 'approval_level' => 2], 'MCP001 standard pilot must expose only its fixed safe context shape');
mcpAssert(strpos((string) file_get_contents(dirname(__DIR__, 2) . '/bibliotecas/classes/MCP001.php'), "definirClasse('MCP001')") !== false, 'MCP001 must use the legacy class-resolution mechanism');
foreach ([
    [new ExecutionContext(1, 2, 3, null, ['mcp.write']), new TestLegacyActorResolver(new LegacyActorContext(3, 9, 12, 2, 'UTC')), ['remote_ip' => '203.0.113.10']],
    [new ExecutionContext(1, 2, 3, 9, ['mcp.write']), new TestLegacyActorResolver(null), ['remote_ip' => '203.0.113.10']],
    [new ExecutionContext(1, 2, 3, 9, ['mcp.write']), new TestLegacyActorResolver(new LegacyActorContext(4, 9, 12, 2, 'UTC')), ['remote_ip' => '203.0.113.10']],
    [new ExecutionContext(1, 2, 3, 9, ['mcp.write']), new TestLegacyActorResolver(new LegacyActorContext(3, 9, 12, 2, 'UTC')), ['remote_ip' => 'not-an-ip']],
] as [$testExecutionContext, $testActorResolver, $testMetadata]) {
    try {
        (new LegacyExecutionContextAdapter($testActorResolver, dirname(__DIR__)))->adapt($testExecutionContext, $resolvedTenantContext, $testMetadata);
        throw new RuntimeException('invalid legacy context inputs must fail closed');
    } catch (LegacyContextResolutionException $exception) {
        mcpAssert($exception->getMessage() === 'Unable to prepare legacy execution context.', 'legacy context failure must use a safe message');
    }
}
foreach ([[3, 9, 0, 2], [3, 9, 12, 0]] as [$invalidCompanyId, $invalidUserId, $invalidProfileId, $invalidApprovalLevel]) {
    try {
        new LegacyActorContext($invalidCompanyId, $invalidUserId, $invalidProfileId, $invalidApprovalLevel, 'UTC');
        throw new RuntimeException('unresolved profile or approval level must fail closed');
    } catch (LegacyContextResolutionException $exception) {
        mcpAssert($exception->getMessage() === 'Unable to prepare legacy execution context.', 'invalid resolved actor data must use a safe message');
    }
}

$validator = new ToolArgumentsValidator();
$validationSchema = [
    'type' => 'object',
    'properties' => [
        'name' => ['type' => 'string', 'minLength' => 2, 'maxLength' => 5],
        'kind' => ['type' => 'string', 'enum' => ['retail', 'wholesale']],
        'quantity' => ['type' => 'integer', 'minimum' => 1, 'exclusiveMaximum' => 10],
        'price' => ['type' => 'number', 'exclusiveMinimum' => 0, 'maximum' => 999.99],
        'active' => ['type' => 'boolean'],
        'note' => ['type' => 'null'],
        'tags' => ['type' => 'array', 'items' => ['type' => 'string', 'minLength' => 1], 'minItems' => 1, 'maxItems' => 2],
        'filter' => ['type' => 'object', 'properties' => ['code' => ['type' => 'string', 'minLength' => 1]], 'required' => ['code'], 'additionalProperties' => false],
    ],
    'required' => ['name', 'kind', 'quantity', 'price', 'active', 'note', 'tags', 'filter'],
    'additionalProperties' => false,
];
mcpAssert(mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must accept a valid nested argument object');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana"}', $validationSchema), 'validator must reject a missing required field');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"},"unknown":1}', $validationSchema), 'validator must reject unknown properties');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"A","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must enforce string minLength');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Longer","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must enforce string maxLength');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"other","quantity":2,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must enforce string enum');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2.5,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must distinguish integer from number');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":10,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must enforce exclusive numeric bounds');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":0,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must enforce exclusive minimum');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":[],"filter":{"code":"A"}}', $validationSchema), 'validator must enforce array minItems');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":["a","b","c"],"filter":{"code":"A"}}', $validationSchema), 'validator must enforce array maxItems');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":[1],"filter":{"code":"A"}}', $validationSchema), 'validator must validate array elements');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A","extra":true}}', $validationSchema), 'validator must validate nested objects');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":"not-null","tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must apply null strictly');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":"new","filter":{"code":"A"}}', $validationSchema), 'validator must reject a non-array value');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"Ana","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', ['type' => 'object', 'pattern' => '.*']), 'validator must reject unsupported schema keywords');
mcpAssert(!mcpSchemaIsValid($validator, '{"name":"' . str_repeat('x', 8193) . '","kind":"retail","quantity":2,"price":12.5,"active":true,"note":null,"tags":["new"],"filter":{"code":"A"}}', $validationSchema), 'validator must reject excessive strings');
$largeArraySchema = ['type' => 'array', 'items' => ['type' => 'string']];
mcpAssert(!mcpSchemaIsValid($validator, json_encode(array_fill(0, 101, 'x')), $largeArraySchema), 'validator must reject excessive arrays');
$deepSchema = ['type' => 'string'];
$deepValue = 'x';
$deepRaw = 'x';
for ($depth = 0; $depth < 8; $depth++) {
    $deepSchema = ['type' => 'object', 'properties' => ['child' => $deepSchema], 'required' => ['child'], 'additionalProperties' => false];
    $rawObject = new stdClass(); $rawObject->child = $deepRaw;
    $deepRaw = $rawObject; $deepValue = ['child' => $deepValue];
}
mcpAssert(!$validator->isValid($deepValue, $deepRaw, $deepSchema), 'validator must reject structures beyond the maximum depth');
try { new ApcuRateLimiter(); if (!extension_loaded('apcu') || !apcu_enabled()) { throw new RuntimeException('unavailable APCu must fail'); } } catch (RateLimiterUnavailableException $exception) { mcpAssert(!extension_loaded('apcu') || !function_exists('apcu_enabled') || !apcu_enabled(), 'APCu unavailability must fail closed'); }

$adminPdo = new TestAdminPdo();
$endpointPdo = new TestAdminPdo();
$endpointResolver = new McpEndpointResolver($endpointPdo);
mcpAssert($endpointResolver->resolve('/mcp') === null, 'the legacy MCP path must remain resolved without a connection-specific identifier.');
mcpAssert($endpointResolver->resolve('/mcp/c/' . $endpointPdo->connectionPublicId) === 101, 'a valid active MCP connection public identifier must resolve to its connection.');
foreach (['/mcp/c/short', '/mcp/c/AbCdEfGhIjKlMnOpQrStUvW!', '/mcp/c/' . $endpointPdo->connectionPublicId . '/extra'] as $malformedEndpointPath) {
    try { $endpointResolver->resolve($malformedEndpointPath); throw new RuntimeException('malformed connection endpoint must fail'); } catch (\HardnessMcp\Core\HttpException $exception) { mcpAssert($exception->getHttpStatus() === 404, 'malformed connection public identifiers must return 404.'); }
}
try { $endpointResolver->resolve('/mcp/c/ZyXwVuTsRqPoNmLkJiHgFeDc'); throw new RuntimeException('unknown connection endpoint must fail'); } catch (\HardnessMcp\Core\HttpException $exception) { mcpAssert($exception->getHttpStatus() === 404, 'unknown connection public identifiers must return 404.'); }
$endpointPdo->connectionStatus = 'disabled';
try { $endpointResolver->resolve('/mcp/c/' . $endpointPdo->connectionPublicId); throw new RuntimeException('disabled connection endpoint must fail'); } catch (\HardnessMcp\Core\HttpException $exception) { mcpAssert($exception->getHttpStatus() === 404, 'disabled connection public identifiers must return 404.'); }
$admin = new McpConnectionAdminService($adminPdo, $config, 'admin.test', '127.0.0.1', 77);
$pendingEndpointPdo = new TestAdminPdo();
$pendingEndpointBootstrap = new TestBootstrap(dirname(__DIR__));
$pendingEndpointBootstrap->setEndpointPdo($pendingEndpointPdo);
$pendingEndpointBootstrap->setAuthenticatedConnectionId(101);
$response = $pendingEndpointBootstrap->handle(new Request('POST', '/mcp/c/' . $pendingEndpointPdo->connectionPublicId, 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders()), $config);
$pendingEndpointBootstrap->setScopes(["company:read"]);
$exclusiveToolResponse = $pendingEndpointBootstrap->handle(new Request("POST", "/mcp/c/" . $pendingEndpointPdo->connectionPublicId, "cliente.fastcrud.com.br", "{\"jsonrpc\":\"2.0\",\"id\":21,\"method\":\"tools/call\",\"params\":{\"name\":\"hardness.company.get_current\",\"arguments\":{}}}", mcpHeaders(["MCP-Protocol-Version" => "2025-11-25"])), $config);
mcpAssert(($exclusiveToolResponse->getPayload()["result"]["structuredContent"]["id"] ?? null) === 1 && $pendingEndpointBootstrap->getCompanyRepository()->lastCompanyId === 1, "Tools on a connection-specific URL must receive only the authenticated connection company.");
mcpAssert($response->getStatus() === 200, 'a connection-specific MCP URL must become available only to its own authenticated connection.');
$wrongConnectionBootstrap = new TestBootstrap(dirname(__DIR__));
$wrongConnectionBootstrap->setEndpointPdo($pendingEndpointPdo);
$wrongConnectionBootstrap->setAuthenticatedConnectionId(202);
$wrongConnectionResponse = $wrongConnectionBootstrap->handle(new Request('POST', '/mcp/c/' . $pendingEndpointPdo->connectionPublicId, 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders()), $config);
mcpAssert($wrongConnectionResponse->getStatus() === 401, 'an API-key execution context from connection B must be rejected at connection A URL.');
$oauthRedirectAdmin = new OAuthAdminService(new TestAdminPdo(), 'admin.test', '127.0.0.1', 77);
$oauthRedirectValidator = new ReflectionMethod(OAuthAdminService::class, 'redirectUri');
$oauthRedirectValidator->setAccessible(true);
foreach (['https://client.example/callback', 'http://127.0.0.1:33418', 'http://[::1]:33418/callback'] as $validPublicRedirect) {
    mcpAssert($oauthRedirectValidator->invoke($oauthRedirectAdmin, $validPublicRedirect) === $validPublicRedirect, 'pre-registered public clients must accept HTTPS and explicit loopback redirects');
}
foreach (['http://localhost:33418', 'http://127.0.0.1', 'http://[::1]', 'http://127.0.0.2:33418', 'http://10.0.0.1:33418', 'com.example.app:/callback', 'http://127.0.0.1:33418/*'] as $invalidPublicRedirect) {
    try {
        $oauthRedirectValidator->invoke($oauthRedirectAdmin, $invalidPublicRedirect);
        throw new RuntimeException('unsafe pre-registered redirect must fail');
    } catch (RuntimeException $exception) {
        mcpAssert($exception->getMessage() === 'Invalid redirect URI.', 'pre-registered public client redirects must reject unsafe hosts and schemes');
    }
}
$dcrMetadataPdo = new TestAdminPdo();
$dcrMetadata = (new DynamicClientRegistrationService($dcrMetadataPdo, 'admin.test', '127.0.0.1'))->register([
    'client_name' => 'Metadata test', 'redirect_uris' => ['https://client.example/callback'], 'grant_types' => ['authorization_code', 'refresh_token'], 'response_types' => ['code'], 'token_endpoint_auth_method' => 'none',
    'client_uri' => 'https://client.example', 'logo_uri' => 'https://client.example/logo.png', 'tos_uri' => 'https://client.example/terms', 'policy_uri' => 'https://client.example/privacy', 'contacts' => ['support@example.test'], 'software_id' => 'client-software', 'software_version' => '1.0',
]);
mcpAssert(!isset($dcrMetadata['logo_uri'], $dcrMetadata['client_uri'], $dcrMetadata['contacts'], $dcrMetadata['software_id']), 'standard descriptive DCR metadata must be discarded rather than persisted or returned');
try { (new DynamicClientRegistrationService(new TestAdminPdo(), 'admin.test', '127.0.0.1'))->register(['client_name' => 'Metadata test', 'redirect_uris' => ['https://client.example/callback'], 'unknown_metadata' => 'value']); throw new RuntimeException('unknown DCR metadata must fail'); } catch (DcrValidationException $exception) { mcpAssert($exception->field === 'unknown_metadata', 'non-standard DCR metadata must remain rejected'); }
$dcrDiagnostic = DcrDiagnostic::summarize(['client_name' => 'No log', 'grant_types' => ['authorization_code'], 'response_types' => ['code'], 'token_endpoint_auth_method' => 'none', 'redirect_uris' => ['https://chatgpt.com/connector/oauth/callback?never=log'], 'scope' => 'company:read quote:write']);
mcpAssert(($dcrDiagnostic['fields'] ?? null) === ['client_name', 'grant_types', 'redirect_uris', 'response_types', 'scope', 'token_endpoint_auth_method'], 'DCR diagnostics must record metadata field names only.');
mcpAssert(($dcrDiagnostic['grant_types']['values'][0]['value'] ?? null) === 'authorization_code' && ($dcrDiagnostic['response_types']['values'][0]['value'] ?? null) === 'code' && ($dcrDiagnostic['token_endpoint_auth_method']['value'] ?? null) === 'none', 'DCR diagnostics must retain safe OAuth compatibility values.');
mcpAssert(($dcrDiagnostic['redirect_uris']['count'] ?? null) === 1 && ($dcrDiagnostic['redirect_uris']['origins'] ?? null) === ['https://chatgpt.com'], 'DCR diagnostics must record redirect origins but not paths or query strings.');
mcpAssert(($dcrDiagnostic['scopes']['values'][1]['value'] ?? null) === 'quote:write' && strpos((string) json_encode($dcrDiagnostic), 'never=log') === false, 'DCR diagnostics must retain scopes but redact redirect URI paths and queries.');
$unknownDcrDiagnostic = DcrDiagnostic::summarize(['grant_types' => ['not-a-secret-but-do-not-log'], 'scope' => 'unknown-scope']);
mcpAssert(isset($unknownDcrDiagnostic['grant_types']['values'][0]['value_sha256'], $unknownDcrDiagnostic['scopes']['values'][0]['value_sha256']) && strpos((string) json_encode($unknownDcrDiagnostic), 'not-a-secret-but-do-not-log') === false && strpos((string) json_encode($unknownDcrDiagnostic), 'unknown-scope') === false, 'DCR diagnostics must hash unexpected metadata values instead of logging them.');
$dcrResponseDiagnostic = DcrDiagnostic::summarizeSuccessfulResponse(['client_id' => 'never-log-client-id', 'client_id_issued_at' => 123, 'client_name' => 'Never log this name', 'redirect_uris' => ['https://chatgpt.com/connector/oauth/callback?never=log'], 'response_types' => ['code'], 'grant_types' => ['authorization_code', 'refresh_token'], 'token_endpoint_auth_method' => 'none']);
mcpAssert(($dcrResponseDiagnostic['response_contract'] ?? null) === 'dcr_public_code_refresh_without_scope_v1' && ($dcrResponseDiagnostic['contract_valid'] ?? null) === true && ($dcrResponseDiagnostic['field_count'] ?? null) === 7 && ($dcrResponseDiagnostic['json_serialization_ok'] ?? null) === true, 'DCR response diagnostics must validate the expected public-client response contract before serialization.');
mcpAssert(($dcrResponseDiagnostic['client_id_type'] ?? null) === 'string' && ($dcrResponseDiagnostic['client_id_issued_at_type'] ?? null) === 'integer' && ($dcrResponseDiagnostic['redacted_registered_metadata_count'] ?? null) === 1 && ($dcrResponseDiagnostic['redirect_uris']['origins'] ?? null) === ['https://chatgpt.com'] && ($dcrResponseDiagnostic['scope_present'] ?? null) === false && ($dcrResponseDiagnostic['unexpected_field_count'] ?? null) === 0, 'DCR response diagnostics must retain only the permitted structural evidence.');
mcpAssert(strpos((string) json_encode($dcrResponseDiagnostic), 'never-log-client-id') === false && strpos((string) json_encode($dcrResponseDiagnostic), 'Never log this name') === false && strpos((string) json_encode($dcrResponseDiagnostic), 'never=log') === false, 'DCR response diagnostics must not record client identifiers, client names, or redirect URI paths and queries.');
$scopeCatalog = new ScopeCatalog();
$catalogScopes = $scopeCatalog->all();
mcpAssert(array_column($catalogScopes, 'id') === ['company:read', 'product:prices:history', 'product:prices:export', 'product:prices:reverse', 'partner:search', 'partner:lookup', 'product:prices:apply', 'product:prices:prepare', 'product:read', 'product:partner-reference:lookup', 'quote:read', 'quote:report:read', 'quote:item-report:read', 'sales:read', 'sales:item-report:read', 'order:report:read', 'order:item-report:read', 'product:conversion-report:read', 'commercial:opportunities:read', 'finance:receivables:read', 'finance:payables:read', 'quote:write', 'quote:item:add', 'quote:item:update', 'quote:item:delete', 'ide_smt:quote:order:generate'], 'scope catalog must expose only scopes backed by registered Tools');
$quoteCreateGateway = new TestQuoteCreateGateway();
$quotePdfGateway = new TestQuotePdfGateway();
$catalogProductRepository = new TestProductRepository();
$catalogQuoteRepository = new TestQuoteRepository();
$companyCatalog = (new ToolCatalogFactory([new CompanyToolsModule(new TestCompanyRepository(), new TestPartnerRepository()), new ProductToolsModule($catalogProductRepository, $catalogProductRepository), new QuoteToolsModule($catalogQuoteRepository, $quoteCreateGateway, $quotePdfGateway, null, $catalogQuoteRepository), new SalesToolsModule(new TestSalesSummaryRepository()), new FinanceToolsModule(new TestFinanceSummaryRepository()), new OrderToolsModule(new TestOrderBacklogRepository()), ItemReportToolsModule::fromPdo(new TestPdo()), CommercialToolsModule::fromPdo(new TestPdo()), new IdeSmtToolsModule(new TestIdeSmtGenerateOrderGateway(), null), new SystemToolsModule(new TestContextPilotGateway())]))->createRegistry();
mcpAssert(array_map(static function (ToolInterface $tool): string { return $tool->getDefinition()->getName(); }, $companyCatalog->all()) === ['hardness.company.get_current', 'hardness.partner.search', 'hardness.partner.get', 'hardness.partner.get_by_document', 'hardness.product.search', 'hardness.product.get_by_partner_reference', 'hardness.product.prices.prepare', 'hardness.product.prices.apply', 'hardness.product.prices.history', 'hardness.product.prices.export', 'hardness.product.prices.reversal_prepare', 'hardness.product.prices.reversal_apply', 'hardness.quote.search', 'hardness.quote.get_by_id', 'hardness.quote.summary', 'hardness.quote.create', 'hardness.quote.generate_pdf_link', 'hardness.quote_item.add', 'hardness.quote_item.update', 'hardness.quote_item.delete', 'hardness.sales.summary', 'hardness.finance.receivables_summary', 'hardness.finance.payables_summary', 'hardness.finance.cash_flow_summary', 'hardness.order.summary', 'hardness.quote.item_summary', 'hardness.order.item_summary', 'hardness.sales.item_summary', 'hardness.product.conversion_summary', 'hardness.commercial.opportunities', 'hardness.ide_smt.quote.generate_order_by_destination', 'hardness.system.context_pilot'], 'explicit modules must register the approved public Tool catalog');
(new ToolCatalogConsistencyValidator())->assertConsistent($companyCatalog, $scopeCatalog);
$publicCatalogServer = new JsonRpcServer($config, new ToolExecutor($companyCatalog));
$publicCatalogList = $publicCatalogServer->handle('{"jsonrpc":"2.0","id":89,"method":"tools/list"}', '2025-11-25', new ExecutionContext(7, 8, 3, 9, ['company:read', 'partner:search', 'partner:lookup', 'product:read', 'product:partner-reference:lookup', 'product:prices:prepare', 'product:prices:apply', 'product:prices:history', 'product:prices:export', 'product:prices:reverse', 'quote:read', 'quote:item-report:read', 'quote:report:read', 'quote:write', 'sales:read', 'order:report:read', 'order:item-report:read', 'sales:item-report:read', 'product:conversion-report:read', 'commercial:opportunities:read'], 'api_key', null, null, null, null, true));
$publicCatalogNames = array_column($publicCatalogList->getPayload()['result']['tools'] ?? [], 'name');
mcpAssert($publicCatalogNames === ['hardness_company_get_current', 'hardness_partner_search', 'hardness_partner_get', 'hardness_partner_get_by_document', 'hardness_product_search', 'hardness_product_get_by_partner_reference', 'hardness_product_prices_prepare', 'hardness_product_prices_apply', 'hardness_product_prices_history', 'hardness_product_prices_export', 'hardness_product_prices_reversal_prepare', 'hardness_product_prices_reversal_apply', 'hardness_quote_search', 'hardness_quote_get_by_id', 'hardness_quote_summary', 'hardness_quote_create', 'hardness_quote_generate_pdf_link', 'hardness_sales_summary', 'hardness_order_summary', 'hardness_quote_item_summary', 'hardness_order_item_summary', 'hardness_sales_item_summary', 'hardness_product_conversion_summary', 'hardness_commercial_opportunities', 'hardness_system_context_pilot'], 'tools/list must preserve catalog order and expose only normalized public Tool names');
foreach ($publicCatalogNames as $publicCatalogName) {
    mcpAssert(preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $publicCatalogName) === 1 && strpos($publicCatalogName, '.') === false, 'every public Tool name must contain only letters, numbers, underscores, or hyphens');
}
$quoteCreateRegistry = (new ToolCatalogFactory([new QuoteToolsModule(new TestQuoteRepository(), $quoteCreateGateway, $quotePdfGateway)]))->createRegistry();
$quoteCreateServer = new JsonRpcServer($config, new ToolExecutor($quoteCreateRegistry));
$quoteCreateContext = new ExecutionContext(7, 8, 1, 9, ['quote:write'], 'api_key', null, null, null, '203.0.113.10', true);
$quoteCreateList = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":88,"method":"tools/list"}', '2025-11-25', $quoteCreateContext);
mcpAssert(array_column($quoteCreateList->getPayload()['result']['tools'] ?? [], 'name') === ['hardness_quote_create', 'hardness_quote_generate_pdf_link'], 'tools/list must expose the quote write Tools');
$quoteCreateCall = '{"jsonrpc":"2.0","id":93,"method":"tools/call","params":{"name":"hardness_quote_create","arguments":{"customer_id":12,"items":[{"product_id":34,"quantity":2},{"product_id":35,"quantity":0.5}]}}}';
$quoteCreateResponse = $quoteCreateServer->handle($quoteCreateCall, '2025-11-25', $quoteCreateContext);
$quoteCreatePayload = $quoteCreateResponse->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($quoteCreatePayload['success'] ?? false) === true && ($quoteCreatePayload['quote_id'] ?? null) === 77 && ($quoteCreatePayload['items_count'] ?? null) === 2 && ($quoteCreatePayload['customer_id'] ?? null) === 12 && ($quoteCreatePayload['status'] ?? null) === 'draft' && ($quoteCreatePayload['items'] ?? null) === [['index' => 1, 'product_id' => 34, 'quantity' => 2], ['index' => 2, 'product_id' => 35, 'quantity' => 0.5]] && $quoteCreateGateway->arguments === ['customer_id' => 12, 'items' => [['product_id' => 34, 'quantity' => 2], ['product_id' => 35, 'quantity' => 0.5]]] && $quoteCreateGateway->context === $quoteCreateContext, 'quote create must confirm the committed draft and accepted items while passing only validated data to its fixed gateway');
$quoteCreateDefinition = null;
foreach (($quoteCreateList->getPayload()['result']['tools'] ?? []) as $definition) if (($definition['name'] ?? null) === 'hardness_quote_create') { $quoteCreateDefinition = $definition; break; }
mcpAssert(is_array($quoteCreateDefinition) && strpos((string) ($quoteCreateDefinition['description'] ?? ''), 'never ask for it merely because it was omitted') !== false && !in_array('location_code', $quoteCreateDefinition['inputSchema']['properties']['items']['items']['required'] ?? [], true), 'quote create must publish ERP Ambiente as optional and must prohibit asking for it merely because it is absent');
$quoteCreateWithLocation = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":193,"method":"tools/call","params":{"name":"hardness_quote_create","arguments":{"customer_id":12,"items":[{"product_id":34,"quantity":2,"location_code":"Cozinha"}]}}}', '2025-11-25', $quoteCreateContext);
$quoteCreateWithLocationPayload = $quoteCreateWithLocation->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($quoteCreateGateway->arguments['items'][0]['location_code'] ?? null) === 'Cozinha' && ($quoteCreateWithLocationPayload['items'][0]['location_code'] ?? null) === 'Cozinha', 'quote create must accept and confirm an explicitly supplied optional item location');
$quoteCreateLongLocation = $quoteCreateServer->handle(json_encode(['jsonrpc' => '2.0', 'id' => 194, 'method' => 'tools/call', 'params' => ['name' => 'hardness_quote_create', 'arguments' => ['customer_id' => 12, 'items' => [['product_id' => 34, 'quantity' => 2, 'location_code' => str_repeat('A', 61)]]]]]), '2025-11-25', $quoteCreateContext);
mcpAssert(($quoteCreateLongLocation->getPayload()['error']['code'] ?? null) === -32602, 'quote create must reject an item location longer than the ERP 60-character column');
$quotePdfCall = '{"jsonrpc":"2.0","id":96,"method":"tools/call","params":{"name":"hardness_quote_generate_pdf_link","arguments":{"quote_id":50}}}';
$quotePdfResponse = $quoteCreateServer->handle($quotePdfCall, '2025-11-25', $quoteCreateContext);
$quotePdfPayload = $quotePdfResponse->getPayload()['result']['structuredContent'] ?? [];
mcpAssert($quotePdfPayload === ['quote_id' => 50, 'pdf_url' => 'https://cliente.fastcrud.com.br/hardness3/dados_usuarios/tenant/tmp/orcamento-50.pdf'] && $quotePdfGateway->quoteId === 50 && $quotePdfGateway->context === $quoteCreateContext, 'quote PDF tool must generate a link only for the quote and execution context received');
$quotePdfNotFound = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":97,"method":"tools/call","params":{"name":"hardness_quote_generate_pdf_link","arguments":{"quote_id":999}}}', '2025-11-25', $quoteCreateContext);
mcpAssert(($quotePdfNotFound->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'quote_not_found', 'quote PDF tool must not generate a link for an out-of-scope or missing quote');
$quoteCreateInvalid = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":94,"method":"tools/call","params":{"name":"hardness.quote.create","arguments":{"customer_id":12,"items":[{"product_id":34,"quantity":2,"company_id":99}]}}}', '2025-11-25', $quoteCreateContext);
mcpAssert(($quoteCreateInvalid->getPayload()['error']['code'] ?? null) === -32602, 'quote create schema must reject identity or infrastructure fields on items');
$quoteCreateIdentity = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":95,"method":"tools/call","params":{"name":"hardness.quote.create","arguments":{"customer_id":12,"company_id":99,"items":[{"product_id":34,"quantity":2}]}}}', '2025-11-25', $quoteCreateContext);
mcpAssert(($quoteCreateIdentity->getPayload()['error']['code'] ?? null) === -32602, 'quote create schema must reject company, user, tenant, and infrastructure arguments');
$quoteCreateDenied = $quoteCreateServer->handle($quoteCreateCall, '2025-11-25', new ExecutionContext(7, 8, 3, 9, ['quote:read']));
mcpAssert(($quoteCreateDenied->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'scope_denied', 'quote create must require quote:write');
$quoteCreateNoWriteContext = new ExecutionContext(7, 8, 3, 9, ['quote:read']);
$quoteCreateHidden = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":96,"method":"tools/list"}', '2025-11-25', $quoteCreateNoWriteContext);
mcpAssert(!in_array('hardness_quote_create', array_column($quoteCreateHidden->getPayload()['result']['tools'] ?? [], 'name'), true), 'quote create must not be listed without quote:write');
$quoteCreateNoActor = new ExecutionContext(7, 8, 3, null, ['quote:write'], 'api_key', null, null, null, '203.0.113.10', false);
$quoteCreateNoActorList = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":196,"method":"tools/list"}', '2025-11-25', $quoteCreateNoActor);
mcpAssert(!in_array('hardness_quote_create', array_column($quoteCreateNoActorList->getPayload()['result']['tools'] ?? [], 'name'), true), 'API key without a valid ERP actor must not list quote create');
$quoteCreateNoActorCall = $quoteCreateServer->handle($quoteCreateCall, '2025-11-25', $quoteCreateNoActor);
mcpAssert(($quoteCreateNoActorCall->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'erp_actor_required', 'direct quote create calls must reject API keys without a valid ERP actor');
$quoteCreateOAuthActor = new ExecutionContext(7, 8, 3, 91, ['quote:write'], 'oauth', 4, 5, 6, '203.0.113.10');
$quoteCreateOAuthList = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":197,"method":"tools/list"}', '2025-11-25', $quoteCreateOAuthActor);
mcpAssert(in_array('hardness_quote_create', array_column($quoteCreateOAuthList->getPayload()['result']['tools'] ?? [], 'name'), true), 'OAuth actor availability must not depend on the API-key actor flag');
$apiKeyAuditPdo = new TestAdminPdo();
(new McpAuditService($apiKeyAuditPdo))->record('request', 'tenant.test', '203.0.113.10', 'tools_call_success', 200, 1, $quoteCreateContext, 'hardness_quote_create');
$apiKeyAudit = end($apiKeyAuditPdo->executed);
mcpAssert(strpos($apiKeyAudit['sql'], "MCP003_Auth_Method") !== false && ($apiKeyAudit['params']['user_id'] ?? null) === 9 && ($apiKeyAudit['params']['credential_id'] ?? null) === 8, 'API-key audit must record its auth method, credential, and effective responsible user');
$quoteCreateGateway->arguments = null;
$quoteCreateAliasDenied = $quoteCreateServer->handle('{"jsonrpc":"2.0","id":97,"method":"tools/call","params":{"name":"hardness.quote.create","arguments":{"customer_id":12,"items":[{"product_id":34,"quantity":2}]}}}', '2025-11-25', $quoteCreateNoWriteContext);
mcpAssert(($quoteCreateAliasDenied->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'scope_denied' && $quoteCreateGateway->arguments === null, 'the dotted quote create alias must receive the same quote:write check before gateway execution');
$quoteCreateGateway->fail = true;
$quoteCreateFailure = $quoteCreateServer->handle($quoteCreateCall, '2025-11-25', $quoteCreateContext);
mcpAssert(($quoteCreateFailure->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'quote_creation_failed' && strpos(json_encode($quoteCreateFailure->getPayload()), 'sql/path') === false, 'quote create legacy failure must remain functional and safe');
$quoteCreateGateway->failCode = 'quote_header_insert_failed';
$quoteCreateAuditedFailure = $quoteCreateServer->handle($quoteCreateCall, '2025-11-25', $quoteCreateContext);
mcpAssert(($quoteCreateAuditedFailure->getAuditSafeCode() ?? null) === 'quote_header_insert_failed' && ($quoteCreateAuditedFailure->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'quote_creation_failed', 'quote create must keep the client error generic while exposing an allow-listed internal audit code');
$quoteCreateGateway->failCode = 'product_services_not_supported';
$quoteCreateServicesFailure = $quoteCreateServer->handle($quoteCreateCall, '2025-11-25', $quoteCreateContext);
mcpAssert(($quoteCreateServicesFailure->getAuditSafeCode() ?? null) === 'product_services_not_supported' && ($quoteCreateServicesFailure->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'quote_creation_failed' && strpos((string) ($quoteCreateServicesFailure->getPayload()['result']['structuredContent']['error']['message'] ?? ''), 'serviços associados') !== false, 'product services must produce a safe, explanatory quote-create error without changing its public code');
$quoteCreateGateway->failCode = null;
$quoteCreateGateway->fail = false;
$quoteCreateItemFailureTool = new \HardnessMcp\Tools\Quote\Create\QuoteCreateTool(new class implements QuoteCreateGatewayInterface {
    public function create(array $arguments, ExecutionContext $context): array { throw new LegacyBridgeException('quote_item_creation_failed', ['item_index' => 2, 'product_id' => 117752]); }
});
$quoteCreateItemFailure = $quoteCreateItemFailureTool->execute(['customer_id' => 12, 'items' => [['product_id' => 117751, 'quantity' => 1], ['product_id' => 117752, 'quantity' => 1]]], $quoteCreateContext);
$quoteCreateItemFailureResult = $quoteCreateItemFailure->getResult();
mcpAssert(($quoteCreateItemFailure->getAuditSafeCode() ?? null) === 'quote_item_creation_failed' && ($quoteCreateItemFailureResult['structuredContent']['error']['failed_item'] ?? null) === ['index' => 2, 'product_id' => 117752] && strpos((string) ($quoteCreateItemFailureResult['structuredContent']['error']['message'] ?? ''), 'item 2 (produto 117752)') !== false, 'quote item failures must identify the safe client-supplied item that blocked the transaction');
$mcp001Source = file_get_contents(dirname(__DIR__, 2) . '/bibliotecas/classes/MCP001.php');
mcpAssert($mcp001Source !== false && strpos($mcp001Source, 'mcp001ProdutoPossuiServicos') < strpos($mcp001Source, 'incluirCabecalhoOrcamento'), 'MCP001 must validate product services before the first quote-header write');
$contextPilotGateway = new TestContextPilotGateway();
$contextPilotRegistry = (new ToolCatalogFactory([new SystemToolsModule($contextPilotGateway)]))->createRegistry();
$contextPilotServer = new JsonRpcServer($config, new ToolExecutor($contextPilotRegistry));
$contextPilotContext = new ExecutionContext(7, 8, 3, 9, ['company:read'], 'api_key', null, null, null, '203.0.113.10');
$contextPilotList = $contextPilotServer->handle('{"jsonrpc":"2.0","id":90,"method":"tools/list"}', '2025-11-25', $contextPilotContext);
$contextPilotDefinition = $contextPilotList->getPayload()['result']['tools'][0] ?? [];
mcpAssert(($contextPilotDefinition['name'] ?? null) === 'hardness_system_context_pilot' && ($contextPilotDefinition['inputSchema']['additionalProperties'] ?? null) === false && ($contextPilotDefinition['inputSchema']['properties'] ?? null) instanceof stdClass, 'context pilot must be listed with an empty public schema');
$contextPilotCall = '{"jsonrpc":"2.0","id":91,"method":"tools/call","params":{"name":"hardness_system_context_pilot","arguments":{}}}';
$contextPilotResponse = $contextPilotServer->handle($contextPilotCall, '2025-11-25', $contextPilotContext);
$contextPilotStructured = $contextPilotResponse->getPayload()['result']['structuredContent'] ?? [];
mcpAssert($contextPilotStructured === ['pilot' => 'ready', 'company_id' => 3, 'responsible_user_id' => 9, 'profile_id' => 12, 'approval_level' => 2] && $contextPilotGateway->receivedContext === $contextPilotContext, 'context pilot must call its fixed gateway with the authenticated execution context and present only allow-listed fields');
mcpAssert(strpos((string) ($contextPilotResponse->getPayload()['result']['content'][0]['text'] ?? ''), 'Contexto ERP autorizado') !== false && strpos(json_encode($contextPilotResponse->getPayload()), 'internal-only') === false, 'context pilot must use ToolResultPresenter and hide the internal facade marker');
foreach (['company_id', 'user_id', 'profile_id', 'approval_level', 'tenant', 'database', 'class', 'method', 'file'] as $forbiddenArgument) {
    $body = '{"jsonrpc":"2.0","id":92,"method":"tools/call","params":{"name":"hardness.system.context_pilot","arguments":{' . json_encode($forbiddenArgument) . ':1}}}';
    $response = $contextPilotServer->handle($body, '2025-11-25', $contextPilotContext);
    mcpAssert(($response->getPayload()['error']['code'] ?? null) === -32602, 'context pilot schema must reject identity and infrastructure arguments');
}
$contextPilotScopeDenied = $contextPilotServer->handle($contextPilotCall, '2025-11-25', new ExecutionContext(7, 8, 3, 9, []));
mcpAssert(($contextPilotScopeDenied->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'scope_denied', 'context pilot must require the existing company:read scope');
$contextPilotGateway->fail = true;
$contextPilotFailure = $contextPilotServer->handle($contextPilotCall, '2025-11-25', $contextPilotContext);
mcpAssert(($contextPilotFailure->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'legacy_context_unavailable' && strpos(json_encode($contextPilotFailure->getPayload()), 'credentials') === false, 'context pilot bridge failure must remain functional and safe');
$productRepositorySource = file_get_contents(dirname(__DIR__) . '/src/Tools/Product/Infrastructure/PdoProductRepository.php');
mcpAssert($productRepositorySource !== false && strpos($productRepositorySource, 'EXISTS (SELECT 1 FROM D049') !== false && strpos($productRepositorySource, 'd009.D009_C004_Id=:company_id') !== false, 'product repository must use an EXISTS company link to avoid duplicate products and cross-company exposure');
$quoteRepositorySource = file_get_contents(dirname(__DIR__) . '/src/Tools/Quote/Infrastructure/PdoQuoteRepository.php');
mcpAssert($quoteRepositorySource !== false && strpos($quoteRepositorySource, 't003.T003_C004_Id=:company_id') !== false && strpos($quoteRepositorySource, 't004.T004_C004_Id=:company_id') !== false, 'quote repository must enforce the context company on headers and items');
$partnerRepositorySource = file_get_contents(dirname(__DIR__) . '/src/Tools/Partner/Infrastructure/PdoPartnerRepository.php');
mcpAssert($partnerRepositorySource !== false && strpos($partnerRepositorySource, 'd024.D024_C004_Id=:company_id') !== false && strpos($partnerRepositorySource, "d024.D024_Flag_Ativo='S'") !== false, 'partner repository must restrict results to active global or company-linked records');
$composedServer = (new CompositionBootstrap(dirname(__DIR__)))->createServerForTest($config, new TestPdo());
$composedList = $composedServer->handle('{"jsonrpc":"2.0","id":19,"method":"tools/list"}', '2025-11-25', new ExecutionContext(1, 1, 1, null, ['company:read', 'product:read', 'quote:read']));
mcpAssert(($composedList->getPayload()['result']['tools'][0]['name'] ?? null) === 'hardness_company_get_current', 'Bootstrap must expose the Claude-compatible Company tool name');
try {
    (new ToolCatalogFactory([
    new CompanyToolsModule(new TestCompanyRepository(), new TestPartnerRepository()),
    new CompanyToolsModule(new TestCompanyRepository(), new TestPartnerRepository()),
    ]))->createRegistry();
    throw new RuntimeException('duplicate tool registration must fail');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Duplicate MCP tool registration.', 'catalog factory must reject duplicate public Tool names');
}
mcpAssert($scopeCatalog->normalize(['company:read', 'product:read', 'product:read']) === ['company:read', 'product:read'], 'scope catalog must remove duplicate scopes');
mcpAssert($scopeCatalog->requiresErpActor(['quote:write']) && $scopeCatalog->requiresErpActor(['quote:item:add']) && $scopeCatalog->requiresErpActor(['quote:item:update']) && $scopeCatalog->requiresErpActor(['quote:item:delete']) && $scopeCatalog->requiresErpActor(['ide_smt:quote:order:generate']), 'every quote write scope must require an ERP actor in connection configuration');
mcpAssert(!$scopeCatalog->requiresErpActor(['quote:read']) && !$scopeCatalog->requiresErpActor(['company:read', 'product:read']), 'read-only scopes must not require an ERP actor in connection configuration');
try { $scopeCatalog->normalize(['admin']); throw new RuntimeException('unknown scope must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Unknown MCP scope.', 'scope catalog must reject unknown scopes'); }
try { $scopeCatalog->normalize('{"company:read":true}'); throw new RuntimeException('malformed scope payload must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Invalid scopes payload.', 'scope catalog must reject malformed payloads'); }
$queriesBeforeInvalidDate = count($adminPdo->executed);
foreach (['2024-02-30 10:00:00', '0000-00-00 00:00:00', '2000-01-01 00:00:00'] as $invalidDate) {
    try { $admin->createConnection('Teste', 1, null, ['mcp.read'], $invalidDate); throw new RuntimeException('invalid expiration date must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Invalid expiration date.', 'invalid or past expiration must be rejected before SQL'); }
}
mcpAssert(count($adminPdo->executed) === $queriesBeforeInvalidDate, 'invalid expiration must not open a transaction or execute SQL');
$originalTimezone = date_default_timezone_get();
date_default_timezone_set('America/Sao_Paulo');
$futureUtcDate = '2099-01-01 00:00:00';
$utcCreated = $admin->createConnection('UTC test', 1, null, ['mcp.read'], $futureUtcDate);
date_default_timezone_set($originalTimezone);
mcpAssert((new ApiKeyParser())->parseAuthorization('Bearer ' . $utcCreated['api_key']) !== null, 'future UTC expiration remains valid regardless of PHP default timezone');
$created = $admin->createConnection('Integração de teste', 1, null, ['mcp.read']);
mcpAssert($created['connection_id'] === 101 && (new ApiKeyParser())->parseAuthorization('Bearer ' . $created['api_key']) !== null, 'admin creation must return a key only at creation');
$secondCreated = $admin->createConnection("Integração de teste 2", 1, null, ["mcp.read"]);
mcpAssert(preg_match("/^[A-Za-z0-9_-]{24}$/", (string) ($created["public_id"] ?? "")) === 1 && preg_match("/^[A-Za-z0-9_-]{24}$/", (string) ($secondCreated["public_id"] ?? "")) === 1, "new MCP connections must receive opaque URL-safe public identifiers.");
mcpAssert($created["public_id"] !== $secondCreated["public_id"], "new MCP connections must receive distinct public identifiers.");
$connectionInserts = array_values(array_filter($adminPdo->executed, static function (array $query): bool { return strpos($query["sql"], "INSERT INTO MCP001") === 0; }));
$legacyPublicIdPdo = new TestAdminPdo();
$legacyPublicIdPdo->connectionPublicId = null;
$legacyPublicIdService = new \HardnessMcp\Admin\McpConnectionManagementService($legacyPublicIdPdo, "admin.test", "127.0.0.1", 77);
$legacyPublicId = $legacyPublicIdService->ensurePublicId(1, 101);
$legacyPublicIdRepeat = $legacyPublicIdService->ensurePublicId(1, 101);
mcpAssert(preg_match("/^[A-Za-z0-9_-]{24}$/", $legacyPublicId) === 1 && $legacyPublicId === $legacyPublicIdRepeat, "an existing connection must receive one stable public identifier without regeneration on repeat.");
mcpAssert(!array_filter($legacyPublicIdPdo->executed, static function (array $query): bool { return strpos($query["sql"], "UPDATE MCP005") === 0 || strpos($query["sql"], "UPDATE MCP007") === 0 || strpos($query["sql"], "UPDATE MCP008") === 0 || strpos($query["sql"], "UPDATE MCP002") === 0; }), "creating an exclusive URL for a legacy connection must not alter grants, OAuth tokens, refresh tokens, or API Keys.");
$lastConnectionInsert = $connectionInserts[count($connectionInserts) - 1] ?? null;
mcpAssert(is_array($lastConnectionInsert) && ($lastConnectionInsert["params"]["public_id"] ?? null) === $secondCreated["public_id"], "new MCP connection creation must persist its public identifier in MCP001.");
foreach (['quote:write', 'quote:item:add', 'quote:item:update', 'quote:item:delete', 'ide_smt:quote:order:generate'] as $writeScope) {
    try { $admin->createConnection('Sem ator', 1, null, [$writeScope]); throw new RuntimeException('write scope without actor must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'A responsible user is required for these permissions.', "$writeScope must require an ERP actor during connection creation"); }
}
$rotatedKey = $admin->rotateCredential(101);
mcpAssert((new ApiKeyParser())->parseAuthorization('Bearer ' . $rotatedKey) !== null, 'credential rotation must return a new valid key');
$admin->revokeCredential(201, 'test');
$admin->updateConnection(101, 'Integração de teste', ['mcp.read'], 'disabled');
$admin->updateConnection(101, 'Integração de teste', ['mcp.read'], 'active');
$adminSql = implode("\n", array_column($adminPdo->executed, 'sql'));
mcpAssert(strpos($adminSql, 'MCP002_Substituida_Por_MCP002_Id') !== false, 'rotation must link the previous credential to its replacement');
mcpAssert(substr_count($adminSql, 'INSERT INTO MCP003') >= 5, 'all administrative mutations must write MCP003 audit entries');
mcpAssert(strpos($adminSql, 'UTC_TIMESTAMP()+INTERVAL') !== false, 'rotation grace period must use the database UTC clock');
mcpAssert(strpos($adminSql, 'MCP003_C007_Id') !== false, 'administrative audit must persist the ERP actor column');
$adminAuditParameters = array_values(array_filter($adminPdo->executed, static function (array $item): bool { return strpos($item['sql'], 'INSERT INTO MCP003') === 0; }));
mcpAssert($adminAuditParameters !== [] && $adminAuditParameters[0]['params']['actor_user_id'] === 77, 'credential administrative audit must use the server-derived ERP actor');
try { $admin->revokeCredentialForCompany(201, 101, 1, '   '); throw new RuntimeException('blank revocation reason must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Invalid revocation reason.', 'revocation reason must be required'); }
try { $admin->generateFirstCredential(101, 2); throw new RuntimeException('cross-company credential operation must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Connection not found.', 'credential operation must reject a connection from another company'); }
$updatedScopes = $admin->updateScopesForCompany(101, 1, ['company:read', 'company:read']);
mcpAssert($updatedScopes === ['company:read'], 'scope update must normalize duplicate scopes');
foreach (['quote:write', 'quote:item:add', 'quote:item:update', 'quote:item:delete', 'ide_smt:quote:order:generate'] as $writeScope) {
    try { $admin->updateScopesForCompany(101, 1, [$writeScope]); throw new RuntimeException('write scope update without actor must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'A responsible user is required for API key permissions.', "$writeScope must require an ERP actor during scope updates"); }
}
$oauthOnlyPdo = new TestAdminPdo();
$oauthOnlyPdo->activeApiKey = false;
$oauthOnlyAdmin = new McpConnectionAdminService($oauthOnlyPdo, $config);
mcpAssert($oauthOnlyAdmin->updateScopesForCompany(101, 1, ['quote:item:update']) === ['quote:item:update'], 'an OAuth-only connection without an API Key may receive quote:item:update without MCP001_C007_Id');
mcpAssert($oauthOnlyAdmin->updateScopesForCompany(101, 1, ['quote:item:delete']) === ['quote:item:delete'], 'an OAuth-only connection without an API Key may receive quote:item:delete without MCP001_C007_Id');
$expiredApiKeyPdo = new TestAdminPdo();
$expiredApiKeyPdo->activeApiKey = false;
$expiredApiKeyAdmin = new McpConnectionAdminService($expiredApiKeyPdo, $config);
mcpAssert($expiredApiKeyAdmin->updateScopesForCompany(101, 1, ['quote:item:update']) === ['quote:item:update'], 'an expired API Key must not require an API-key responsible user');
$revokedApiKeyPdo = new TestAdminPdo();
$revokedApiKeyPdo->activeApiKey = false;
$revokedApiKeyAdmin = new McpConnectionAdminService($revokedApiKeyPdo, $config);
mcpAssert($revokedApiKeyAdmin->updateScopesForCompany(101, 1, ['quote:item:delete']) === ['quote:item:delete'], 'a revoked API Key must not require an API-key responsible user');
$activeApiKeyPdo = new TestAdminPdo();
$activeApiKeyPdo->connectionScopes = ['quote:item:update'];
$activeApiKeyAdmin = new McpConnectionAdminService($activeApiKeyPdo, $config);
try { $activeApiKeyAdmin->updateScopesForCompany(101, 1, ['quote:item:update']); throw new RuntimeException('active API Key without actor must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'A responsible user is required for API key permissions.', 'an active API Key with a write scope must require its responsible user'); }
try { $activeApiKeyAdmin->updateApiKeyResponsibleForCompany(101, 1, null); throw new RuntimeException('active API Key responsible removal must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'A responsible user is required for these permissions.', 'an active API Key with a write scope must not lose its responsible user'); }
try { $activeApiKeyAdmin->updateConnection(101, 'Alternativa', ['quote:item:delete'], 'active'); throw new RuntimeException('alternate update path must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'A responsible user is required for API key permissions.', 'updateConnection must not bypass the API-key responsible-user rule'); }
$issueApiKeyPdo = new TestAdminPdo();
$issueApiKeyPdo->activeApiKey = false;
$issueApiKeyPdo->connectionScopes = ['quote:item:update'];
$issueApiKeyAdmin = new McpConnectionAdminService($issueApiKeyPdo, $config);
try { $issueApiKeyAdmin->generateFirstCredential(101, 1); throw new RuntimeException('API Key generation without actor must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'A responsible user is required for API key permissions.', 'generating an API Key must remain blocked without an actor for write scopes'); }
$rotateApiKeyPdo = new TestAdminPdo();
$rotateApiKeyPdo->connectionScopes = ['quote:item:delete'];
$rotateApiKeyAdmin = new McpConnectionAdminService($rotateApiKeyPdo, $config);
try { $rotateApiKeyAdmin->rotateCredentialForCompany(101, 1); throw new RuntimeException('API Key rotation without actor must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'A responsible user is required for API key permissions.', 'rotating an API Key must remain blocked without an actor for write scopes'); }
try { $admin->updateScopesForCompany(101, 1, ['unknown:scope']); throw new RuntimeException('unknown scope update must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Unknown MCP scope.', 'scope update must reject unknown scope'); }
$adminScopeSql = implode("\n", array_column($adminPdo->executed, 'sql'));
mcpAssert(strpos($adminScopeSql, 'connection_scopes_updated') === false || strpos($adminScopeSql, 'INSERT INTO MCP003') !== false, 'scope update must be audited in MCP003');
$disabledAdminPdo = new TestAdminPdo();
$disabledAdminPdo->connectionStatus = 'disabled';
$disabledAdmin = new McpConnectionAdminService($disabledAdminPdo, $config);
try { $disabledAdmin->generateFirstCredential(101, 1); throw new RuntimeException('disabled connection must not issue credentials'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Connection must be active to issue credentials.', 'disabled connection must be rejected before creating a credential'); }
$configV2 = $config;
$configV2['auth']['credential_pepper_current_version'] = 'v2';
$configV2['auth']['credential_peppers'] = ['v1' => 'old-pepper', 'v2' => 'current-pepper'];
$adminV2 = new McpConnectionAdminService(new TestAdminPdo(), $configV2);
$v2Created = $adminV2->createConnection('Pepper v2', 1, null, ['mcp.read']);
mcpAssert((new ApiKeyParser())->parseAuthorization('Bearer ' . $v2Created['api_key']) !== null, 'configured current pepper v2 must create a credential');
$configV2Missing = $configV2;
$configV2Missing['auth']['credential_peppers'] = ['v1' => 'old-pepper'];
try { new McpConnectionAdminService(new TestAdminPdo(), $configV2Missing); throw new RuntimeException('missing current pepper must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Credential pepper is unavailable.', 'missing configured current pepper must fail closed'); }
try { $admin->createConnection('   ', 1, null, ['mcp.read']); throw new RuntimeException('blank name must fail'); } catch (RuntimeException $exception) { mcpAssert($exception->getMessage() === 'Invalid connection name.', 'blank connection name must be rejected'); }

$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders()), $config);
mcpAssert($response->getStatus() === 200, 'valid initialize must return HTTP 200');
mcpAssert(($response->getPayload()['result']['protocolVersion'] ?? null) === '2025-11-25', 'initialize must return the supported protocol version');
mcpAssert(($response->getPayload()['result']['serverInfo']['name'] ?? null) === 'Hardness MCP', 'initialize must return server information');
mcpAssert(isset($response->getPayload()['result']['capabilities']['tools']), 'initialize must announce tools capability');

$toolsHeaders = mcpHeaders(['MCP-Protocol-Version' => '2025-11-25']);
$toolsListBody = '{"jsonrpc":"2.0","id":20,"method":"tools/list"}';
$toolsCallBody = '{"jsonrpc":"2.0","id":21,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":{}}}';
$toolsBootstrap = new TestBootstrap(dirname(__DIR__));
$toolsBootstrap->setScopes(['company:read']);
$response = $toolsBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $toolsListBody, $toolsHeaders), $config);
mcpAssert(($response->getPayload()['result']['tools'][0]['name'] ?? null) === 'hardness_company_get_current', 'authorized tool must be listed with its Claude-compatible public name');
mcpAssert(($response->getPayload()['result']['tools'][0]['inputSchema']['additionalProperties'] ?? null) === false, 'tool schema must reject additional properties');
mcpAssert(($toolsBootstrap->getAuditEvents()[0]['event'] ?? null) === 'tools_list', 'tools/list must be audited');
$response = $toolsBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $toolsCallBody, $toolsHeaders), $config);
$company = $response->getPayload()['result']['structuredContent'] ?? [];
mcpAssert($response->getStatus() === 200 && ($company['id'] ?? null) === 1 && ($company['legal_name'] ?? null) === 'Empresa Teste', 'authorized tool call must return the current company');
mcpAssert(array_keys($company) === ['id', 'legal_name', 'short_name', 'document', 'city', 'state', 'status'], 'tool response must contain only approved fields');
mcpAssert(strpos((string) ($response->getPayload()['result']['content'][0]['text'] ?? ''), 'Razão social: Empresa Teste') !== false, 'company content must present the same approved business fields as structuredContent');
mcpAssert($toolsBootstrap->getCompanyRepository()->lastCompanyId === 1, 'tool must use companyId exclusively from ExecutionContext');
mcpAssert(($toolsBootstrap->getAuditEvents()[1]['event'] ?? null) === 'tools_call_success' && ($toolsBootstrap->getAuditEvents()[1]['safe_code'] ?? null) === 'hardness.company.get_current', 'successful tool call must be audited without arguments');
$newPublicCompanyCall = '{"jsonrpc":"2.0","id":28,"method":"tools/call","params":{"name":"hardness_company_get_current","arguments":{}}}';
$response = $toolsBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $newPublicCompanyCall, $toolsHeaders), $config);
mcpAssert(($response->getPayload()['result']['structuredContent']['id'] ?? null) === 1, 'Claude-compatible public tool name must dispatch successfully');

$businessBootstrap = new TestBootstrap(dirname(__DIR__));
$businessBootstrap->setScopes(['product:read', 'quote:read', 'quote:report:read', 'sales:read']);
$businessList = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $toolsListBody, $toolsHeaders), $config);
mcpAssert(array_column($businessList->getPayload()['result']['tools'], 'name') === ['hardness_product_search', 'hardness_quote_search', 'hardness_quote_get_by_id', 'hardness_quote_summary', 'hardness_sales_summary'], 'tools/list must expose only Claude-compatible business Tool names');
$productCall = '{"jsonrpc":"2.0","id":29,"method":"tools/call","params":{"name":"hardness_product_search","arguments":{"query":"ABC"}}}';
$productResponse = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $productCall, $toolsHeaders), $config);
mcpAssert(($productResponse->getPayload()['result']['structuredContent']['items'][0] ?? null) === ['id' => 11, 'code' => 'ABC001', 'description' => 'Produto teste', 'unit' => 'UN'], 'product search must expose only the approved public fields');
mcpAssert(($productResponse->getPayload()['result']['structuredContent']['count'] ?? null) === 1 && strpos((string) ($productResponse->getPayload()['result']['content'][0]['text'] ?? ''), 'ID interno: 11') !== false && strpos((string) ($productResponse->getPayload()['result']['content'][0]['text'] ?? ''), 'Código: ABC001') !== false, 'product collection content and count must agree with structuredContent');
mcpAssert($businessBootstrap->getProductRepository()->lastCompanyId === 1, 'product search must get companyId exclusively from ExecutionContext');
$productSearchMore = (new \HardnessMcp\Tools\Product\Search\ProductSearchHandler(new class implements ProductRepositoryInterface {
    public function search(int $companyId, string $query, int $limit): array { return [['D001_Id' => 1, 'D001_Codigo_Produto' => 'A', 'D001_Descricao_Produto' => 'A', 'D037_Unidade' => 'UN'], ['D001_Id' => 2, 'D001_Codigo_Produto' => 'B', 'D001_Descricao_Produto' => 'B', 'D037_Unidade' => 'UN']]; }
}))->handle(\HardnessMcp\Tools\Product\Search\ProductSearchInput::fromArguments(['query' => 'A', 'limit' => 1]), new ExecutionContext(1, 1, 1, 1, ['product:read']));
mcpAssert(($productSearchMore['count'] ?? null) === 1 && ($productSearchMore['has_more'] ?? null) === true, 'product search must disclose when the requested limit truncated matching results');
$productSearchUnavailable = (new \HardnessMcp\Tools\Product\Search\ProductSearchTool(new \HardnessMcp\Tools\Product\Search\ProductSearchHandler(new class implements ProductRepositoryInterface {
    public function search(int $companyId, string $query, int $limit): array { throw new RuntimeException('database details must not escape'); }
})))->execute(['query' => 'A'], new ExecutionContext(1, 1, 1, 1, ['product:read']));
mcpAssert(($productSearchUnavailable->getResult()['structuredContent']['error']['code'] ?? null) === 'product_search_unavailable' && $productSearchUnavailable->getAuditEvent() === 'tools_call_product_search_unavailable', 'product lookup failures must return a safe actionable result');
$partnerReferenceCall = '{"jsonrpc":"2.0","id":33,"method":"tools/call","params":{"name":"hardness_product_get_by_partner_reference","arguments":{"partner_code":7,"role":"customer","reference_code":"ABC-001"}}}';
$partnerReferenceDenied = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $partnerReferenceCall, $toolsHeaders), $config);
mcpAssert(($partnerReferenceDenied->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'scope_denied', 'partner-reference product lookup must require its new scope without changing product:read');
$partnerReferenceBootstrap = new TestBootstrap(dirname(__DIR__));
$partnerReferenceBootstrap->setScopes(['product:partner-reference:lookup']);
$partnerReferenceResponse = $partnerReferenceBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $partnerReferenceCall, $toolsHeaders), $config);
$partnerReferencePayload = $partnerReferenceResponse->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($partnerReferencePayload['items'][0] ?? null) === ['id'=>11,'code'=>'ABC001','description'=>'Produto teste','unit'=>'UN','source'=>'partner_reference'] && ($partnerReferencePayload['partner_code'] ?? null) === 7 && ($partnerReferencePayload['role'] ?? null) === 'customer' && ($partnerReferencePayload['reference_code'] ?? null) === 'ABC-001' && ($partnerReferencePayload['selection_required'] ?? null) === false, 'customer partner reference lookup must return the quote-compatible product and explicit source');
$partnerReferenceProductId = (int) ($partnerReferencePayload['items'][0]['id'] ?? 0);
$quoteFromPartnerReferenceCall = json_encode(['jsonrpc'=>'2.0','id'=>94,'method'=>'tools/call','params'=>['name'=>'hardness_quote_create','arguments'=>['customer_id'=>12,'items'=>[['product_id'=>$partnerReferenceProductId,'quantity'=>1]]]]]);
$quoteFromPartnerReferenceResponse = $quoteCreateServer->handle((string) $quoteFromPartnerReferenceCall, '2025-11-25', $quoteCreateContext);
mcpAssert(($quoteFromPartnerReferenceResponse->getPayload()['result']['structuredContent']['items'][0]['product_id'] ?? null) === $partnerReferenceProductId && ($quoteCreateGateway->arguments['items'][0]['product_id'] ?? null) === $partnerReferenceProductId, 'the product id returned from a partner reference lookup must be accepted as quote.create product_id');
$supplierReferenceCall = '{"jsonrpc":"2.0","id":34,"method":"tools/call","params":{"name":"hardness_product_get_by_partner_reference","arguments":{"partner_code":8,"role":"supplier","reference_code":"SUP-001"}}}';
$supplierReferenceResponse = $partnerReferenceBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $supplierReferenceCall, $toolsHeaders), $config);
mcpAssert(($supplierReferenceResponse->getPayload()['result']['structuredContent']['items'][0]['id'] ?? null) === 14, 'supplier partner reference lookup must enforce and resolve the supplier role independently');
$wrongRoleCall = '{"jsonrpc":"2.0","id":35,"method":"tools/call","params":{"name":"hardness_product_get_by_partner_reference","arguments":{"partner_code":7,"role":"supplier","reference_code":"ABC-001"}}}';
$wrongRoleResponse = $partnerReferenceBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $wrongRoleCall, $toolsHeaders), $config);
mcpAssert(($wrongRoleResponse->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'partner_not_available', 'partner reference lookup must reject a partner without the requested role');
$inactivePartnerCall = '{"jsonrpc":"2.0","id":351,"method":"tools/call","params":{"name":"hardness_product_get_by_partner_reference","arguments":{"partner_code":9,"role":"customer","reference_code":"ABC-001"}}}';
$inactivePartnerResponse = $partnerReferenceBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $inactivePartnerCall, $toolsHeaders), $config);
mcpAssert(($inactivePartnerResponse->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'partner_not_available', 'partner reference lookup must reject inactive partners before checking references');
$missingReferenceCall = '{"jsonrpc":"2.0","id":36,"method":"tools/call","params":{"name":"hardness_product_get_by_partner_reference","arguments":{"partner_code":7,"role":"customer","reference_code":"MISSING"}}}';
$missingReferenceResponse = $partnerReferenceBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $missingReferenceCall, $toolsHeaders), $config);
mcpAssert(($missingReferenceResponse->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'partner_product_reference_not_found', 'partner reference lookup must not fall back to internal product fields');
$specialReferenceCall = '{"jsonrpc":"2.0","id":37,"method":"tools/call","params":{"name":"hardness_product_get_by_partner_reference","arguments":{"partner_code":7,"role":"customer","reference_code":"X%_"}}}';
$specialReferenceResponse = $partnerReferenceBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $specialReferenceCall, $toolsHeaders), $config);
mcpAssert(($specialReferenceResponse->getPayload()['result']['structuredContent']['items'][0]['id'] ?? null) === 13, 'partner reference lookup must treat percent and underscore as literal reference characters');
$multipleReferenceCall = '{"jsonrpc":"2.0","id":38,"method":"tools/call","params":{"name":"hardness_product_get_by_partner_reference","arguments":{"partner_code":7,"role":"customer","reference_code":"MULTI"}}}';
$multipleReferenceResponse = $partnerReferenceBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $multipleReferenceCall, $toolsHeaders), $config);
mcpAssert(($multipleReferenceResponse->getPayload()['result']['structuredContent']['count'] ?? null) === 2 && ($multipleReferenceResponse->getPayload()['result']['structuredContent']['selection_required'] ?? null) === true, 'partner reference lookup must return ambiguity instead of choosing a product');
$outsideCompanyBootstrap = new TestBootstrap(dirname(__DIR__));
$outsideCompanyBootstrap->setCompanyId(2); $outsideCompanyBootstrap->setScopes(['product:partner-reference:lookup']);
$outsideCompanyResponse = $outsideCompanyBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $partnerReferenceCall, $toolsHeaders), $config);
mcpAssert(($outsideCompanyResponse->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'partner_not_available', 'partner reference lookup must isolate partners by connection company');
$partnerReferenceServer = new JsonRpcServer($config, new ToolExecutor((new ToolCatalogFactory([new ProductToolsModule(new TestProductRepository(), new TestProductRepository())]))->createRegistry()));
$oauthPartnerReference = $partnerReferenceServer->handle($partnerReferenceCall, '2025-11-25', new ExecutionContext(1, 1, 1, null, ['product:partner-reference:lookup'], 'oauth'));
mcpAssert(($oauthPartnerReference->getPayload()['result']['structuredContent']['items'][0]['id'] ?? null) === 11, 'OAuth execution context with the granted partner-reference scope must access the new Tool');
$apiKeyPartnerReference = $partnerReferenceServer->handle($partnerReferenceCall, '2025-11-25', new ExecutionContext(1, 1, 1, null, ['product:partner-reference:lookup'], 'api_key'));
mcpAssert(($apiKeyPartnerReference->getPayload()['result']['structuredContent']['items'][0]['id'] ?? null) === 11, 'API key execution context with the granted partner-reference scope must access the new Tool');
$partnerBootstrap = new TestBootstrap(dirname(__DIR__));
$partnerBootstrap->setScopes(['partner:search']);
$partnerCall = '{"jsonrpc":"2.0","id":35,"method":"tools/call","params":{"name":"hardness_partner_search","arguments":{"criterion":"document","query":"12.345.678/0001-90","role":"customer"}}}';
$partnerResponse = $partnerBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $partnerCall, $toolsHeaders), $config);
$partnerItem = $partnerResponse->getPayload()['result']['structuredContent']['items'][0] ?? [];
mcpAssert(!isset($partnerItem['id']) && !isset($partnerItem['document']) && is_string($partnerItem['candidate_ref'] ?? null) && ($partnerItem['legal_name'] ?? null) === 'Atom Indústria Ltda' && $partnerBootstrap->getPartnerRepository()->lastCompanyId === 1 && $partnerBootstrap->getPartnerRepository()->lastQuery === '12345678000190' && $partnerBootstrap->getPartnerRepository()->lastRole === 'customer' && $partnerBootstrap->getPartnerRepository()->lastLimit === 6, 'partner search must normalize documents, use execution context and minimize output');
mcpAssert(strpos((string) ($partnerResponse->getPayload()['result']['content'][0]['text'] ?? ''), (string) $partnerItem['candidate_ref']) !== false, 'partner search text must expose candidate_ref so MCP clients can chain partner get');
$partnerGetCall = json_encode(['jsonrpc'=>'2.0','id'=>37,'method'=>'tools/call','params'=>['name'=>'hardness_partner_get','arguments'=>['candidate_ref'=>$partnerItem['candidate_ref']]]]);
$partnerGetResponse = $partnerBootstrap->handle(new Request('POST','/mcp','cliente.fastcrud.com.br',(string)$partnerGetCall,$toolsHeaders),$config);
$partnerGetPayload = $partnerGetResponse->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($partnerGetPayload['document'] ?? null) === '12.345.678/0001-90' && ($partnerGetPayload['code'] ?? null) === 7 && strpos((string) ($partnerGetResponse->getPayload()['result']['content'][0]['text'] ?? ''), 'Código: 7') !== false && ($partnerGetPayload['address']['street'] ?? null) === 'Rua Teste', 'partner search candidate_ref must open the selected partner details with its public code in the same connection');
$partnerInvalidCall = '{"jsonrpc":"2.0","id":36,"method":"tools/call","params":{"name":"hardness_partner_search","arguments":{"criterion":"name","query":"Joe","role":"customer"}}}';
$partnerInvalidResponse = $partnerBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $partnerInvalidCall, $toolsHeaders), $config);
mcpAssert(($partnerInvalidResponse->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'partner_search_refinement_required', 'partner search must require refinement for generic name searches');
$felipeRepository = new class implements PartnerRepositoryInterface {
    public string $query = '';
    public function searchByDocument(int $companyId, string $document, string $role, int $limit): array { return []; }
    public function searchByName(int $companyId, string $query, string $role, int $limit): array { $this->query = $query; return $query === 'Felipe Augusto' ? [['D024_Id'=>201,'D024_Nome_Empresa'=>'FELIPE  AUGUSTO','D024_Nome_Fantasia'=>'FELIPE  AUGUSTO','D024_Cidade'=>null,'D024_Estado'=>null]] : []; }
    public function findVisibleById(int $companyId, int $partnerId): ?array { return null; }
};
$felipeTool = new \HardnessMcp\Tools\Partner\Search\PartnerSearchTool(new \HardnessMcp\Tools\Partner\Search\PartnerSearchHandler($felipeRepository, new \HardnessMcp\Tools\Partner\Search\PartnerCandidateReference('test-only-candidate-reference-key')));
$felipeResult = $felipeTool->execute(['criterion'=>'name','query'=>' Felipe   Augusto ','role'=>'customer'], new ExecutionContext(1, 1, 1, 1, ['partner:search']));
mcpAssert($felipeRepository->query === 'Felipe Augusto' && ($felipeResult->getResult()['structuredContent']['items'][0]['legal_name'] ?? null) === 'FELIPE  AUGUSTO', 'partner name search must collapse input whitespace while preserving minimized partner output');
$emptyProductBootstrap = new TestBootstrap(dirname(__DIR__));
$emptyProductBootstrap->setScopes(['product:read']);
$emptyProductBootstrap->setCompanyId(2);
$emptyProductResponse = $emptyProductBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $productCall, $toolsHeaders), $config);
mcpAssert(($emptyProductResponse->getPayload()['result']['isError'] ?? false) === false && ($emptyProductResponse->getPayload()['result']['structuredContent'] ?? null) === ['items' => [], 'count' => 0, 'has_more' => false], 'an empty collection must remain a successful structured response');
mcpAssert(($emptyProductResponse->getPayload()['result']['content'][0]['text'] ?? null) === 'Nenhum produto encontrado.', 'an empty collection must have explicit useful text');
$quoteSearchCall = '{"jsonrpc":"2.0","id":30,"method":"tools/call","params":{"name":"hardness.quote.search","arguments":{"issued_from":"2026-08-01","issued_to":"2026-08-31"}}}';
$quoteSearchResponse = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $quoteSearchCall, $toolsHeaders), $config);
mcpAssert(($quoteSearchResponse->getPayload()['result']['structuredContent']['items'][0]['id'] ?? null) === 50 && $businessBootstrap->getQuoteRepository()->lastCompanyId === 1, 'quote search must be limited to the ExecutionContext company');
mcpAssert(($quoteSearchResponse->getPayload()['result']['structuredContent']['count'] ?? null) === 1 && strpos((string) ($quoteSearchResponse->getPayload()['result']['content'][0]['text'] ?? ''), 'Orçamento: 50') !== false, 'quote collection content and count must agree with structuredContent');
$quoteSearchAssistantIaCall = '{"jsonrpc":"2.0","id":301,"method":"tools/call","params":{"name":"hardness_quote_search","arguments":{"issued_from":"2026-08-01","issued_to":"2026-08-31","origin":"assistant_ia"}}}';
$quoteSearchAssistantIaResponse = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $quoteSearchAssistantIaCall, $toolsHeaders), $config);
mcpAssert(($quoteSearchAssistantIaResponse->getPayload()['result']['structuredContent']['count'] ?? null) === 1 && $businessBootstrap->getQuoteRepository()->lastOrigin === 'assistant_ia', 'quote search must pass the assistant IA origin filter to its repository');
$quoteSearchMore = (new \HardnessMcp\Tools\Quote\Search\QuoteSearchHandler(new class implements QuoteRepositoryInterface {
    public function search(int $companyId, string $issuedFrom, string $issuedTo, ?int $customerId, ?string $origin, int $limit): array { return [['T003_Id' => 1, 'T003_Data_Emissao' => '2026-08-01', 'status_label' => 'Pendente', 'T003_Valor_Total' => '0', 'D024_Id' => 1, 'D024_Nome_Fantasia' => 'A'], ['T003_Id' => 2, 'T003_Data_Emissao' => '2026-08-01', 'status_label' => 'Pendente', 'T003_Valor_Total' => '0', 'D024_Id' => 1, 'D024_Nome_Fantasia' => 'A']]; }
    public function findById(int $companyId, int $quoteId): ?array { return null; }
}))->handle(\HardnessMcp\Tools\Quote\Search\QuoteSearchInput::fromArguments(['issued_from' => '2026-08-01', 'issued_to' => '2026-08-01', 'limit' => 1]), new ExecutionContext(1, 1, 1, 1, ['quote:read']));
mcpAssert(($quoteSearchMore['count'] ?? null) === 1 && ($quoteSearchMore['has_more'] ?? null) === true, 'quote search must disclose when the requested limit truncated matching results');
$quoteSearchUnavailable = (new \HardnessMcp\Tools\Quote\Search\QuoteSearchTool(new \HardnessMcp\Tools\Quote\Search\QuoteSearchHandler(new class implements QuoteRepositoryInterface {
    public function search(int $companyId, string $issuedFrom, string $issuedTo, ?int $customerId, ?string $origin, int $limit): array { throw new RuntimeException('database details must not escape'); }
    public function findById(int $companyId, int $quoteId): ?array { return null; }
})))->execute(['issued_from' => '2026-08-01', 'issued_to' => '2026-08-01'], new ExecutionContext(1, 1, 1, 1, ['quote:read']));
mcpAssert(($quoteSearchUnavailable->getResult()['structuredContent']['error']['code'] ?? null) === 'quote_search_unavailable' && $quoteSearchUnavailable->getAuditEvent() === 'tools_call_quote_search_unavailable', 'quote search failures must return a safe actionable result');
$quoteSummaryCall = '{"jsonrpc":"2.0","id":304,"method":"tools/call","params":{"name":"hardness_quote_summary","arguments":{"date_from":"2026-08-01","date_to":"2026-08-31","group_by":"customer","limit":1}}}';
$quoteSummaryResponse = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $quoteSummaryCall, $toolsHeaders), $config);
$quoteSummaryPayload = $quoteSummaryResponse->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($quoteSummaryPayload['summary'] ?? null) === ['quoted_total' => '200.00', 'quote_count' => 4] && ($quoteSummaryPayload['rows'] ?? null) === [['customer_id' => 7, 'customer_name' => 'Cliente', 'quoted_total' => '120.00', 'quote_count' => 2]] && ($quoteSummaryPayload['has_more'] ?? null) === true && strpos((string) ($quoteSummaryResponse->getPayload()['result']['content'][0]['text'] ?? ''), 'Cliente: 7') !== false, 'quote summary must return ranked customer rows in both tool text and structured content');
$quoteSummaryAssistantIa = (new \HardnessMcp\Tools\Quote\Summary\QuoteSummaryHandler($businessBootstrap->getQuoteRepository()))->handle(\HardnessMcp\Tools\Quote\Summary\QuoteSummaryInput::fromArguments(['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'status', 'origin' => 'assistant_ia']), new ExecutionContext(1, 1, 1, 1, ['quote:report:read']));
mcpAssert(($quoteSummaryAssistantIa['filters_applied']['origin'] ?? null) === 'assistant_ia' && $businessBootstrap->getQuoteRepository()->lastOrigin === 'assistant_ia', 'quote summary must apply and report the assistant IA origin filter');
$quoteSummaryBySupervisor = (new \HardnessMcp\Tools\Quote\Summary\QuoteSummaryHandler(new TestQuoteRepository()))->handle(\HardnessMcp\Tools\Quote\Summary\QuoteSummaryInput::fromArguments(['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'supervisor']), new ExecutionContext(1, 1, 1, 1, ['quote:report:read']));
mcpAssert(($quoteSummaryBySupervisor['rows'][0] ?? null) === ['supervisor_id' => 4, 'supervisor_name' => 'Gerente', 'quoted_total' => '200.00', 'quote_count' => 4], 'quote summary must support supervisor groupings based on the quote creator');
$quoteSummaryBySeller = (new \HardnessMcp\Tools\Quote\Summary\QuoteSummaryHandler(new TestQuoteRepository()))->handle(\HardnessMcp\Tools\Quote\Summary\QuoteSummaryInput::fromArguments(['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'seller_internal']), new ExecutionContext(1, 1, 1, 1, ['quote:report:read']));
mcpAssert(($quoteSummaryBySeller['rows'][0]['supervisor_name'] ?? null) === 'Gerente', 'seller rows must include the supervisor linked to the seller');
$quoteSummarySupervisorSellersRepository = new TestQuoteRepository();
$quoteSummarySupervisorSellers = (new \HardnessMcp\Tools\Quote\Summary\QuoteSummaryHandler($quoteSummarySupervisorSellersRepository))->handle(\HardnessMcp\Tools\Quote\Summary\QuoteSummaryInput::fromArguments(['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'seller_internal', 'supervisor_id' => 4, 'origin' => 'assistant_ia']), new ExecutionContext(1, 1, 1, 1, ['quote:report:read']));
mcpAssert(($quoteSummarySupervisorSellers['filters_applied']['supervisor_id'] ?? null) === 4 && $quoteSummarySupervisorSellersRepository->lastSupervisorId === 4 && $quoteSummarySupervisorSellersRepository->lastOrigin === 'assistant_ia', 'quote summary must filter seller rows by supervisor and preserve the assistant IA origin filter');
$quoteSummaryTextRows = (new \HardnessMcp\Tools\Quote\Summary\QuoteSummaryTool(new \HardnessMcp\Tools\Quote\Summary\QuoteSummaryHandler(new TestQuoteRepository())))->execute(['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'seller_internal'], new ExecutionContext(1, 1, 1, 1, ['quote:report:read']))->getResult()['content'][0]['text'] ?? '';
mcpAssert(strpos((string)$quoteSummaryTextRows, 'Supervisor: 4') !== false && strpos((string)$quoteSummaryTextRows, 'Nome do supervisor: Gerente') !== false, 'quote summary text must render the supervisor included with each seller');
$quoteByStatus = (new \HardnessMcp\Tools\Quote\Summary\QuoteSummaryHandler(new TestQuoteRepository()))->handle(\HardnessMcp\Tools\Quote\Summary\QuoteSummaryInput::fromArguments(['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'status']), new ExecutionContext(1, 1, 1, 1, ['quote:report:read']));
mcpAssert(($quoteByStatus['rows'][0] ?? null) === ['status' => 'P', 'status_label' => 'Pendente', 'quoted_total' => '150.00', 'quote_count' => 3], 'quote summary status rows must expose a business status label');
$salesSummaryCall = '{"jsonrpc":"2.0","id":305,"method":"tools/call","params":{"name":"hardness_sales_summary","arguments":{"date_from":"2026-08-01","date_to":"2026-08-31","group_by":"day"}}}';
$salesSummaryResponse = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $salesSummaryCall, $toolsHeaders), $config);
$salesSummaryPayload = $salesSummaryResponse->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($salesSummaryPayload['summary'] ?? null) === ['invoiced_total' => '128450.75', 'invoice_count' => 83] && ($salesSummaryPayload['rows'][0] ?? null) === ['date' => '2026-08-01', 'invoiced_total' => '15200.00', 'invoice_count' => 12] && strpos((string) ($salesSummaryResponse->getPayload()['result']['content'][0]['text'] ?? ''), 'Data: 2026-08-01') !== false && $businessBootstrap->getSalesSummaryRepository()->lastCompanyId === 1, 'sales summary must return aggregated rows in both tool text and structured content while using the ExecutionContext company');
$salesByCustomer = (new \HardnessMcp\Tools\Sales\SalesSummaryHandler(new TestSalesSummaryRepository()))->handle(\HardnessMcp\Tools\Sales\SalesSummaryInput::fromArguments(['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'customer', 'limit' => 1]), new ExecutionContext(1, 1, 1, 1, ['sales:read']));
mcpAssert(($salesByCustomer['rows'] ?? null) === [['customer_id' => 7, 'customer_name' => 'Cliente', 'invoiced_total' => '32000.00', 'invoice_count' => 14]] && ($salesByCustomer['has_more'] ?? null) === true, 'sales customer summaries must be limited and disclose truncation without changing the total summary');
$salesInvalidPeriod = (new \HardnessMcp\Tools\Sales\SalesSummaryTool(new \HardnessMcp\Tools\Sales\SalesSummaryHandler(new TestSalesSummaryRepository())))->execute(['date_from' => '2026-01-01', 'date_to' => '2026-04-01', 'group_by' => 'day'], new ExecutionContext(1, 1, 1, 1, ['sales:read']));
mcpAssert(($salesInvalidPeriod->getResult()['structuredContent']['error']['code'] ?? null) === 'sales_summary_invalid_period', 'sales summary must reject daily ranges longer than 90 days before querying');
$salesLongPeriod = (new \HardnessMcp\Tools\Sales\SalesSummaryTool(new \HardnessMcp\Tools\Sales\SalesSummaryHandler(new TestSalesSummaryRepository())))->execute(['date_from' => '2025-01-01', 'date_to' => '2026-01-02', 'group_by' => 'document'], new ExecutionContext(1, 1, 1, 1, ['sales:read']));
mcpAssert(strpos((string) ($salesLongPeriod->getResult()['structuredContent']['error']['message'] ?? ''), 'Não divida') !== false, 'sales summary must prohibit sequential calls as a workaround for the 12-month interactive limit');
$quoteGetCall = '{"jsonrpc":"2.0","id":31,"method":"tools/call","params":{"name":"hardness.quote.get_by_id","arguments":{"id":50}}}';
$quoteGetResponse = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $quoteGetCall, $toolsHeaders), $config);
mcpAssert(($quoteGetResponse->getPayload()['result']['structuredContent']['items'][0]['description'] ?? null) === 'Produto histórico', 'quote detail must return the approved historical item fields');
mcpAssert(($quoteGetResponse->getPayload()['result']['structuredContent']['items'][0]['location_code'] ?? null) === 'SALA', 'quote detail must return the ERP Ambiente stored on the item');
mcpAssert(strpos((string) ($quoteGetResponse->getPayload()['result']['content'][0]['text'] ?? ''), 'Produto histórico') !== false, 'quote detail content must render approved item data');
$businessAuditEvents = $businessBootstrap->getAuditEvents();
$lastBusinessAuditEvent = $businessAuditEvents[count($businessAuditEvents) - 1] ?? [];
mcpAssert(($lastBusinessAuditEvent['event'] ?? null) === 'tools_call_success' && ($lastBusinessAuditEvent['safe_code'] ?? null) === 'hardness.quote.get_by_id', 'business Tool calls must preserve safe audit events');
$quoteLookupUnavailable = (new \HardnessMcp\Tools\Quote\GetById\QuoteGetByIdTool(new \HardnessMcp\Tools\Quote\GetById\QuoteGetByIdHandler(new class implements QuoteRepositoryInterface {
    public function search(int $companyId, string $issuedFrom, string $issuedTo, ?int $customerId, ?string $origin, int $limit): array { return []; }
    public function findById(int $companyId, int $quoteId): ?array { throw new RuntimeException('database details must not escape'); }
})))->execute(['id' => 50], new ExecutionContext(1, 1, 1, 1, ['quote:read']));
mcpAssert(($quoteLookupUnavailable->getResult()['structuredContent']['error']['code'] ?? null) === 'quote_lookup_unavailable' && $quoteLookupUnavailable->getAuditEvent() === 'tools_call_quote_lookup_unavailable', 'quote detail lookup failures must return a safe actionable result');
$otherCompany = new TestBootstrap(dirname(__DIR__));
$otherCompany->setScopes(['quote:read']);
$otherCompany->setCompanyId(2);
$otherCompanyResponse = $otherCompany->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $quoteGetCall, $toolsHeaders), $config);
mcpAssert(($otherCompanyResponse->getPayload()['result']['isError'] ?? false) === true && ($otherCompany->getAuditEvents()[0]['event'] ?? null) === 'tools_call_quote_not_found', 'a quote from another company must be indistinguishable from not found');
mcpAssert(($otherCompanyResponse->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'quote_not_found', 'functional not-found results must expose a stable safe error code');
foreach ([
    '{"jsonrpc":"2.0","id":32,"method":"tools/call","params":{"name":"hardness.product.search","arguments":{"query":"ABC","C004_Id":2}}}',
    '{"jsonrpc":"2.0","id":33,"method":"tools/call","params":{"name":"hardness.quote.search","arguments":{"issued_from":"2026-08-01","issued_to":"2026-08-31","C004_Id":2}}}',
    '{"jsonrpc":"2.0","id":34,"method":"tools/call","params":{"name":"hardness.quote.get_by_id","arguments":{"id":50,"C004_Id":2}}}',
] as $companyEscapeAttempt) {
    $response = $businessBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $companyEscapeAttempt, $toolsHeaders), $config);
    mcpAssert(($response->getPayload()['error']['code'] ?? null) === -32602, 'business Tool schemas must reject any company argument');
}

$withoutScopeBootstrap = new TestBootstrap(dirname(__DIR__));
$withoutScopeBootstrap->setScopes([]);
$response = $withoutScopeBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $toolsListBody, $toolsHeaders), $config);
mcpAssert(($response->getPayload()['result']['tools'] ?? null) === [], 'tool must not be listed without its scope');
$response = $withoutScopeBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $toolsCallBody, $toolsHeaders), $config);
mcpAssert(($response->getPayload()['result']['isError'] ?? false) === true && $withoutScopeBootstrap->getCompanyRepository()->calls === 0, 'scope denial must not execute the company repository');
mcpAssert(($withoutScopeBootstrap->getAuditEvents()[1]['event'] ?? null) === 'tools_call_scope_denied', 'scope denial must be audited');

foreach (['{"jsonrpc":"2.0","id":22,"method":"tools/call","params":{"name":"hardness.company.get_current"}}', '{"jsonrpc":"2.0","id":23,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":[]}}', '{"jsonrpc":"2.0","id":24,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":{"C004_Id":999}}}', '{"jsonrpc":"2.0","id":25,"method":"tools/call","params":{"name":"hardness.company.get_current","arguments":{"tenant":"outro-cliente"}}}'] as $invalidToolCall) {
    $response = $toolsBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $invalidToolCall, $toolsHeaders), $config);
    mcpAssert(($response->getPayload()['error']['code'] ?? null) === -32602, 'missing, non-object or additional tool arguments must be rejected');
    mcpAssert(($toolsBootstrap->getAuditEvents()[count($toolsBootstrap->getAuditEvents()) - 1]['event'] ?? null) === 'tools_call_invalid_params', 'invalid tool parameters must be audited');
}
$response = $toolsBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '{"jsonrpc":"2.0","id":26,"method":"tools/call","params":{"name":"hardness.unknown","arguments":{}}}', $toolsHeaders), $config);
mcpAssert(($response->getPayload()['error']['code'] ?? null) === -32601, 'unknown tool must return method not found');
mcpAssert(($toolsBootstrap->getAuditEvents()[count($toolsBootstrap->getAuditEvents()) - 1]['event'] ?? null) === 'tools_call_not_found', 'unknown tool must be audited');
$toolsBootstrap->getCompanyRepository()->company = null;
$response = $toolsBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $toolsCallBody, $toolsHeaders), $config);
mcpAssert(($response->getPayload()['result']['isError'] ?? false) === true && ($toolsBootstrap->getAuditEvents()[count($toolsBootstrap->getAuditEvents()) - 1]['event'] ?? null) === 'tools_call_company_not_found', 'missing company must return a safe tool error and audit it');
$toolsBootstrap->getCompanyRepository()->fail = true;
$response = $toolsBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $toolsCallBody, $toolsHeaders), $config);
mcpAssert(($response->getPayload()['result']['isError'] ?? false) === true && ($toolsBootstrap->getAuditEvents()[count($toolsBootstrap->getAuditEvents()) - 1]['event'] ?? null) === 'tools_call_internal_error', 'repository failure must return a safe tool error and audit it');

$boundedPresenter = new \HardnessMcp\Tools\Presentation\ToolResultPresenter(1, 256);
$boundedResult = $boundedPresenter->collection(['items' => [
    ['code' => 'AÇÚCAR', 'description' => 'Produto & especial'],
    ['code' => 'B002', 'description' => str_repeat('x', 300)],
], 'count' => 2], 'items', 'produto', 'produtos', [['path' => 'code', 'label' => 'Código'], ['path' => 'description', 'label' => 'Descrição']]);
$boundedPayload = $boundedResult->getResult();
$boundedText = (string) ($boundedPayload['content'][0]['text'] ?? '');
mcpAssert(strpos($boundedText, 'AÇÚCAR') !== false && strpos($boundedText, 'Mais 1 item(ns)') !== false, 'presenter must render special characters and disclose omitted textual items');
mcpAssert((function_exists('mb_strlen') ? mb_strlen($boundedText, 'UTF-8') : strlen($boundedText)) <= 256, 'presenter content must respect the configured safe size limit');
mcpAssert(count($boundedPayload['structuredContent']['items'] ?? []) === 2, 'presenter must never truncate structuredContent');

$schemaBootstrap = new SchemaTestBootstrap(dirname(__DIR__));
$schemaBootstrap->setScopes(['company:read']);
$fixtureCall = '{"jsonrpc":"2.0","id":27,"method":"tools/call","params":{"name":"hardness.test.schema_fixture","arguments":{"term":"abc","page":2}}}';
$response = $schemaBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $fixtureCall, $toolsHeaders), $config);
mcpAssert(($response->getPayload()['result']['structuredContent']['term'] ?? null) === 'abc', 'tools/call must execute a valid argument-bearing schema fixture');
$invalidFixtureCall = '{"jsonrpc":"2.0","id":28,"method":"tools/call","params":{"name":"hardness.test.schema_fixture","arguments":{"term":"x","page":2}}}';
$response = $schemaBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $invalidFixtureCall, $toolsHeaders), $config);
mcpAssert(($response->getPayload()['error']['code'] ?? null) === -32602 && strpos((string) ($response->getPayload()['error']['message'] ?? ''), 'campos obrigatórios') !== false, 'invalid fixture arguments must explain how to correct the safe JSON-RPC error');
mcpAssert(($schemaBootstrap->getAuditEvents()[1]['event'] ?? null) === 'tools_call_invalid_params' && ($schemaBootstrap->getAuditEvents()[1]['safe_code'] ?? null) === null, 'invalid arguments must be audited without arguments or a tool code');

$canonicalHostBootstrap = new TestBootstrap(dirname(__DIR__));
$canonicalHostBootstrap->handle(new Request('POST', '/mcp', 'CLIENTE.FASTCRUD.COM.BR:443', $initializeBody, mcpHeaders()), $config);
$expectedRateKey = 'preauth:' . hash_hmac('sha256', 'cliente.fastcrud.com.br|', 'test-rate-limit-pepper');
mcpAssert(in_array($expectedRateKey, $canonicalHostBootstrap->getRateLimitKeys(), true), 'pre-authentication rate limit must use the canonical tenant host');

$discoveryBootstrap = new TestBootstrap(dirname(__DIR__));
$response = $discoveryBootstrap->handle(new Request('GET', '/.well-known/oauth-protected-resource/mcp', 'attacker.example', '', []), $config);
$protectedMetadata = $response->getPayload();
mcpAssert($response->getStatus() === 200 && ($response->getHeaders()['Content-Type'] ?? null) === 'application/json; charset=utf-8', 'protected-resource metadata must be public JSON over GET');
mcpAssert(($protectedMetadata['resource'] ?? null) === 'https://cliente.fastcrud.com.br/mcp' && ($protectedMetadata['authorization_servers'] ?? null) === ['https://cliente.fastcrud.com.br'], 'protected-resource metadata must use configured canonical URLs, never the request host');
mcpAssert(($protectedMetadata['scopes_supported'] ?? null) === ['commercial:opportunities:read', 'company:read', 'finance:payables:read', 'finance:receivables:read', 'ide_smt:quote:order:generate', 'order:item-report:read', 'order:report:read', 'partner:lookup', 'partner:search', 'product:conversion-report:read', 'product:partner-reference:lookup', 'product:prices:apply', 'product:prices:export', 'product:prices:history', 'product:prices:prepare', 'product:prices:reverse', 'product:read', 'quote:item-report:read', 'quote:item:add', 'quote:item:delete', 'quote:item:update', 'quote:read', 'quote:report:read', 'quote:write', 'sales:item-report:read', 'sales:read'] && ($protectedMetadata['bearer_methods_supported'] ?? null) === ['header'], 'protected-resource metadata must advertise the active MCP scope catalogue and bearer header method');
$fallbackProtected = $discoveryBootstrap->handle(new Request('GET', '/.well-known/oauth-protected-resource', 'cliente.fastcrud.com.br', '', []), $config);
mcpAssert($fallbackProtected->getStatus() === 200 && $fallbackProtected->getPayload() === $protectedMetadata, 'protected-resource fallback metadata must match the /mcp variant');
$connectionOnePdo = new TestAdminPdo();
$connectionOneDiscovery = new TestBootstrap(dirname(__DIR__));
$connectionOneDiscovery->setEndpointPdo($connectionOnePdo);
$connectionOneMetadataPath = '/.well-known/oauth-protected-resource/mcp/c/' . $connectionOnePdo->connectionPublicId;
$connectionOneMetadata = $connectionOneDiscovery->handle(new Request('GET', $connectionOneMetadataPath, 'attacker.example', '', []), $config);
mcpAssert($connectionOneMetadata->getStatus() === 200 && ($connectionOneMetadata->getPayload()['resource'] ?? null) === 'https://cliente.fastcrud.com.br/mcp/c/' . $connectionOnePdo->connectionPublicId, 'connection-specific metadata must use the exact canonical MCP resource URL.');
$connectionTwoPdo = new TestAdminPdo();
$connectionTwoPdo->connectionPublicId = 'ZyXwVuTsRqPoNmLkJiHgFeDc';
$connectionTwoDiscovery = new TestBootstrap(dirname(__DIR__));
$connectionTwoDiscovery->setEndpointPdo($connectionTwoPdo);
$connectionTwoMetadata = $connectionTwoDiscovery->handle(new Request('GET', '/.well-known/oauth-protected-resource/mcp/c/' . $connectionTwoPdo->connectionPublicId, 'attacker.example', '', []), $config);
mcpAssert($connectionTwoMetadata->getStatus() === 200 && ($connectionTwoMetadata->getPayload()['resource'] ?? null) === 'https://cliente.fastcrud.com.br/mcp/c/' . $connectionTwoPdo->connectionPublicId && $connectionTwoMetadata->getPayload() !== $connectionOneMetadata->getPayload(), 'two active MCP connections must publish distinct protected-resource metadata.');
foreach (['/.well-known/oauth-protected-resource/mcp/c/short', '/.well-known/oauth-protected-resource/mcp/c/AbCdEfGhIjKlMnOpQrStUvW!', '/.well-known/oauth-protected-resource/mcp/c/ZyXwVuTsRqPoNmLkJiHgFeDc'] as $invalidConnectionMetadataPath) {
    $invalidConnectionMetadata = $connectionOneDiscovery->handle(new Request('GET', $invalidConnectionMetadataPath, 'attacker.example', '', []), $config);
    mcpAssert($invalidConnectionMetadata->getStatus() === 404 && $invalidConnectionMetadata->getPayload() === null, 'invalid or unknown connection-specific metadata must return 404.');
}
$exclusiveChallengePdo = new TestAdminPdo();
$exclusiveChallengeBootstrap = new TestBootstrap(dirname(__DIR__));
$exclusiveChallengeBootstrap->setEndpointPdo($exclusiveChallengePdo);
$exclusiveChallengeBootstrap->setAuthenticated(false);
$exclusiveChallenge = $exclusiveChallengeBootstrap->handle(new Request('POST', '/mcp/c/' . $exclusiveChallengePdo->connectionPublicId, 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders()), $config);
mcpAssert($exclusiveChallenge->getStatus() === 401 && ($exclusiveChallenge->getHeaders()['WWW-Authenticate'] ?? null) === 'Bearer resource_metadata="https://cliente.fastcrud.com.br/.well-known/oauth-protected-resource/mcp/c/' . $exclusiveChallengePdo->connectionPublicId . '"', 'an unauthenticated connection-specific request must advertise its exact protected-resource metadata URL.');
$authorizationMetadata = $discoveryBootstrap->handle(new Request('GET', '/.well-known/oauth-authorization-server', 'cliente.fastcrud.com.br', '', []), $config)->getPayload();
mcpAssert(($authorizationMetadata['issuer'] ?? null) === 'https://cliente.fastcrud.com.br' && ($authorizationMetadata['authorization_endpoint'] ?? null) === 'https://cliente.fastcrud.com.br/oauth/authorize' && ($authorizationMetadata['token_endpoint'] ?? null) === 'https://cliente.fastcrud.com.br/oauth/token', 'authorization-server metadata must expose the planned canonical OAuth endpoints');
mcpAssert(($authorizationMetadata['response_types_supported'] ?? null) === ['code'] && ($authorizationMetadata['grant_types_supported'] ?? null) === ['authorization_code', 'refresh_token'] && ($authorizationMetadata['token_endpoint_auth_methods_supported'] ?? null) === ['none'] && ($authorizationMetadata['code_challenge_methods_supported'] ?? null) === ['S256'], 'authorization-server metadata must advertise only code flow, refresh tokens, public clients and S256 PKCE');
mcpAssert(($authorizationMetadata['registration_endpoint'] ?? null) === 'https://cliente.fastcrud.com.br/oauth/register' && !isset($authorizationMetadata['revocation_endpoint']) && !isset($authorizationMetadata['cimd']), 'metadata must announce the implemented DCR endpoint but not revocation, CIMD, or other unimplemented capabilities');
$openidMetadata = $discoveryBootstrap->handle(new Request('GET', '/.well-known/openid-configuration', 'cliente.fastcrud.com.br', '', []), $config);
mcpAssert($openidMetadata->getStatus() === 404 && $openidMetadata->getPayload() === null, 'OpenID discovery must return controlled 404 because this server does not implement OIDC');
$discoveryMethod = $discoveryBootstrap->handle(new Request('POST', '/.well-known/oauth-authorization-server', 'cliente.fastcrud.com.br', '', []), $config);
mcpAssert($discoveryMethod->getStatus() === 405 && ($discoveryMethod->getHeaders()['Allow'] ?? null) === 'GET', 'discovery endpoints must reject non-GET methods');

$limitedBootstrap = new TestBootstrap(dirname(__DIR__));
$limitedBootstrap->setDenyRateLimit(true);
$response = $limitedBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders()), $config);
mcpAssert($response->getStatus() === 429 && $response->getPayload() === null && ($response->getHeaders()['Retry-After'] ?? null) === '12', 'rate limit rejection must return empty HTTP 429 with Retry-After');
mcpAssert(($limitedBootstrap->getAuditEvents()[0]['event'] ?? null) === 'rate_limited_preauthentication' && ($limitedBootstrap->getAuditEvents()[0]['status'] ?? null) === 429, 'pre-authentication 429 must be audited with final status');

$unauthenticatedBootstrap = new TestBootstrap(dirname(__DIR__));
$unauthenticatedBootstrap->setAuthenticated(false);
$response = $unauthenticatedBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders()), $config);
mcpAssert($response->getStatus() === 401 && $response->getPayload() === null && ($response->getHeaders()['WWW-Authenticate'] ?? null) === 'Bearer resource_metadata="https://cliente.fastcrud.com.br/.well-known/oauth-protected-resource/mcp"', 'missing or invalid credential must return empty HTTP 401 with canonical protected-resource metadata');
$response = $unauthenticatedBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '', mcpHeaders()), $config);
mcpAssert($response->getStatus() === 401 && $response->getPayload() === null, 'authentication must occur before parsing an empty body');
$response = $unauthenticatedBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '{', mcpHeaders()), $config);
mcpAssert($response->getStatus() === 401 && $response->getPayload() === null, 'authentication must occur before parsing malformed JSON');
$response = $unauthenticatedBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '', []), $config);
mcpAssert($response->getStatus() === 401 && ($response->getHeaders()['WWW-Authenticate'] ?? null) === 'Bearer resource_metadata="https://cliente.fastcrud.com.br/.well-known/oauth-protected-resource/mcp"', 'missing credentials must be challenged before MCP Accept or content-type validation');

$unavailableRateConfig = $config;
$unavailableRateConfig['rate_limit']['driver'] = 'invalid';
$response = (new UnavailableRateBootstrap(dirname(__DIR__)))->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders()), $unavailableRateConfig);
mcpAssert($response->getStatus() === 503 && $response->getPayload() === null, 'unavailable configured rate limiter must fail closed with HTTP 503');

$otherVersionBody = str_replace('2025-11-25', '2024-11-05', $initializeBody);
$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $otherVersionBody, mcpHeaders()), $config);
mcpAssert(($response->getPayload()['result']['protocolVersion'] ?? null) === '2025-11-25', 'initialize must negotiate to the supported version');

$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '{', mcpHeaders()), $config);
mcpAssert(($response->getPayload()['error']['code'] ?? null) === -32700, 'invalid JSON must return parse error');

$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '{"jsonrpc":"1.0","id":1,"method":"initialize","params":{}}', mcpHeaders()), $config);
mcpAssert(($response->getPayload()['error']['code'] ?? null) === -32600, 'invalid JSON-RPC version must return invalid request');

$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '{"jsonrpc":"2.0","id":1,"method":"unknown/method"}', mcpHeaders(['MCP-Protocol-Version' => '2025-11-25'])), $config);
mcpAssert(($response->getPayload()['error']['code'] ?? null) === -32601, 'unknown request must return method not found');

$initializedBody = '{"jsonrpc":"2.0","method":"notifications/initialized"}';
$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializedBody, mcpHeaders(['MCP-Protocol-Version' => '2025-11-25'])), $config);
mcpAssert($response->getStatus() === 202 && $response->getPayload() === null, 'initialized notification must return HTTP 202 without a body');

$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '{"jsonrpc":"2.0","method":"notifications/unknown"}', mcpHeaders(['MCP-Protocol-Version' => '2025-11-25'])), $config);
mcpAssert($response->getStatus() === 202 && $response->getPayload() === null, 'unknown valid notification must be accepted without a JSON-RPC body');

$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', '{"jsonrpc":"1.0","method":"notifications/unknown"}', mcpHeaders(['MCP-Protocol-Version' => '2025-11-25'])), $config);
mcpAssert($response->getStatus() === 400 && $response->getPayload() === null, 'structurally invalid notification must be rejected without a JSON-RPC body');

$response = $bootstrap->handle(new Request('GET', '/mcp', 'cliente.fastcrud.com.br', '', []), $config);
mcpAssert($response->getStatus() === 405 && ($response->getHeaders()['Allow'] ?? null) === 'POST', 'GET must return 405 and Allow: POST while SSE is unavailable');
mcpAssert(($response->getHeaders()['Content-Type'] ?? null) === 'application/json; charset=utf-8' && $response->getPayload() === null, 'empty responses must not inherit PHP/Apache text/html content type');
$response = $bootstrap->handle(new Request('DELETE', '/mcp', 'cliente.fastcrud.com.br', '', []), $config);
mcpAssert($response->getStatus() === 405 && ($response->getHeaders()['Allow'] ?? null) === 'POST', 'DELETE must return 405 and Allow: POST without sessions');

$response = $bootstrap->handle(new Request('GET', '/oauth/token', 'cliente.fastcrud.com.br', '', []), $config);
mcpAssert($response->getStatus() === 405 && ($response->getHeaders()['Allow'] ?? null) === 'POST', 'GET /oauth/token must return 405 and Allow: POST');

$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, ['Accept' => 'application/json, text/event-stream']), $config);
mcpAssert($response->getStatus() === 415 && $response->getPayload() === null, 'POST without Content-Type must be rejected before tenant authentication');
$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, ['Accept' => 'application/json, text/event-stream', 'Content-Type' => 'text/plain']), $config);
mcpAssert($response->getStatus() === 415 && $response->getPayload() === null, 'POST with a non-JSON Content-Type must be rejected');
$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, ['Accept' => 'application/json, text/event-stream', 'Content-Type' => 'Application/JSON; charset=UTF-8']), $config);
mcpAssert($response->getStatus() === 200, 'POST must accept application/json with parameters case-insensitively');

$originConfig = $config;
$originConfig['transport']['allowed_origins'] = ['https://console.example.com'];
$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders(['Origin' => 'https://console.example.com'])), $originConfig);
mcpAssert($response->getStatus() === 200, 'configured Origin must be accepted');
$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders(['Origin' => 'https://attacker.example'])), $originConfig);
mcpAssert($response->getStatus() === 403 && $response->getPayload() === null, 'unconfigured Origin must return 403 without details');

$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializedBody, mcpHeaders(['MCP-Protocol-Version' => '2025-11-25'])), $config);
mcpAssert($response->getStatus() === 202, 'supported MCP-Protocol-Version must be accepted');
$response = $bootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializedBody, mcpHeaders(['MCP-Protocol-Version' => '2024-11-05'])), $config);
mcpAssert($response->getStatus() === 400 && $response->getPayload() === null, 'unsupported MCP-Protocol-Version notification must be rejected without a body');

$directory = sys_get_temp_dir() . '/hardness-mcp-test-' . bin2hex(random_bytes(6));
mkdir($directory, 0700, true);
$configurationFile = $directory . '/cliente.fastcrud.com.br-confUsuario.php';
$sideEffectFile = $directory . '/configuration-was-executed';
file_put_contents($configurationFile, '<?php file_put_contents(' . var_export($sideEffectFile, true) . ', "executed"); $confUsuario = ["dbDatabase" => "tenant", "dbHost" => "db", "dbUser" => "user", "dbPass" => "pass"]; $confUsuarioEspelhado = ["dbDatabase" => "tenant", "dbHost" => "replica", "dbUser" => "read_user", "dbPass" => "read_pass"];');

try {
    $tenantConfig = $config;
    $tenantConfig['tenant']['conf_path'] = $directory;
    $tenant = (new TenantResolver($tenantConfig))->resolve('CLIENTE.FASTCRUD.COM.BR:443');
    mcpAssert(($tenant['dbDatabase'] ?? null) === 'tenant', 'tenant resolver must load the existing host configuration');
    mcpAssert(($tenant['readDbHost'] ?? null) === 'replica' && ($tenant['readDbUser'] ?? null) === 'read_user', 'tenant resolver must expose an optional read replica without executing tenant configuration');
    mcpAssert(!file_exists($sideEffectFile), 'tenant resolver must not execute tenant configuration PHP or start a session');

    foreach (['localhost', '127.0.0.1', '../etc/passwd'] as $invalidHost) {
        try {
            (new TenantResolver($tenantConfig))->resolve($invalidHost);
            throw new RuntimeException('invalid host should have failed: ' . $invalidHost);
        } catch (McpException $exception) {
            mcpAssert($exception->getMessage() === 'Unable to resolve tenant.', 'tenant errors must not expose filesystem details');
        }
    }
} finally {
    unlink($configurationFile);
    if (file_exists($sideEffectFile)) { unlink($sideEffectFile); }
    rmdir($directory);
}

try {
    (new ConnectionFactory($config))->create([
        'host' => 'test',
        'dbDatabase' => 'tenant',
        'dbHost' => '127.0.0.1:1',
        'dbUser' => 'user',
        'dbPass' => 'secret',
    ]);
    throw new RuntimeException('database connection should have failed');
} catch (McpException $exception) {
    mcpAssert($exception->getMessage() === 'Unable to connect to the tenant database.', 'database errors must not expose connection details');
}

$failingBootstrap = new TestBootstrap(dirname(__DIR__));
$failingBootstrap->failConnection();
$response = $failingBootstrap->handle(new Request('POST', '/mcp', 'cliente.fastcrud.com.br', $initializeBody, mcpHeaders()), $config);
mcpAssert($response->getStatus() === 500, 'tenant connection failure must return HTTP 500');
mcpAssert(strpos(json_encode($response->getPayload()), '127.0.0.1') === false, 'connection failure response must not expose infrastructure details');

require __DIR__ . '/admin-foundation.php';
require __DIR__ . '/OAuth/foundation.php';
require __DIR__ . '/Eligibility/foundation.php';
require __DIR__ . '/QuoteHeader/foundation.php';
require __DIR__ . '/Partner/CodeFlowFoundation.php';
require __DIR__ . '/DocumentSummary/foundation.php';
require __DIR__ . '/IdeSmtCapability/foundation.php';
require __DIR__ . '/IdeSmtGenerateOrder/foundation.php';
require __DIR__ . '/IdeSmtTool/foundation.php';
require __DIR__ . '/ReportPeriod/foundation.php';

echo "MCP stages 1–7, administrative stages 4.1–4.5 and OAuth foundation tests passed.\n";
