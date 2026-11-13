<?php

declare(strict_types=1);

use HardnessMcp\Capabilities\CapabilityDefinition;
use HardnessMcp\Capabilities\CapabilityProbeInterface;
use HardnessMcp\Capabilities\CapabilityRegistry;
use HardnessMcp\Capabilities\InstallationCapabilityCatalog;
use HardnessMcp\Capabilities\RequiredColumns;
use HardnessMcp\Capabilities\RequiredInstallationProfile;
use HardnessMcp\Capabilities\RequiredRecord;
use HardnessMcp\Scopes\ScopeCatalog;
use HardnessMcp\Tools\Logistics\LoadReportDefinition;
use HardnessMcp\Tools\Logistics\LoadReportInput;
use HardnessMcp\Tools\PublicToolNameMap;
use HardnessMcp\Tools\Inecel\Certificate\InecelCertificateTraceRepositoryInterface;
use HardnessMcp\Tools\Inecel\Certificate\InecelCertificateDocumentRepositoryInterface;
use HardnessMcp\Tools\Inecel\Certificate\InecelCertificateTraceTool;
use HardnessMcp\Tools\Inecel\Certificate\InecelCertificateReadTool;
use HardnessMcp\Tools\Inecel\Certificate\CertificateDownloadLinkService;
use HardnessMcp\Tools\Inecel\Certificate\CertificateOcrBusyException;
use HardnessMcp\Tools\Inecel\Certificate\PdfTextExtractorInterface;
use HardnessMcp\Context\ExecutionContext;

final class TestCapabilityProbe implements CapabilityProbeInterface
{
    /** @var array<string,true> */ private array $records;
    /** @var array<string,array<string,true>> */ private array $columns;
    private ?string $installationProfile;
    /** @param string[] $records @param array<string,string[]> $columns */
    public function __construct(array $records, array $columns, ?string $installationProfile = null)
    {
        $this->records = array_fill_keys(array_map('strtoupper', $records), true);
        $this->columns = [];
        foreach ($columns as $table => $items) { $this->columns[strtoupper($table)] = array_fill_keys(array_map('strtoupper', $items), true); }
        $this->installationProfile = $installationProfile;
    }
    public function hasInstallationProfile(string $profile): bool { return $this->installationProfile !== null && hash_equals($profile, $this->installationProfile); }
    public function hasColumns(string $table, array $columns): bool
    {
        $available = $this->columns[strtoupper($table)] ?? [];
        foreach ($columns as $column) { if (!isset($available[strtoupper((string) $column)])) { return false; } }
        return $columns !== [];
    }
    public function hasRecord(string $table, string $column, $value): bool { return isset($this->records[strtoupper($table . '.' . $column . '=' . (string) $value)]); }
}

