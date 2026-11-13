<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi\Http;

use HardnessMcp\Admin\McpConnectionAdminQueryService;
use HardnessMcp\AdminUi\Context\SelectedCompanyContext;
use HardnessMcp\AdminUi\Context\TenantAdminContext;
use HardnessMcp\AdminUi\Presentation\PermissionPresentationCatalog;
use HardnessMcp\Scopes\ScopeCatalog;

final class McpAdminController
{
    private TenantAdminContext $tenantContext;
    private McpConnectionAdminQueryService $queries;
    private ScopeCatalog $scopeCatalog;
    private string $publicMcpUrl;

    public function __construct(TenantAdminContext $tenantContext, McpConnectionAdminQueryService $queries, string $publicMcpUrl = '')
    {
        $this->tenantContext = $tenantContext;
        $this->queries = $queries;
        $this->scopeCatalog = new ScopeCatalog();
        $this->publicMcpUrl = $publicMcpUrl;
    }

    public function renderShell(): string
    {
        return '<link rel="stylesheet" href="/hardness3/mcp/admin/assets/mcp-admin.css?v=22">'
            . $this->renderCompanies($this->queries->listAvailableCompanies())
            . '<script src="/hardness3/mcp/admin/assets/mcp-admin.js?v=41" defer></script>';
    }

    public function renderStandalonePage(string $csrfToken): string
    {
        $content = $this->renderStandaloneLayout($this->queries->listAvailableCompanies(), $csrfToken);

        return '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<meta name="robots" content="noindex,nofollow"><title>Conexões MCP</title>'
            . '<link rel="stylesheet" href="/hardness3/mcp/admin/assets/mcp-admin.css?v=22"></head>'
            . '<body class="mcp-admin-standalone">' . $content
            . '<script src="/hardness3/mcp/admin/assets/mcp-admin.js?v=41" defer></script></body></html>';
    }

    /** @return array{id:int,legal_name:string,short_name:string,status:string} */
    public function validateSelectedCompany(int $companyId): array
    {
        $company = $this->queries->validateSelectedCompany($companyId);
        return $this->selectedCompanyDto($company);
    }

    /** @return array{company:array{id:int,legal_name:string,short_name:string,document:string,status:string,connection_count:int,active_connection_count:int,last_mcp_usage:?string},connections:array<int,array{id:int,name:string,status:string,scopes:array<int,string>,created_at:string,updated_at:?string,last_usage_at:?string,credential_count:int,active_credential_count:int}>} */
    public function companyDetail(int $companyId): array
    {
        $company = $this->queries->validateSelectedCompany($companyId);
        $connections = $this->queries->listConnectionsForCompany($company);
        $credentialRows = $this->queries->listCredentialsForConnections($company, array_map(static function (array $connection): int { return $connection['id']; }, $connections));
        $credentials = [];
        foreach ($credentialRows as $connectionId => $items) {
            $credentials[(string) $connectionId] = $items;
        }
        return [
            'company' => $this->queries->getCompanySummary($company),
            'connections' => $connections,
            'credentials' => $credentials,
            'scope_catalog' => $this->presentationScopeCatalog($company),
        ];
    }

    /** @return array<string,mixed> */
    public function connectionDetail(int $companyId, int $connectionId, int $auditOffset = 0): array
    {
        $company = $this->queries->validateSelectedCompany($companyId);
        $auditLimit = 50;
        $auditRows = $this->queries->listAuditForConnection($company, $connectionId, $auditLimit + 1, max(0, $auditOffset));
        $hasMoreAudit = count($auditRows) > $auditLimit;
        if ($hasMoreAudit) { array_pop($auditRows); }
        $connection = $this->queries->getConnectionForCompany($company, $connectionId);
        return ['company' => $this->queries->getCompanySummary($company), 'connection' => $connection, 'mcp_url' => $this->connectionMcpUrl($connection), 'credentials' => $this->queries->listCredentialsForConnection($company, $connectionId), 'oauth_users' => $this->queries->listActiveUsersForCompany($company), 'eligibility' => $this->queries->eligibilityForConnection($company, $connectionId), 'oauth_grants' => $this->queries->listOAuthGrantsForConnection($company, $connectionId), 'audit' => $auditRows, 'audit_pagination' => ['next_offset' => $auditOffset + count($auditRows), 'has_more' => $hasMoreAudit], 'scope_catalog' => $this->presentationScopeCatalog($company)];
    }
    /** @param array<string,mixed> $connection */
    private function connectionMcpUrl(array $connection): string
    {
        $publicId = $connection['public_id'] ?? null;
        if (!is_string($publicId) || preg_match('/\A[A-Za-z0-9_-]{24}\z/', $publicId) !== 1) { return $this->publicMcpUrl; }
        return rtrim($this->publicMcpUrl, '/') . '/c/' . $publicId;
    }


