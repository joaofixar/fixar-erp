<?php

declare(strict_types=1);

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Partner\Domain\PartnerRepositoryInterface;
use HardnessMcp\Tools\Partner\Get\PartnerGetTool;
use HardnessMcp\Tools\Partner\GetByDocument\PartnerGetByDocumentTool;
use HardnessMcp\Tools\Partner\Search\PartnerCandidateReference;
use HardnessMcp\Tools\Partner\Search\PartnerSearchHandler;
use HardnessMcp\Tools\Partner\Search\PartnerSearchTool;
use HardnessMcp\Tools\Quote\Create\QuoteCreateGatewayInterface;
use HardnessMcp\Tools\Quote\Create\QuoteCreateTool;

$partnerRows = [
    7 => ['D024_Id' => 7, 'D024_Nome_Empresa' => 'Atom Indústria Ltda', 'D024_Nome_Fantasia' => 'ATOM', 'D024_Cnpj' => '12.345.678/0001-90', 'D024_Cpf' => '', 'D024_Flag_Pessoa_Fisica_Juridica' => 'J', 'D024_Flag_Ativo' => 'S', 'D024_Flag_Cliente' => 'S', 'D024_Flag_Fornecedor' => 'N', 'D024_Endereco' => 'Rua Teste', 'D024_Numero' => '10', 'D024_Complemento' => '', 'D024_Bairro' => 'Centro', 'D024_Cidade' => 'São Paulo', 'D024_Estado' => 'SP', 'D024_Cep' => '01000-000'],
    8 => ['D024_Id' => 8, 'D024_Nome_Empresa' => 'Atom Comércio Ltda', 'D024_Nome_Fantasia' => 'ATOM', 'D024_Cnpj' => '12.345.678/0001-90', 'D024_Cpf' => '', 'D024_Flag_Pessoa_Fisica_Juridica' => 'J', 'D024_Flag_Ativo' => 'S', 'D024_Flag_Cliente' => 'S', 'D024_Flag_Fornecedor' => 'N', 'D024_Endereco' => 'Rua Dois', 'D024_Numero' => '20', 'D024_Complemento' => '', 'D024_Bairro' => 'Centro', 'D024_Cidade' => 'São Paulo', 'D024_Estado' => 'SP', 'D024_Cep' => '01000-001'],
];
$codeRepository = new class($partnerRows) implements PartnerRepositoryInterface {
    /** @var array<int,array<string,mixed>> */ private array $rows;
    /** @param array<int,array<string,mixed>> $rows */ public function __construct(array $rows) { $this->rows = $rows; }
    public function searchByDocument(int $companyId, string $document, string $role, int $limit): array { return $companyId === 3 && $document === '12345678000190' ? array_values($this->rows) : []; }
    public function searchByName(int $companyId, string $query, string $role, int $limit): array { return $companyId === 3 && $query === 'Atom Indústria' ? array_values($this->rows) : []; }
    public function findVisibleById(int $companyId, int $partnerId): ?array { return $companyId === 3 && isset($this->rows[$partnerId]) ? $this->rows[$partnerId] : null; }
};
$singleCodeRepository = new class($partnerRows[7]) implements PartnerRepositoryInterface {
    /** @var array<string,mixed> */ private array $row;
    /** @param array<string,mixed> $row */ public function __construct(array $row) { $this->row = $row; }
    public function searchByDocument(int $companyId, string $document, string $role, int $limit): array { return $companyId === 3 && $document === '12345678000190' ? [$this->row] : []; }
    public function searchByName(int $companyId, string $query, string $role, int $limit): array { return $companyId === 3 && $query === 'Atom Indústria' ? [$this->row] : []; }
    public function findVisibleById(int $companyId, int $partnerId): ?array { return $companyId === 3 && $partnerId === 7 ? $this->row : null; }
};
$references = new PartnerCandidateReference('partner-code-flow-test-key');
$apiContext = new ExecutionContext(41, 1, 3, 9, ['partner:search', 'partner:lookup', 'quote:write'], 'api_key', null, null, null, null, true);
$oauthContext = new ExecutionContext(42, 2, 3, 9, ['partner:search', 'partner:lookup', 'quote:write'], 'oauth', 7, 8, 9);