$logisticsColumns = [
    'SMT071' => ['SMT071_Id', 'SMT071_C004_Id', 'SMT071_Descricao', 'SMT071_Flag_Status', 'SMT071_Data_Criacao', 'SMT071_Data_Previsao', 'SMT071_Hora_Previsao', 'SMT071_D109_Id', 'SMT071_D022_Id', 'SMT071_D170_Id', 'SMT071_Valor_Total', 'SMT071_Peso_Total', 'SMT071_Valor_Frete_Incluso', 'SMT071_Valor_Fechado', 'SMT071_Km_Rodado'],
    'SMT072' => ['SMT072_Id', 'SMT072_SMT071_Id', 'SMT072_T005_Id', 'SMT072_Sequecia_Entrega', 'SMT072_Valor_Pedido', 'SMT072_Peso_Pedido', 'SMT072_Guia_St', 'SMT072_Contrata_Auxiliar_Descarga', 'SMT072_Valor_Axiliar_Descarga'],
    'T005' => ['T005_Id', 'T005_C004_Id', 'T005_D024_Id', 'T005_D148_Id_Entrega', 'T005_Data_Emissao', 'T005_Data_Entrega', 'T005_Valor_Frete', 'T005_Prazos_Pagamentos', 'T005_Nome_Status'],
    'T005A' => ['T005A_T005_Id', 'T005A_D148_Id_Redespacho', 'T005A_Valor_Frete_Itens'],
    'D024' => ['D024_Id', 'D024_C004_Id', 'D024_D018_Id', 'D024_D020_Id', 'D024_Nome_Empresa', 'D024_Nome_Fantasia', 'D024_Cidade', 'D024_Estado', 'D024_Endereco', 'D024_Numero', 'D024_Bairro', 'D024_Cep'],
    'D148' => ['D148_Id', 'D148_D018_Id', 'D148_D020_Id', 'D148_Rua', 'D148_Numero', 'D148_Bairro', 'D148_Cep'],
    'D018' => ['D018_Id', 'D018_UF'],
    'D020' => ['D020_Id', 'D020_D084_Id', 'D020_Nome_Cidade'],
    'D084' => ['D084_Id', 'D084_Regiao'],
    'T007' => ['T007_Id', 'T007_T005_Id', 'T007_C004_Id', 'T007_Numero_Nota_Fiscal', 'T007_Serie', 'T007_Data_Emissao', 'T007_Valor_Total', 'T007_Chave_Acesso_Nfe', 'T007_Flag_Cancelada', 'T007_Cliente_Razao', 'T007_Cliente_Cidade', 'T007_Cliente_Estado', 'T007_Cliente_Endereco', 'T007_Cliente_Endereco_Numero', 'T007_Cliente_Bairro', 'T007_Cliente_CEP'],
    'D109' => ['D109_Id', 'D109_Nome'],
    'D022' => ['D022_Id', 'D022_Nome_Empresa', 'D022_Nome_Fantasia'],
    'D170' => ['D170_Id', 'D170_Identificacao_Veiculo', 'D170_Placa', 'D170_Capacidade_Kg', 'D170_Capacidade_M3'],
    'D170A' => ['D170A_D170_Id', 'D170A_Comprimento_Carroceria'],
    'SMT171' => ['SMT171_Id', 'SMT171_SMT071_Id', 'SMT171_C004_Id', 'SMT171_Data'],
];
$availableCapabilities = InstallationCapabilityCatalog::create()->resolve(new TestCapabilityProbe(['C029.C029_Id=286'], $logisticsColumns));
mcpAssert($availableCapabilities->has('logistics.loads'), 'logistics capability must require the complete partner load signature');
mcpAssert($availableCapabilities->has('logistics.load.order_context'), 'order enrichment must require the complete standard order and invoice signature');
mcpAssert($availableCapabilities->has('logistics.load.resources'), 'resource enrichment must require the complete logistics resource signature');

$missingPermission = InstallationCapabilityCatalog::create()->resolve(new TestCapabilityProbe([], $logisticsColumns));
mcpAssert(!$missingPermission->has('logistics.loads'), 'logistics capability must remain unavailable without the legacy menu record');

$partialColumns = $logisticsColumns;
array_pop($partialColumns['SMT072']);
$partialCapabilities = InstallationCapabilityCatalog::create()->resolve(new TestCapabilityProbe(['C029.C029_Id=286'], $partialColumns));
mcpAssert(!$partialCapabilities->has('logistics.loads'), 'logistics capability must remain unavailable on a partial schema');
$withoutInvoiceColumns = $logisticsColumns;
unset($withoutInvoiceColumns['T007']);
$withoutInvoices = InstallationCapabilityCatalog::create()->resolve(new TestCapabilityProbe(['C029.C029_Id=286'], $withoutInvoiceColumns));
mcpAssert($withoutInvoices->has('logistics.loads') && !$withoutInvoices->has('logistics.load.order_context'), 'order enrichment must not disable the core logistics tools');
$resolvedScopes = (new ScopeCatalog($availableCapabilities))->all();
$defaultScopes = (new ScopeCatalog())->all();
mcpAssert(count($resolvedScopes) === count($defaultScopes) + 1, 'resolved capabilities must add only the matching logistics scope');
mcpAssert(!in_array('logistics.load:read', array_column($defaultScopes, 'id'), true), 'capability-gated scope must fail closed without a resolved installation');
$logisticsScope = array_values(array_filter($resolvedScopes, static fn(array $scope): bool => $scope['id'] === 'logistics.load:read'))[0] ?? null;
mcpAssert(is_array($logisticsScope) && $logisticsScope['tools'] === ['hardness.logistics.load.summary', 'hardness.logistics.load.list', 'hardness.logistics.load.get_by_id'], 'logistics scope must describe the complete read-only tool set');
mcpAssert(LoadReportDefinition::summary()->getRequiredScopes() === ['logistics.load:read'] && LoadReportDefinition::listing()->getRequiredScopes() === ['logistics.load:read'] && LoadReportDefinition::detail()->getRequiredScopes() === ['logistics.load:read'], 'every logistics tool must require the dedicated least-privilege scope');
mcpAssert(PublicToolNameMap::publicName('hardness.logistics.load.get_by_id') === 'hardness_logistics_load_get_by_id', 'logistics tool must have a grammar-safe public name');
try { LoadReportInput::period(['date_from' => '2025-01-01', 'date_to' => '2026-02-01'], false); throw new RuntimeException('oversized logistics period must fail'); }
catch (InvalidArgumentException $exception) { mcpAssert($exception->getMessage() === 'Consultas de cargas aceitam um período de até 12 meses.', 'logistics period boundary must be explicit'); }
mcpAssert(preg_match('/\A[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)?(?::[a-z][a-z0-9_-]*){1,3}\z/', 'logistics.load:read') === 1, 'tool catalogue scope grammar must accept one dotted subdomain');