    /** @param array<int,array{id:int,legal_name:string,short_name:string,document:string,status:string,connection_count:int,active_connection_count:int,last_mcp_usage:?string}> $companies */
    private function renderCompanies(array $companies): string
    {
        $cards = '';
        foreach ($companies as $company) {
            $name = $company['short_name'] !== '' ? $company['short_name'] : $company['legal_name'];
            $cards .= '<button type="button" class="mcp-admin-company-card" data-company-id="' . (int) $company['id'] . '">'
                . '<div class="mcp-admin-company-card__heading"><h2>' . $this->escape($name) . '</h2><span class="mcp-admin-status">' . $this->escape($this->statusLabel($company['status'])) . '</span></div>'
                . '<p class="mcp-admin-company-card__legal">' . $this->escape($company['legal_name']) . '</p>'
                . '<dl class="mcp-admin-company-card__metrics">'
                . '<div><dt>Conexões</dt><dd>' . (int) $company['connection_count'] . '</dd></div>'
                . '<div><dt>Ativas</dt><dd>' . (int) $company['active_connection_count'] . '</dd></div>'
                . '</dl>'
                . '<p class="mcp-admin-company-card__usage">Último uso: ' . $this->escape($this->usageLabel($company['last_mcp_usage'])) . '</p>'
                . '<span class="mcp-admin-company-card__open">Ver conexões <span aria-hidden="true">→</span></span>'
                . '</button>';
        }
        if ($cards === '') {
            $cards = '<div class="mcp-admin-empty">Nenhuma empresa está disponível nesta instalação.</div>';
        }

        return '<section class="mcp-admin" aria-labelledby="mcp-admin-title" data-company-detail-url="/gerencial/mcp/outro/McpAdminCompanyDetail/" data-connection-create-url="/gerencial/mcp/outro/McpAdminConnectionCreate/" data-connection-status-url="/gerencial/mcp/outro/McpAdminConnectionStatus/">'
            . '<header class="mcp-admin__header"><div><p class="mcp-admin__eyebrow">INTEGRAÇÕES</p><h1 id="mcp-admin-title">Conexões MCP</h1><p>Empresas da instalação atual</p></div>'
            . '<div class="mcp-admin__context">Diretor autenticado</div></header>'
            . '<main class="mcp-admin__content" id="mcp-admin-content"><section class="mcp-admin__intro"><h2>Empresas da instalação</h2><p>Escolha uma empresa para administrar suas conexões MCP. Nesta etapa, a visão é somente leitura.</p></section>'
            . '<div class="mcp-admin-company-grid">' . $cards . '</div></main>'
            . '</section>';
    }

