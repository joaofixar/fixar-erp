<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/autoload.php';

if (!function_exists('mcpAssert')) {
    function mcpAssert(bool $condition, string $message): void
    {
        if (!$condition) { throw new RuntimeException($message); }
    }
}

use HardnessMcp\Admin\McpConnectionAdminQueryService;
use HardnessMcp\AdminUi\Authorization\ErpAdminAuthorization;
use HardnessMcp\AdminUi\Context\TenantAdminContext;
use HardnessMcp\AdminUi\Http\McpAdminController;
use HardnessMcp\AdminUi\Security\AdministrativeRequestGuard;
use HardnessMcp\AdminUi\Security\CsrfGuard;

$managementSource = file_get_contents(dirname(__DIR__) . "/src/Admin/McpConnectionManagementService.php");
mcpAssert($managementSource !== false && strpos($managementSource, "MCP001_Public_Id") !== false && strpos($managementSource, "generatePublicId") !== false, "the administrative connection creation flow must generate and persist a public identifier for new connections.");
final class McpAdminFoundationStatement extends PDOStatement
{
    /** @var array<int,array<string,mixed>> */ private array $rows;
    /** @var array<int,array<string,mixed>> */ private array $resultRows;
    /** @param array<int,array<string,mixed>> $rows */
    public function __construct(array $rows) { $this->rows = $rows; $this->resultRows = $rows; }
    public function execute($parameters = null) {
        if (is_array($parameters) && (isset($parameters['id']) || isset($parameters['company_id']))) {
            $id = (int) (isset($parameters['id']) ? $parameters['id'] : $parameters['company_id']);
            $setting = $parameters['setting'] ?? null;
            $this->resultRows = array_values(array_filter($this->rows, static function (array $row) use ($id, $setting): bool {
                $companyId = $row['C004_Id'] ?? ($row['_test_company_id'] ?? ($row['C031_C004_Id'] ?? 0));
                return (int) $companyId === $id
                    && ($setting === null || ($row['C031_Campo'] ?? null) === $setting);
            }));
        }
        return true;
    }
    public function fetch($fetchStyle = null, $cursorOrientation = null, $cursorOffset = null) { return $this->resultRows[0] ?? false; }
    public function fetchAll(int $mode = \PDO::FETCH_DEFAULT, mixed ...$args): array { return $this->resultRows; }
}

final class McpAdminFoundationPdo extends PDO
{
    /** @var array<int,array<string,mixed>> */ private array $companies;
    /** @var array<int,array<string,mixed>> */ private array $connections;
    /** @var array<int,array<string,mixed>> */ private array $settings;
    /** @param array<int,array<string,mixed>> $companies @param array<int,array<string,mixed>> $connections @param array<int,array<string,mixed>> $settings */
    public function __construct(array $companies, array $connections = [], array $settings = []) { $this->companies = $companies; $this->connections = $connections; $this->settings = $settings; }
    public function query($query, ?int $fetchMode = null, ...$fetchModeArgs) {
        $companies = $this->companies;
        if (strpos($query, "c.C004_Flag_Ativo = 'S'") !== false) {
            $companies = array_values(array_filter($companies, static function (array $company): bool { return ($company['C004_Flag_Ativo'] ?? '') === 'S'; }));
        }
        return new McpAdminFoundationStatement($companies);
    }
    public function prepare($statement, $options = []) {
        $rows = strpos($statement, 'FROM C031') !== false ? $this->settings : (strpos($statement, 'FROM MCP001 c') !== false ? $this->connections : $this->companies);
        if (strpos($statement, "C004_Flag_Ativo = 'S'") !== false) {
            $rows = array_values(array_filter($rows, static function (array $company): bool { return ($company['C004_Flag_Ativo'] ?? '') === 'S'; }));
        }
        return new McpAdminFoundationStatement($rows);
    }
}

$adminAuthorization = new ErpAdminAuthorization();
$adminAuthorization->assertAuthorized(['usuarioAtual' => 12, 'nivelAlcada' => 4]);
try {
    $adminAuthorization->assertAuthorized(['usuarioAtual' => 12, 'nivelAlcada' => 3]);
    throw new RuntimeException('MCP admin authorization must reject a non-director.');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'MCP administration access denied.', 'MCP admin authorization must require access level 4');
}