try {
    new CapabilityRegistry([new CapabilityDefinition('test.feature', [new RequiredRecord('C029', 'C029_Id', 1)]), new CapabilityDefinition('test.feature', [new RequiredColumns('D001', ['D001_Id'])])]);
    throw new RuntimeException('duplicate capabilities must fail');
} catch (InvalidArgumentException $exception) {
    mcpAssert($exception->getMessage() === 'Invalid capability registry.', 'capability registry must reject duplicate ids');
}

$customerCapability = new CapabilityRegistry([
    new CapabilityDefinition('customer.inecel.process_analysis', [
        new RequiredInstallationProfile('inecel-process-analysis-v1'),
        new RequiredColumns('INECEL001', ['INECEL001_Id', 'INECEL001_C004_Id']),
    ]),
]);
mcpAssert($customerCapability->resolve(new TestCapabilityProbe([], ['INECEL001' => ['INECEL001_Id', 'INECEL001_C004_Id']], 'inecel-process-analysis-v1'))->has('customer.inecel.process_analysis'), 'customer capability must require its explicit installation profile and complete schema signature');
mcpAssert(!$customerCapability->resolve(new TestCapabilityProbe([], ['INECEL001' => ['INECEL001_Id', 'INECEL001_C004_Id']], 'another-customer-v1'))->has('customer.inecel.process_analysis'), 'customer capability must remain unavailable for another installation despite an identical schema');
mcpAssert(!$customerCapability->resolve(new TestCapabilityProbe([], ['INECEL001' => ['INECEL001_Id']], 'inecel-process-analysis-v1'))->has('customer.inecel.process_analysis'), 'customer capability must remain unavailable when its schema signature is incomplete');