$search = (new PartnerSearchTool(new PartnerSearchHandler($codeRepository, $references)))->execute(['criterion' => 'name', 'query' => 'Atom Indústria', 'role' => 'customer'], $apiContext)->getResult();
$searchItems = $search['structuredContent']['items'] ?? [];
mcpAssert(count($searchItems) === 2 && !array_key_exists('code', $searchItems[0]) && !array_key_exists('code', $searchItems[1]), 'partner search candidates must not expose a partner code.');
$get = (new PartnerGetTool($codeRepository, $references))->execute(['candidate_ref' => $searchItems[0]['candidate_ref']], $apiContext)->getResult();
mcpAssert(($get['structuredContent']['code'] ?? null) === 7 && strpos((string) ($get['content'][0]['text'] ?? ''), 'Código: 7') !== false, 'partner get must expose the selected D024 identifier as code in structured and textual output.');
$crossCompany = (new PartnerGetTool($codeRepository, $references))->execute(['candidate_ref' => $searchItems[0]['candidate_ref']], new ExecutionContext(41, 1, 4, 9, ['partner:search'], 'api_key', null, null, null, null, true))->getResult();
mcpAssert(($crossCompany['structuredContent']['error']['code'] ?? null) === 'partner_candidate_invalid', 'partner candidate references must not cross companies.');

$getByDocument = new PartnerGetByDocumentTool($singleCodeRepository, $references);
$documentResult = $getByDocument->execute(['document' => '12.345.678/0001-90', 'role' => 'customer'], $oauthContext)->getResult();
mcpAssert(($documentResult['structuredContent']['code'] ?? null) === 7 && strpos((string) ($documentResult['content'][0]['text'] ?? ''), 'Código: 7') !== false, 'an unambiguous document lookup must expose the code for OAuth callers.');
$duplicateTool = new PartnerGetByDocumentTool($codeRepository, $references);
$duplicateResult = $duplicateTool->execute(['document' => '12.345.678/0001-90', 'role' => 'customer'], $apiContext)->getResult();
$duplicateItems = $duplicateResult['structuredContent']['items'] ?? [];
mcpAssert(count($duplicateItems) === 2 && !isset($duplicateItems[0]['code']) && isset($duplicateItems[0]['candidate_ref']), 'duplicate document matches must not choose or expose a code before selection.');
$selectedDocumentResult = $duplicateTool->execute(['document' => '12.345.678/0001-90', 'role' => 'customer', 'candidate_ref' => $duplicateItems[0]['candidate_ref']], $apiContext)->getResult();
mcpAssert(($selectedDocumentResult['structuredContent']['code'] ?? null) === 7, 'the selected duplicate document candidate must expose only its own code.');

$quoteGateway = new class implements QuoteCreateGatewayInterface {
    /** @var array<string,mixed>|null */ public ?array $arguments = null;
    public ?ExecutionContext $context = null;
    public function create(array $arguments, ExecutionContext $context): array { $this->arguments = $arguments; $this->context = $context; return ['success' => true, 'quote_id' => 901, 'items_count' => count($arguments['items'] ?? [])]; }
};
$quoteResult = (new QuoteCreateTool($quoteGateway))->execute(['customer_id' => $selectedDocumentResult['structuredContent']['code'], 'items' => [['product_id' => 34, 'quantity' => 1]]], $apiContext)->getResult();
mcpAssert(($quoteGateway->arguments['customer_id'] ?? null) === 7 && ($quoteResult['structuredContent']['success'] ?? false) === true, 'a code returned by partner lookup must be accepted as customer_id by quote create.');
$oauthQuoteResult = (new QuoteCreateTool($quoteGateway))->execute(['customer_id' => $documentResult['structuredContent']['code'], 'items' => [['product_id' => 34, 'quantity' => 1]]], $oauthContext)->getResult();
mcpAssert($quoteGateway->context === $oauthContext && ($oauthQuoteResult['structuredContent']['success'] ?? false) === true, 'a code returned by document lookup must be accepted by quote create for OAuth callers.');

$ven001Source = file_get_contents(dirname(__DIR__, 3) . '/bibliotecas/classes/VEN001.php');
mcpAssert($ven001Source !== false && strpos($ven001Source, "D024_Flag_Cliente = 'S'") !== false && strpos($ven001Source, "D024_Flag_Ativo = 'S'") !== false && strpos($ven001Source, "separarClientePorEmpresa") !== false, 'quote creation must retain the ERP validation for existing, active customers and the configured company-separation rule.');
$getByDocumentSource = file_get_contents(dirname(__DIR__, 3) . '/mcp/src/Tools/Partner/GetByDocument/PartnerGetByDocumentTool.php');
mcpAssert($getByDocumentSource !== false && strpos($getByDocumentSource, 'searchByDocument') !== false && strpos($getByDocumentSource, 'searchByName') === false, 'document lookup must not gain textual search capability.');