    /** @param array<int,array{id:int,legal_name:string,short_name:string,document:string,status:string,connection_count:int,active_connection_count:int,last_mcp_usage:?string}> $companies */
    private function renderStandaloneLayout(array $companies, string $csrfToken): string
    {
        $companyLinks = '';
        $connectionCount = 0;
        $activeConnectionCount = 0;
        foreach ($companies as $company) {
            $name = $company['short_name'] !== '' ? $company['short_name'] : $company['legal_name'];
            $companyLinks .= '<button type="button" class="mcp-admin-nav__company" data-company-id="' . (int) $company['id'] . '">'
                . '<span>' . $this->escape($name) . '</span><small>Id ' . (int) $company['id'] . ' · ' . (int) $company['active_connection_count'] . ' ativas</small></button>';
            $connectionCount += (int) $company['connection_count'];
            $activeConnectionCount += (int) $company['active_connection_count'];
        }
        if ($companyLinks === '') {
            $companyLinks = '<p class="mcp-admin-nav__empty">Nenhuma empresa disponível.</p>';
        }

        $companyCount = count($companies);
        $home = '<section class="mcp-admin-home"><div class="mcp-admin-home__hero"><p class="mcp-admin__eyebrow">INTEGRAÇÕES</p><h1>Conexões MCP</h1>'
            . '<p>O MCP é uma conexão segura que permite que assistentes de inteligência artificial, como o Claude, consultem informações do Hardness de forma controlada. Esta integração já foi homologada com o Claude.</p><p>Escolha uma empresa no menu para criar ou administrar as conexões que ela poderá usar.</p></div>'
            . '<section class="mcp-admin-home__guide" aria-label="Como funciona"><h2>Como funciona</h2><ol><li>Escolha a empresa que deseja conectar a um assistente de IA.</li><li>Crie uma conexão e defina quais informações ela poderá consultar.</li><li>Conclua a autorização pelo fluxo seguro de OAuth.</li></ol></section>'
            . '<section class="mcp-admin-home__metrics" aria-label="Resumo da instalação"><article><span>Empresas</span><strong>' . $companyCount . '</strong><small>nesta instalação</small></article><article><span>Conexões</span><strong>' . $connectionCount . '</strong><small>configuradas</small></article><article><span>Ativas</span><strong>' . $activeConnectionCount . '</strong><small>prontas para uso</small></article></section></section>';

        return '<div class="mcp-admin-app"><aside class="mcp-admin-nav"><a class="mcp-admin-nav__brand" href="/erp">MCP <span>Administração</span></a>'
            . '<button type="button" class="mcp-admin-nav__home" data-mcp-admin-home aria-current="page">Visão geral</button>'
            . '<div class="mcp-admin-nav__label">Empresas da instalação</div><nav class="mcp-admin-nav__companies" aria-label="Empresas MCP">' . $companyLinks . '</nav>'
            . '<a class="mcp-admin-nav__back" href="/erp">← Voltar ao ERP</a></aside>'
            . '<main class="mcp-admin mcp-admin--workspace" aria-live="polite" data-company-detail-url="/gerencial/mcp/outro/McpAdminCompanyDetail/" data-connection-detail-url="/gerencial/mcp/outro/McpAdminConnectionDetail/" data-connection-create-url="/gerencial/mcp/outro/McpAdminConnectionCreate/" data-connection-status-url="/gerencial/mcp/outro/McpAdminConnectionStatus/" data-connection-public-id-url="/gerencial/mcp/outro/McpAdminConnectionPublicId/" data-connection-delete-url="/gerencial/mcp/outro/McpAdminConnectionDeleteUnused/" data-connection-rename-url="/gerencial/mcp/outro/McpAdminConnectionRename/" data-connection-scopes-url="/gerencial/mcp/outro/McpAdminConnectionScopes/" data-api-key-responsible-url="/gerencial/mcp/outro/McpAdminConnectionApiKeyResponsible/" data-credential-generate-url="/gerencial/mcp/outro/McpAdminCredentialGenerate/" data-credential-rotate-url="/gerencial/mcp/outro/McpAdminCredentialRotate/" data-credential-revoke-url="/gerencial/mcp/outro/McpAdminCredentialRevoke/" data-oauth-grant-revoke-url="/gerencial/mcp/outro/McpAdminOAuthGrantRevoke/" data-oauth-grants-revoke-outdated-url="/gerencial/mcp/outro/McpAdminOAuthGrantsRevokeOutdated/" data-eligibility-direct-add-url="/gerencial/mcp/outro/McpAdminEligibilityDirectAdd/" data-eligibility-direct-revoke-url="/gerencial/mcp/outro/McpAdminEligibilityDirectRevoke/" data-eligibility-profile-add-url="/gerencial/mcp/outro/McpAdminEligibilityProfileAdd/" data-eligibility-profile-revoke-url="/gerencial/mcp/outro/McpAdminEligibilityProfileRevoke/" data-eligibility-all-users-url="/gerencial/mcp/outro/McpAdminEligibilityAllUsers/" data-csrf-token="' . $this->escape($csrfToken) . '"><div id="mcp-admin-content">' . $home . '</div></main></div>';
    }

    /** @return array{id:int,legal_name:string,short_name:string,status:string} */
    private function selectedCompanyDto(SelectedCompanyContext $company): array
    {
        return [
            'id' => $company->getCompanyId(),
            'legal_name' => $company->getLegalName(),
            'short_name' => $company->getShortName(),
            'status' => $company->getStatus(),
        ];
    }

    private function usageLabel(?string $value): string { return $value === null || $value === '' ? 'Nunca' : $value; }
    private function statusLabel(string $status): string { return strtoupper($status) === 'S' ? 'Ativa' : 'Inativa'; }
    private function escape(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

    /** Adds only a UI hint, derived from the authorization catalogue's existing rule. */
    private function presentationScopeCatalog(SelectedCompanyContext $company): array
    {
        $presentation = new PermissionPresentationCatalog();
        $scopes = $this->scopeCatalog->all();
        if (!$this->queries->isIdeSmtOrderGenerationEnabled($company)) {
            $scopes = array_values(array_filter($scopes, static function (array $scope): bool {
                return $scope['id'] !== 'ide_smt:quote:order:generate';
            }));
        }
        return array_map(function (array $scope) use ($presentation): array {
            $scope['requires_erp_actor'] = $this->scopeCatalog->requiresErpActor([$scope['id']]);
            $scope['admin_presentation'] = $presentation->forScope($scope['id']);
            return $scope;
        }, $scopes);
    }
}