$inecelColumns = [
    'T007' => ['T007_Id', 'T007_C004_Id', 'T007_Numero_Nota_Fiscal', 'T007_Chave_Acesso_Nfe'],
    'T008' => ['T008_Id', 'T008_T007_Id', 'T008_D009_Id', 'T008_C004_Id', 'T008_T014_Id'],
    'T001' => ['T001_Id', 'T001_T007_Id', 'T001_T008_Id', 'T001_T013_Id', 'T001_T014_Id', 'T001_D009_Id', 'T001_C004_Id'],
    'T001A' => ['T001A_Id', 'T001A_T001_Id', 'T001A_Lote', 'T001A_T255_Id'],
    'T255' => ['T255_Id', 'T255_C004_Id'], 'T264' => ['T264_Id', 'T264_T255_Id', 'T264_D009_Id'],
    'T013' => ['T013_Id', 'T013_C004_Id', 'T013_Numero_Nota_Fiscal', 'T013_Data_Entrada', 'T013_Chave_Acesso_Nfe'],
    'T014' => ['T014_Id', 'T014_T013_Id', 'T014_D009_Id', 'T014_C004_Id', 'T014_Codigo_Produto', 'T014_Descricao_Produto'],
    'SMT205' => ['SMT205_Id', 'SMT205_T013_Id', 'SMT205_C007_Id', 'SMT205_Nome_Arquivo', 'SMT205_Descricao', 'SMT205_Data_Hora_Upload'],
];
$inecelCapabilities = InstallationCapabilityCatalog::create()->resolve(new TestCapabilityProbe([], $inecelColumns, 'inecel-process-analysis-v1'));
mcpAssert($inecelCapabilities->has('customer.inecel.certificate_trace'), 'Inecel certificate trace must require the exact profile and complete trace schema');
mcpAssert(!(InstallationCapabilityCatalog::create()->resolve(new TestCapabilityProbe([], $inecelColumns, 'another-customer-v1')))->has('customer.inecel.certificate_trace'), 'Inecel certificate trace must remain hidden from another installation with the same schema');
foreach (['T007' => 'T007_Chave_Acesso_Nfe', 'T008' => 'T008_T014_Id', 'T013' => 'T013_Chave_Acesso_Nfe'] as $table => $column) {
    $incompleteTransferColumns = $inecelColumns;
    $incompleteTransferColumns[$table] = array_values(array_diff($incompleteTransferColumns[$table], [$column]));
    mcpAssert(!(InstallationCapabilityCatalog::create()->resolve(new TestCapabilityProbe([], $incompleteTransferColumns, 'inecel-process-analysis-v1')))->has('customer.inecel.certificate_trace'), 'Inecel trace must require the transfer column ' . $column);
}
$inecelScopes = (new ScopeCatalog($inecelCapabilities))->all();
$inecelScope = array_values(array_filter($inecelScopes, static fn(array $scope): bool => $scope['id'] === 'inecel.certificate:read'))[0] ?? null;
mcpAssert(is_array($inecelScope) && $inecelScope['tools'] === ['hardness.inecel.certificate.trace', 'hardness.inecel.certificate.read'], 'Inecel scope must publish its trace and certificate-reading tools together');
$inecelModuleSource = file_get_contents(dirname(__DIR__, 2) . '/src/Tools/Catalog/InecelCertificateToolsModule.php');
mcpAssert($inecelModuleSource !== false && strpos($inecelModuleSource, 'new PdftotextPdfTextExtractor()') !== false && strpos($inecelModuleSource, 'new PdftotextPdfTextExtractor(true, $database)') !== false, 'Inecel trace must avoid OCR while the certificate-reading tool enables it on demand with a tenant-specific lock');

