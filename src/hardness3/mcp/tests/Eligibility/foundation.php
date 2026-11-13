<?php
declare(strict_types=1);

$eligibilityMigration = file_get_contents(dirname(__DIR__, 3) . '/scripts/atualizacaoBase.php');
foreach ([15, 16, 17] as $tableNumber) {
    mcpAssert(strpos((string) $eligibilityMigration, 'CREATE TABLE IF NOT EXISTS MCP' . sprintf('%03d', $tableNumber)) !== false, 'Eligibility migration must create MCP' . sprintf('%03d', $tableNumber));
}
mcpAssert(strpos((string) $eligibilityMigration, 'CREATE TABLE IF NOT EXISTS MCP013') === false && strpos((string) $eligibilityMigration, 'CREATE TABLE IF NOT EXISTS MCP014') === false, 'New installations must not create obsolete MCP013/MCP014 tables.');
mcpAssert(strpos((string) $eligibilityMigration, 'MCP016_C021_Id') !== false && strpos((string) $eligibilityMigration, 'MCP015_Scope') === false && strpos((string) $eligibilityMigration, 'MCP017_Scope') === false, 'Eligibility tables must use ERP profiles and contain no scopes.');
$resolverSource = file_get_contents(dirname(__DIR__, 2) . '/src/Eligibility/McpConnectionEligibilityRepository.php');
mcpAssert($resolverSource !== false && strpos($resolverSource, 'MCP015') !== false && strpos($resolverSource, 'MCP016') !== false && strpos($resolverSource, 'MCP017') !== false && strpos($resolverSource, 'C019') !== false && strpos($resolverSource, 'MCP014') === false, 'Eligibility resolver must allow all-users, direct and ERP-profile membership paths without MCP profiles.');
mcpAssert($resolverSource !== false && strpos($resolverSource, "MCP001_Status='active'") !== false && strpos($resolverSource, "C007_Ativo='S'") !== false && strpos($resolverSource, "FROM C019 membership") !== false && strpos($resolverSource, "membership.C019_C004_Id=c.MCP001_C004_Id") !== false && strpos($resolverSource, "SELECT DISTINCT") !== false, "Eligibility resolver must require an active user, company membership and deduplicated connections.");
$oauthAdminSource = file_get_contents(dirname(__DIR__, 2) . '/src/Admin/OAuthAdminService.php');
mcpAssert($oauthAdminSource !== false && strpos($oauthAdminSource, 'McpConnectionEligibilityResolver') !== false, 'Manual OAuth grants must validate connection eligibility.');
$eligibilityAdminSource = file_get_contents(dirname(__DIR__, 2) . '/src/Admin/McpConnectionEligibilityAdminService.php');
mcpAssert($eligibilityAdminSource !== false && strpos($eligibilityAdminSource, 'mcp_eligibility_user_added') !== false && strpos($eligibilityAdminSource, 'mcp_eligibility_profile_revoked') !== false && strpos($eligibilityAdminSource, 'mcp_eligibility_all_users_enabled') !== false, 'Eligibility mutations must be audited.');
$eligibilityJs = file_get_contents(dirname(__DIR__, 2) . '/admin/assets/mcp-admin.js');
mcpAssert($eligibilityJs !== false && strpos($eligibilityJs, 'data-detail-tab="access"') !== false && strpos($eligibilityJs, 'data-access-tab="all"') !== false && strpos($eligibilityJs, 'Adicionar usuário') !== false, 'MCP detail must expose the reviewed Access tabs.');