$adminQuery = new McpConnectionAdminQueryService(new McpAdminFoundationPdo([
    ['C004_Id' => 7, 'C004_Nome_Empresa' => 'Empresa Matriz', 'C004_Nome_Abreviado' => 'Matriz', 'C004_Cnpj' => '00000000000100', 'C004_Flag_Ativo' => 'S', 'connection_count' => 2, 'active_connection_count' => 1, 'last_mcp_usage' => '2026-07-31 10:00:00'],
    ['C004_Id' => 8, 'C004_Nome_Empresa' => 'Empresa Filial', 'C004_Nome_Abreviado' => '', 'C004_Cnpj' => '00000000000200', 'C004_Flag_Ativo' => 'N', 'connection_count' => 0, 'active_connection_count' => 0, 'last_mcp_usage' => null],
    ['C004_Id' => 9, 'C004_Nome_Empresa' => 'Empresa Ativa Sem Conexão', 'C004_Nome_Abreviado' => '', 'C004_Cnpj' => '00000000000300', 'C004_Flag_Ativo' => 'S', 'connection_count' => 0, 'active_connection_count' => 0, 'last_mcp_usage' => null],
], [
    ['_test_company_id' => 7, 'MCP001_Id' => 101, 'MCP001_Nome' => 'Integração logística', 'MCP001_Status' => 'active', 'MCP001_Scopes' => '["company:read"]', 'MCP001_Data_Criacao' => '2026-07-01 10:00:00', 'MCP001_Data_Alteracao' => null, 'credential_count' => 2, 'active_credential_count' => 1, 'last_usage_at' => '2026-07-31 10:00:00', 'MCP002_Secret_Hash' => 'must-not-leak'],
]));
$availableCompanies = $adminQuery->listAvailableCompanies();
mcpAssert(count($availableCompanies) === 2, 'MCP admin company list must include only active companies.');
mcpAssert($availableCompanies[1]['id'] === 9 && $availableCompanies[1]['connection_count'] === 0 && $availableCompanies[1]['last_mcp_usage'] === null, 'MCP admin company list must preserve an active company without connections.');
$selectedCompany = $adminQuery->validateSelectedCompany(7);
mcpAssert($selectedCompany->getCompanyId() === 7 && $selectedCompany->getShortName() === 'Matriz', 'MCP admin selected company must be database validated');
mcpAssert(!$adminQuery->isIdeSmtOrderGenerationEnabled($selectedCompany), 'MCP admin must hide the IDE SMT order permission when the company setting is absent');
$enabledCapabilityQuery = new McpConnectionAdminQueryService(new McpAdminFoundationPdo([], [], [
    ['C031_Id' => 1, 'C031_C004_Id' => 7, 'C031_Campo' => 'SMT_habilitaMudarGerarPedido', 'C031_Valor' => 'S'],
]));
mcpAssert($enabledCapabilityQuery->isIdeSmtOrderGenerationEnabled($selectedCompany), 'MCP admin must expose the IDE SMT order permission when the company setting is enabled');
$adminTenant = new TenantAdminContext('erp.example.test', 'e040', 12, 4, '203.0.113.10');
$catalogMethod = new ReflectionMethod(McpAdminController::class, 'presentationScopeCatalog');
$catalogMethod->setAccessible(true);
$disabledCatalog = $catalogMethod->invoke(new McpAdminController($adminTenant, $adminQuery), $selectedCompany);
$enabledCatalog = $catalogMethod->invoke(new McpAdminController($adminTenant, $enabledCapabilityQuery), $selectedCompany);
mcpAssert(!in_array('ide_smt:quote:order:generate', array_column($disabledCatalog, 'id'), true), 'MCP admin permission catalog must omit the IDE SMT order permission when disabled');
mcpAssert(in_array('ide_smt:quote:order:generate', array_column($enabledCatalog, 'id'), true), 'MCP admin permission catalog must include the IDE SMT order permission when enabled');
$companyConnections = $adminQuery->listConnectionsForCompany($selectedCompany);
mcpAssert(count($companyConnections) === 1 && $companyConnections[0]['id'] === 101, 'MCP admin must list only the selected company connections');
mcpAssert($companyConnections[0]["public_id"] === null, "a legacy connection without MCP001_Public_Id must remain represented without an exclusive URL.");
$exclusiveAdminQuery = new McpConnectionAdminQueryService(new McpAdminFoundationPdo([["C004_Id" => 7, "C004_Nome_Empresa" => "Empresa Matriz", "C004_Nome_Abreviado" => "Matriz", "C004_Cnpj" => "00000000000100", "C004_Flag_Ativo" => "S"]], [["_test_company_id" => 7, "MCP001_Id" => 102, "MCP001_Nome" => "Integração exclusiva", "MCP001_Public_Id" => "AbCdEfGhIjKlMnOpQrStUvWx", "MCP001_Status" => "active", "MCP001_Scopes" => "[]", "MCP001_Data_Criacao" => "2026-07-01 10:00:00", "MCP001_Data_Alteracao" => null, "credential_count" => 0, "active_credential_count" => 0, "last_usage_at" => null]]));
$exclusiveConnections = $exclusiveAdminQuery->listConnectionsForCompany($selectedCompany);
mcpAssert(($exclusiveConnections[0]["public_id"] ?? null) === "AbCdEfGhIjKlMnOpQrStUvWx", "an exclusive connection must expose its valid public identifier to the OAuth administration DTO.");
mcpAssert(strpos(json_encode($companyConnections), 'must-not-leak') === false && strpos(json_encode($companyConnections), 'Secret_Hash') === false, 'MCP admin connection DTO must not contain credential secrets or hashes');
$emptyCompany = $adminQuery->validateSelectedCompany(9);
mcpAssert($adminQuery->listConnectionsForCompany($emptyCompany) === [], 'MCP admin must return an empty list for a company without connections');
try {
    $adminQuery->validateSelectedCompany(8);
    throw new RuntimeException('inactive selected company must fail');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Selected company was not found.', 'MCP admin must reject an inactive company.');
}
try {
    $adminQuery->validateSelectedCompany(0);
    throw new RuntimeException('invalid selected company must fail');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Selected company is invalid.', 'MCP admin must reject non-positive selected company identifiers');
}
try {
    $adminQuery->validateSelectedCompany(99);
    throw new RuntimeException('unknown selected company must fail');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Selected company was not found.', 'MCP admin must reject a company outside the current installation');
}