final class TestInecelTraceRepository implements InecelCertificateTraceRepositoryInterface, InecelCertificateDocumentRepositoryInterface
{
    public array $transferRows = [];
    public function trace(int $companyId, int $outgoingInvoiceId): array { if ($outgoingInvoiceId === 555) return $this->transferRows; if (in_array($outgoingInvoiceId, [222, 223], true)) return []; $documentId = $outgoingInvoiceId === 444 ? 0 : 111; $documentFileName = $documentId === 0 ? '' : 'certificate.pdf'; return [[
        'outgoing_invoice_id' => $outgoingInvoiceId, 'outgoing_invoice_number' => '10303', 'outgoing_item_id' => 1, 'outgoing_product_id' => 2, 'outgoing_lot' => '9673 - 2', 'production_order_id' => 9673, 'production_company_id' => 2, 'consumption_id' => 3,
        'inbound_lot' => '44064-25X03B45-B', 'inbound_invoice_id' => 4, 'inbound_invoice_number' => '483', 'inbound_company_id' => 2, 'inbound_at' => '2025-12-16', 'inbound_product_code' => '9000525', 'inbound_product_description' => 'Chapa',
        'document_id' => $documentId, 'document_file_name' => $documentFileName, 'document_uploaded_at' => '2026-04-13 13:31:34',
    ]]; }
    public function traceByInvoiceNumber(int $companyId, string $outgoingInvoiceNumber): array { if (in_array($outgoingInvoiceNumber, ['PENDENTE', 'INEXISTENTE'], true)) return []; return $this->trace($companyId, $outgoingInvoiceNumber === 'MCPSAI0001' ? 501 : 0); }
    public function findOutgoingInvoiceById(int $companyId, int $outgoingInvoiceId): ?array { return $outgoingInvoiceId === 222 ? ['outgoing_invoice_id' => 222, 'outgoing_invoice_number' => '10304'] : null; }
    public function findOutgoingInvoiceByNumber(int $companyId, string $outgoingInvoiceNumber): ?array { return $outgoingInvoiceNumber === 'PENDENTE' ? ['outgoing_invoice_id' => 224, 'outgoing_invoice_number' => 'PENDENTE'] : null; }
    public function findReadableDocument(int $companyId, int $documentId): ?array { return $companyId === 5 && $documentId === 111 ? ['document_id' => 111, 'file_name' => 'certificate.pdf'] : null; }
}
final class TestInecelPdfTextExtractor implements PdfTextExtractorInterface
{
    private string $text;
    private bool $busy;
    public function __construct(?string $text = null, bool $busy = false) { $this->text = $text ?? 'Certificate lot 25X03B45-B'; $this->busy = $busy; }
    public function extract(string $path): ?string { if ($this->busy) throw new CertificateOcrBusyException('busy'); return $this->text; }
}
$inecelDownloads = new CertificateDownloadLinkService('https://mcp.test', 'test-download-secret');
$inecelTool = new InecelCertificateTraceTool(new TestInecelTraceRepository(), new TestInecelPdfTextExtractor(), '/irrelevant', $inecelDownloads);
$inecelResult = $inecelTool->execute(['invoice_id' => 111337], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read'], 'oauth', 1, 2));
$inecelData = $inecelResult->getResult()['structuredContent'];
$inecelUrlParts = parse_url((string) ($inecelData['matches'][0]['document']['download_url'] ?? '')); parse_str((string) ($inecelUrlParts['query'] ?? ''), $inecelUrlQuery); $inecelLink = $inecelDownloads->verify((string) ($inecelUrlQuery['token'] ?? ''));
mcpAssert(($inecelData['matches'][0]['certificate_lot'] ?? null) === '25X03B45-B' && ($inecelData['matches'][0]['production_company_id'] ?? null) === 2 && ($inecelData['matches'][0]['inbound_company_id'] ?? null) === 2 && ($inecelLink['s'] ?? null) === 2 && ($inecelData['matches'][0]['document']['lot_found_in_text'] ?? null) === true && ($inecelData['matches'][0]['document']['download_available_for'] ?? null) === '15 minutos' && strpos((string) ($inecelResult->getResult()['content'][0]['text'] ?? ''), 'PDF disponível por: 15 minutos') !== false, 'Inecel tool must identify a cross-company trace and issue its PDF link for the source company');
$transferRepository = new TestInecelTraceRepository();
$transferRepository->transferRows = [array_replace($transferRepository->trace(5, 111337)[0], [
    'outgoing_invoice_id' => 555, 'inbound_invoice_number' => '1219', 'inbound_company_id' => 5,
    'transfer_invoice_id' => 111221, 'transfer_invoice_number' => '10269', 'transfer_company_id' => 5, 'transfer_item_id' => 1053770,
    'material_inbound_invoice_id' => 77308, 'material_inbound_invoice_number' => '10269', 'material_inbound_company_id' => 2,
])];
$transferResult = (new InecelCertificateTraceTool($transferRepository, new TestInecelPdfTextExtractor(), '/irrelevant', $inecelDownloads))->execute(['invoice_id' => 555], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read'], 'oauth', 1, 2));
$transferData = $transferResult->getResult()['structuredContent']['matches'][0];
parse_str((string) parse_url((string) $transferData['document']['download_url'], PHP_URL_QUERY), $transferUrlQuery);
$transferLink = $inecelDownloads->verify((string) ($transferUrlQuery['token'] ?? ''));
mcpAssert($transferData['inbound_invoice_number'] === '1219' && $transferData['transfer']['invoice_number'] === '10269' && $transferData['transfer']['received_company_id'] === 2 && ($transferLink['c'] ?? null) === 5 && ($transferLink['s'] ?? null) === 5 && strpos((string) $transferResult->getResult()['content'][0]['text'], 'NF de transferência: 10269') !== false, 'Inecel transfer trace must present its original invoice and sign the PDF for the original document company');
$inecelNoCertificateResult = $inecelTool->execute(['invoice_id' => 444], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read'], 'oauth', 1, 2));
mcpAssert(($inecelNoCertificateResult->getResult()['structuredContent']['matches'][0]['document']['certificate_status'] ?? null) === 'not_found' && strpos((string) ($inecelNoCertificateResult->getResult()['content'][0]['text'] ?? ''), 'Certificado: Não encontrado') !== false, 'Inecel tool must return a trace without an attached certificate as an explicit missing certificate');
$inecelNumberResult = $inecelTool->execute(['invoice_number' => 'MCPSAI0001'], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read']));
mcpAssert(($inecelNumberResult->getResult()['structuredContent']['outgoing_invoice_id'] ?? null) === 501, 'Inecel tool must accept the outgoing invoice number without exposing a cross-company lookup');
$inecelPendingResult = $inecelTool->execute(['invoice_id' => 222], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read']));
mcpAssert(($inecelPendingResult->getResult()['structuredContent']['error']['code'] ?? null) === 'inecel_certificate_trace_pending' && ($inecelPendingResult->getResult()['structuredContent']['error']['outgoing_invoice_number'] ?? null) === '10304', 'Inecel tool must distinguish an existing outgoing invoice whose trace is pending');
$inecelPendingNumberResult = $inecelTool->execute(['invoice_number' => 'PENDENTE'], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read']));
mcpAssert(($inecelPendingNumberResult->getResult()['structuredContent']['error']['code'] ?? null) === 'inecel_certificate_trace_pending', 'Inecel tool must distinguish an existing outgoing invoice whose trace is pending when searched by number');
$inecelMissingResult = $inecelTool->execute(['invoice_id' => 223], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read']));
mcpAssert(($inecelMissingResult->getResult()['structuredContent']['error']['code'] ?? null) === 'inecel_certificate_not_found', 'Inecel tool must report a missing outgoing invoice separately');
$inecelReadText = str_repeat('A', InecelCertificateReadTool::TEXT_CHUNK_CHARACTERS) . 'fim';
$inecelReadTool = new InecelCertificateReadTool(new TestInecelTraceRepository(), new TestInecelPdfTextExtractor($inecelReadText), '/irrelevant');
$inecelReadResult = $inecelReadTool->execute(['document_id' => 111], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read']));
$inecelReadData = $inecelReadResult->getResult()['structuredContent'];
mcpAssert(($inecelReadData['has_more'] ?? null) === true && ($inecelReadData['next_cursor'] ?? null) === InecelCertificateReadTool::TEXT_CHUNK_CHARACTERS && strlen((string) ($inecelReadData['text'] ?? '')) === InecelCertificateReadTool::TEXT_CHUNK_CHARACTERS, 'Inecel certificate reading must return a bounded first text chunk and cursor');
$inecelReadFinal = $inecelReadTool->execute(['document_id' => 111, 'cursor' => InecelCertificateReadTool::TEXT_CHUNK_CHARACTERS], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read']));
mcpAssert(($inecelReadFinal->getResult()['structuredContent']['text'] ?? null) === 'fim' && ($inecelReadFinal->getResult()['structuredContent']['has_more'] ?? null) === false, 'Inecel certificate reading must return the final chunk from next_cursor');
$inecelReadMissing = $inecelReadTool->execute(['document_id' => 999], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read']));
mcpAssert(($inecelReadMissing->getResult()['structuredContent']['error']['code'] ?? null) === 'inecel_certificate_document_not_found', 'Inecel certificate reading must reject documents outside the trace authorization');
$inecelReadBusy = (new InecelCertificateReadTool(new TestInecelTraceRepository(), new TestInecelPdfTextExtractor(null, true), '/irrelevant'))->execute(['document_id' => 111], new ExecutionContext(1, 1, 5, null, ['inecel.certificate:read']));
mcpAssert(($inecelReadBusy->getResult()['structuredContent']['error']['code'] ?? null) === 'inecel_certificate_ocr_busy', 'Inecel certificate reading must reject concurrent OCR without starting a second process');
