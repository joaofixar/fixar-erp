<?php

declare(strict_types=1);

namespace hardness;

use HardnessMcp\AdminUi\FrameworkEntry;

require_once dirname(__DIR__, 3) . '/mcp/autoload.php';

if (($g['r'][3] ?? '') === 'McpOAuthAuthorizationResume') {
    FrameworkEntry::oauthAuthorizationResume($g, $confUsuario);
}

if (($g['r'][3] ?? '') === 'McpAdminValidateCompany') {
    FrameworkEntry::validateCompany($g, $confUsuario, $_REQUEST['C004_Id'] ?? null);
}

if (($g['r'][3] ?? '') === 'McpAdminCompanyDetail') {
    FrameworkEntry::companyDetail($g, $confUsuario, $_REQUEST['C004_Id'] ?? null);
}
if (($g['r'][3] ?? '') === 'McpAdminConnectionDetail') {
    FrameworkEntry::connectionDetail($g, $confUsuario, $_REQUEST['C004_Id'] ?? null, $_REQUEST['MCP001_Id'] ?? null, $_REQUEST['audit_offset'] ?? 0);
}

if (($g['r'][3] ?? '') === 'McpAdminConnectionPublicId') {
    FrameworkEntry::ensureConnectionPublicId($g, $confUsuario, $_POST['C004_Id'] ?? null, $_POST['MCP001_Id'] ?? null);
}

if (($g['r'][3] ?? '') === 'McpAdminConnectionCreate') {
    FrameworkEntry::createConnection($g, $confUsuario, $_POST['C004_Id'] ?? null, $_POST['nome'] ?? '');
}

if (($g['r'][3] ?? '') === 'McpAdminConnectionStatus') {
    FrameworkEntry::changeConnectionStatus($g, $confUsuario, $_POST['C004_Id'] ?? null, $_POST['MCP001_Id'] ?? null, $_POST['status'] ?? '');
}

if (($g['r'][3] ?? '') === 'McpAdminConnectionDeleteUnused') {
    FrameworkEntry::deleteUnusedConnection($g, $confUsuario, $_POST['C004_Id'] ?? null, $_POST['MCP001_Id'] ?? null);
}

if (($g['r'][3] ?? '') === 'McpAdminConnectionScopes') {
    FrameworkEntry::updateConnectionScopes($g, $confUsuario, $_POST['C004_Id'] ?? null, $_POST['MCP001_Id'] ?? null, $_POST['scopes'] ?? null, $_POST['csrf'] ?? null);
}
if (($g['r'][3] ?? '') === 'McpAdminConnectionRename') {
    FrameworkEntry::renameConnection($g, $confUsuario, $_POST['C004_Id'] ?? null, $_POST['MCP001_Id'] ?? null, $_POST['nome'] ?? null, $_POST['csrf'] ?? null);
}
if (($g['r'][3] ?? '') === 'McpAdminConnectionApiKeyResponsible') {
    FrameworkEntry::updateApiKeyResponsible($g, $confUsuario, $_POST['C004_Id'] ?? null, $_POST['MCP001_Id'] ?? null, $_POST['C007_Id'] ?? null);
}

if (in_array(($g['r'][3] ?? ''), ['McpAdminCredentialGenerate', 'McpAdminCredentialRotate', 'McpAdminCredentialRevoke'], true)) {
    $action = $g['r'][3] === 'McpAdminCredentialGenerate' ? 'generate' : ($g['r'][3] === 'McpAdminCredentialRotate' ? 'rotate' : 'revoke');
    FrameworkEntry::credentialMutation($g, $confUsuario, $action, $_POST);
}

if (in_array(($g['r'][3] ?? ''), ['McpAdminOAuthClientCreate', 'McpAdminOAuthClientUpdate', 'McpAdminOAuthClientStatus', 'McpAdminOAuthRedirectAdd', 'McpAdminOAuthRedirectRevoke', 'McpAdminOAuthGrantCreate', 'McpAdminOAuthGrantRevoke', 'McpAdminOAuthGrantsRevokeOutdated'], true)) {
    $actions = [
        'McpAdminOAuthClientCreate' => 'client_create', 'McpAdminOAuthClientUpdate' => 'client_update', 'McpAdminOAuthClientStatus' => 'client_status',
        'McpAdminOAuthRedirectAdd' => 'redirect_add', 'McpAdminOAuthRedirectRevoke' => 'redirect_revoke',
        'McpAdminOAuthGrantCreate' => 'grant_create', 'McpAdminOAuthGrantRevoke' => 'grant_revoke', 'McpAdminOAuthGrantsRevokeOutdated' => 'grants_revoke_outdated',
    ];
    FrameworkEntry::oauthMutation($g, $confUsuario, $actions[$g['r'][3]], $_POST);
}
if (in_array(($g['r'][3] ?? ''), ['McpAdminEligibilityDirectAdd','McpAdminEligibilityDirectRevoke','McpAdminEligibilityProfileAdd','McpAdminEligibilityProfileRevoke','McpAdminEligibilityAllUsers'], true)) {
    $actions=['McpAdminEligibilityDirectAdd'=>'direct_add','McpAdminEligibilityDirectRevoke'=>'direct_revoke','McpAdminEligibilityProfileAdd'=>'profile_add','McpAdminEligibilityProfileRevoke'=>'profile_revoke','McpAdminEligibilityAllUsers'=>'all_users'];FrameworkEntry::eligibilityMutation($g,$confUsuario,$actions[$g['r'][3]],$_POST);
}