$menuSource = file_get_contents(dirname(__DIR__, 2) . '/prog/etc/etc011/outros.php');
mcpAssert($menuSource !== false && strpos($menuSource, "!empty(\$g['c029Ids'][306]) && isset(\$g['nivelAlcada']) && (int) \$g['nivelAlcada'] === 4") !== false, 'MCP admin shortcut must require MCP permission 306 and access level 4.');
mcpAssert(strpos((string) $menuSource, '/gerencial/mcp/content/McpAdminStandalone/?ajax=true') !== false, 'MCP admin shortcut must open the standalone administrative route outside the public MCP namespace.');
$adminControllerSource = file_get_contents(dirname(__DIR__) . '/admin/Http/McpAdminController.php');
mcpAssert($adminControllerSource !== false && strpos($adminControllerSource, '/gerencial/mcp/outro/McpAdminCompanyDetail/') !== false, 'MCP admin shell must use the authenticated Framework company detail route outside the public MCP namespace.');
mcpAssert(strpos((string) $adminControllerSource, '<!doctype html>') !== false, 'MCP administration must provide a standalone full-page document.');
mcpAssert(strpos((string) $adminControllerSource, 'mcp-admin-nav__companies') !== false, 'MCP standalone administration must provide a company navigation sidebar.');
mcpAssert(strpos((string) $adminControllerSource, "<small>Id '") !== false, 'MCP standalone company navigation must display the C004 identifier.');
$adminEntrySource = file_get_contents(dirname(__DIR__) . '/admin/FrameworkEntry.php');
mcpAssert($adminEntrySource !== false && strpos($adminEntrySource, "'credentials' => \$detail['credentials']") !== false, 'MCP detail response must carry only the safe credential DTO collection.');
mcpAssert(strpos((string) $adminEntrySource, 'updateConnectionScopes') !== false && strpos($adminEntrySource, 'CsrfGuard') !== false, 'scope updates must be routed through the authenticated CSRF-protected administrative entrypoint.');
mcpAssert(strpos((string) $adminEntrySource, 'oauthMutation') !== false && strpos($adminEntrySource, 'assertAdministrativeMutationRequest();') !== false, 'OAuth administration mutations must use the authenticated POST and CSRF-protected entrypoint.');
mcpAssert(strpos((string) $adminControllerSource, 'data-oauth-grant-revoke-url') !== false && strpos($adminControllerSource, 'data-oauth-grant-create-url') === false && strpos($adminControllerSource, '/oauth/authorize') === false, 'OAuth administration must remain inside ERP routes and must not activate public OAuth endpoints.');
mcpAssert(strpos((string) $adminControllerSource, 'data-oauth-grants-revoke-outdated-url') !== false && strpos((string) $adminEntrySource, 'grants_revoke_outdated') !== false, 'OAuth administration must offer the authenticated cleanup of outdated grants.');
$adminJavascript = file_get_contents(dirname(__DIR__) . '/admin/assets/mcp-admin.js');
mcpAssert($adminControllerSource !== false && strpos($adminControllerSource, "connectionMcpUrl") !== false && strpos($adminControllerSource, "public_id") !== false && strpos($adminJavascript, "mcp-admin-mcp-url-enable") !== false && strpos($adminJavascript, "data-connection-public-id-url") !== false, "the OAuth tab must display the connection-specific URL and offer an explicit legacy URL upgrade action.");
mcpAssert($managementSource !== false && strpos($managementSource, "ensurePublicId") !== false && strpos($managementSource, "MCP001_Public_Id IS NULL") !== false && strpos($managementSource, "connection_public_id_created") !== false, "legacy public URL generation must be idempotent and audit only the identifier creation.");
mcpAssert($adminJavascript !== false && strpos($adminJavascript, '?ajax=true&C004_Id=') !== false, 'MCP admin detail requests must use the Framework ajax parameter.');
mcpAssert(strpos((string) $adminJavascript, 'r_ajax=') === false, 'MCP admin detail requests must not use an unsupported ajax parameter.');
mcpAssert(strpos((string) $adminJavascript, 'data-mcp-admin-home') !== false, 'MCP standalone administration must support returning to its overview.');
mcpAssert(strpos((string) $adminJavascript, 'data-detail-tab="oauth"') !== false && strpos($adminJavascript, 'mcp-admin-mcp-url-copy') !== false && strpos($adminJavascript, 'Client ID público') === false, 'MCP connection detail must expose the simplified OAuth onboarding tab without technical client registration.');
mcpAssert(strpos((string) $adminJavascript, 'data-oauth-grants-revoke-outdated') !== false, 'MCP OAuth tab must offer the batch cleanup action for outdated authorizations.');
$mcpAdminMenuVisible = static function (array $context): bool { return isset($context['nivelAlcada']) && (int) $context['nivelAlcada'] === 4; };
mcpAssert($mcpAdminMenuVisible(['nivelAlcada' => 4]), 'MCP admin menu must be available to access level 4');
mcpAssert(!$mcpAdminMenuVisible(['nivelAlcada' => 3]) && !$mcpAdminMenuVisible([]), 'MCP admin menu must be unavailable below access level 4');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_save_path(sys_get_temp_dir());
    session_start();
}
$_SESSION = [];
$csrf = new CsrfGuard();
$csrfToken = $csrf->token();
$csrfSecondRequest = new CsrfGuard();
$csrfSecondRequest->assertValid($csrfToken);
$csrf->assertValid($csrfToken);
$requestGuard = new AdministrativeRequestGuard();
$requestGuard->assertMutationRequest('POST', ['csrf' => $csrfToken]);
foreach ([
    ['GET', ['csrf' => $csrfToken], 'GET administrative mutation must fail'],
    ['POST', [], 'administrative mutation without CSRF must fail'],
    ['POST', ['csrf' => 'invalid'], 'administrative mutation with invalid CSRF must fail'],
] as $case) {
    try {
        $requestGuard->assertMutationRequest($case[0], $case[1]);
        throw new RuntimeException($case[2]);
    } catch (RuntimeException $exception) {
        mcpAssert($exception->getMessage() === 'Invalid administrative request.' || $exception->getMessage() === 'Invalid CSRF token.', $case[2]);
    }
}
try {
    $csrf->assertValid('invalid');
    throw new RuntimeException('invalid csrf must fail');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Invalid CSRF token.', 'MCP credential mutations must reject invalid CSRF');
}
$csrfTokenBeforeRegeneration = $csrf->token();
CsrfGuard::regenerate();
try {
    $csrf->assertValid($csrfTokenBeforeRegeneration);
    throw new RuntimeException('CSRF token from the previous authentication must fail');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Invalid CSRF token.', 'CSRF token must be regenerated after authentication');
}
$csrfTokenAfterRegeneration = $csrf->token();
$_SESSION = [];
try {
    $csrf->assertValid($csrfTokenAfterRegeneration);
    throw new RuntimeException('CSRF token from another session must fail');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Invalid CSRF token.', 'CSRF token must be bound to the current session');
}
$_SESSION = [];
$csrfTokenAfterLogout = $csrf->token();
CsrfGuard::invalidate();
try {
    $csrf->assertValid($csrfTokenAfterLogout);
    throw new RuntimeException('CSRF token must fail after logout invalidation');
} catch (RuntimeException $exception) {
    mcpAssert($exception->getMessage() === 'Invalid CSRF token.', 'CSRF token must be invalidated on logout');
}
$frameworkEntrySource = file_get_contents(dirname(__DIR__) . '/admin/FrameworkEntry.php');
mcpAssert($frameworkEntrySource !== false && substr_count($frameworkEntrySource, 'assertAdministrativeMutationRequest();') === 11, 'every MCP administrative mutation must require POST and CSRF before services.');
mcpAssert($frameworkEntrySource !== false && strpos($frameworkEntrySource, 'oauthCsrfField') !== false && strpos($frameworkEntrySource, "if (\$action !== '') { self::assertAdministrativeMutationRequest(); }") !== false, 'ERP OAuth approval, selection and cancellation forms must require POST and CSRF.');
$adminRouteSource = file_get_contents(dirname(__DIR__, 2) . '/prog/gerencial/mcp/outro.php');
mcpAssert($adminRouteSource !== false && strpos($adminRouteSource, 'credentialMutation($g, $confUsuario, $action, $_POST)') !== false && strpos($adminRouteSource, 'oauthMutation($g, $confUsuario, $actions[$g[\'r\'][3]], $_POST)') !== false, 'administrative mutation routes must read only POST fields.');
mcpAssert(strpos((string) $frameworkEntrySource, 'deleteUnusedConnection') !== false && strpos((string) $adminRouteSource, 'McpAdminConnectionDeleteUnused') !== false, 'unused connection deletion must use the authenticated, CSRF-protected administrative route.');
$loginSource = file_get_contents(dirname(__DIR__, 2) . '/outros/login/index.php');
mcpAssert($loginSource !== false && strpos($loginSource, 'CsrfGuard::regenerate()') !== false, 'a successful ERP login must regenerate the MCP administrative CSRF token.');
$credentialQuerySource = file_get_contents(dirname(__DIR__) . '/src/Admin/McpConnectionAdminQueryService.php');
mcpAssert($credentialQuerySource !== false && strpos($credentialQuerySource, 'MCP002_Secret_Hash') === false && strpos($credentialQuerySource, 'MCP002_Pepper_Version') === false, 'credential list query must not select hash or pepper');
$credentialJavascript = file_get_contents(dirname(__DIR__) . '/admin/assets/mcp-admin.js');
mcpAssert($credentialJavascript !== false && strpos($credentialJavascript, 'localStorage') === false && strpos($credentialJavascript, 'sessionStorage') === false, 'one-time key UI must not persist API keys in browser storage');
mcpAssert(strpos((string) $credentialJavascript, 'mcp-admin-delete-unused') !== false, 'the UI must expose the authenticated unused-connection deletion action.');
mcpAssert(strpos((string) $credentialJavascript, 'confirmUnusedConnection') !== false && strpos((string) $credentialJavascript, 'Não foi possível excluir a conexão') !== false && strpos((string) $credentialJavascript, 'window.confirm(\'Excluir esta conexão?') === false, 'unused-connection deletion must use the MCP dialog and a specific rejection message.');
mcpAssert(strpos((string) $credentialJavascript, "function userLabel(user)") !== false && strpos((string) $credentialJavascript, "(Id '") !== false, 'MCP administration must display the user identifier next to the user name.');
mcpAssert(strpos((string) $credentialJavascript, 'modal.remove()') !== false, 'one-time key UI must remove the key from the DOM when closed');
mcpAssert(strpos((string) $credentialJavascript, 'Ative a conexão para gerar credenciais.') !== false, 'inactive connections must not expose the credential generation action.');
mcpAssert(strpos((string) $credentialJavascript, 'selectedConnectionFilter') !== false && strpos($credentialJavascript, 'applyConnectionFilter(selectedConnectionFilter)') !== false, 'connection filter must survive an administrative list refresh.');
mcpAssert(strpos((string) $credentialJavascript, 'data-connection-scopes-save') !== false && strpos($credentialJavascript, 'data-connection-scopes') !== false, 'administration UI must render server-catalogued scope controls without free text input.');
mcpAssert(strpos((string) $credentialJavascript, 'data-connection-manage') !== false && strpos($credentialJavascript, 'renderConnectionDetail') !== false, 'connection list must open a dedicated management detail instead of expanding credentials and scopes inline.');
