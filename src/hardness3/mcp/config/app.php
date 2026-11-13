<?php

declare(strict_types=1);

/*
 * getenv() is the production contract. This opt-in file exists solely so the
 * bind-mounted local development installation can test OAuth without changing
 * the current container. It is never consulted when MCP_ENVIRONMENT declares
 * any environment other than dev.
 */
$mcpEnvironment = (string) (getenv('MCP_ENVIRONMENT') ?: '');
$localDevelopment = [];
$localDevelopmentFile = getenv('MCP_DEV_LOCAL_CONFIG_PATH') ?: '/www/.local-secrets/mcp.local.php';
if (($mcpEnvironment === '' || $mcpEnvironment === 'dev') && is_file($localDevelopmentFile) && !is_link($localDevelopmentFile)) {
    $permissions = fileperms($localDevelopmentFile);
    if ($permissions === false || (($permissions & 0777) !== 0600)) {
        throw new RuntimeException('Local MCP development configuration permissions are invalid.');
    }
    $candidate = require $localDevelopmentFile;
    if (!is_array($candidate) || ($candidate['environment'] ?? null) !== 'dev') {
        throw new RuntimeException('Local MCP development configuration is invalid.');
    }
    $localDevelopment = $candidate;
    $mcpEnvironment = 'dev';
}
$mcpEnv = static function (string $name) use ($localDevelopment): string {
    $value = getenv($name);
    if (is_string($value) && $value !== '') { return $value; }
    $fallback = $localDevelopment[$name] ?? '';
    return is_string($fallback) ? $fallback : '';
};

$credentialPeppers = [];
$credentialPeppersJson = $mcpEnv('MCP_CREDENTIAL_PEPPERS_JSON');
if (is_string($credentialPeppersJson) && $credentialPeppersJson !== '') {
    $decodedCredentialPeppers = json_decode($credentialPeppersJson, true);
    if (is_array($decodedCredentialPeppers)) {
        foreach ($decodedCredentialPeppers as $version => $pepper) {
            if (is_string($version) && is_string($pepper)) {
                $credentialPeppers[$version] = $pepper;
            }
        }
    }
} else {
    // Backward-compatible source for the initial deployment.
    $credentialPeppers['v1'] = $mcpEnv('MCP_CREDENTIAL_PEPPER_V1');
}
$authorizationRequestPeppers = [];
$authorizationRequestPeppersJson = $mcpEnv('MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPERS_JSON');
if (is_string($authorizationRequestPeppersJson) && $authorizationRequestPeppersJson !== '') { $decoded=json_decode($authorizationRequestPeppersJson,true); if(is_array($decoded))foreach($decoded as $version=>$pepper)if(is_string($version)&&is_string($pepper))$authorizationRequestPeppers[$version]=$pepper; }
else { $authorizationRequestPeppers['v1'] = $mcpEnv('MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPER_V1'); }
$authorizationCodePeppers = [];
$authorizationCodePeppersJson = $mcpEnv('MCP_OAUTH_AUTHORIZATION_CODE_PEPPERS_JSON');
if (is_string($authorizationCodePeppersJson) && $authorizationCodePeppersJson !== '') { $decoded=json_decode($authorizationCodePeppersJson,true); if(is_array($decoded))foreach($decoded as $version=>$pepper)if(is_string($version)&&is_string($pepper))$authorizationCodePeppers[$version]=$pepper; }
else { $authorizationCodePeppers['v1'] = $mcpEnv('MCP_OAUTH_AUTHORIZATION_CODE_PEPPER_V1') ?: $mcpEnv('MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPER_V1'); }
$accessTokenPeppers = [];$accessTokenPeppersJson=$mcpEnv('MCP_OAUTH_ACCESS_TOKEN_PEPPERS_JSON');
if($accessTokenPeppersJson!==''){ $decoded=json_decode($accessTokenPeppersJson,true);if(is_array($decoded))foreach($decoded as $v=>$p)if(is_string($v)&&is_string($p))$accessTokenPeppers[$v]=$p; } else $accessTokenPeppers['v1']=$mcpEnv('MCP_OAUTH_ACCESS_TOKEN_PEPPER_V1') ?: ($mcpEnvironment==='dev' ? $mcpEnv('MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPER_V1') : '');
$refreshTokenPeppers = [];$refreshTokenPeppersJson=$mcpEnv('MCP_OAUTH_REFRESH_TOKEN_PEPPERS_JSON');
if($refreshTokenPeppersJson!==''){ $decoded=json_decode($refreshTokenPeppersJson,true);if(is_array($decoded))foreach($decoded as $v=>$p)if(is_string($v)&&is_string($p))$refreshTokenPeppers[$v]=$p; } else $refreshTokenPeppers['v1']=$mcpEnv('MCP_OAUTH_REFRESH_TOKEN_PEPPER_V1') ?: ($mcpEnvironment==='dev' ? $mcpEnv('MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPER_V1') : '');

return [
    'server' => [
        'name' => 'Hardness MCP',
        'version' => '0.1.0',
        'protocol_version' => '2025-11-25',
    ],
    'endpoint_path' => '/mcp',
    'transport' => [
        // Origins are intentionally deny-by-default. Native MCP clients normally omit Origin.
        'allowed_origins' => [],
    ],
    'tenant' => [
        'conf_path' => getenv('MCP_TENANT_CONF_PATH') ?: '/var/www/sites',
    ],
    'database' => [
        'charset' => 'utf8mb4',
        'default_port' => 3306,
        'pdo_options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => (int) (getenv('MCP_DB_CONNECT_TIMEOUT_SECONDS') ?: 3),
        ],
    ],
    'limits' => [
        'max_payload_bytes' => \HardnessMcp\Tools\Product\Prices\PriceBatchLimits::payloadBytes(),
    ],
    'auth' => [
        'credential_pepper_current_version' => $mcpEnv('MCP_CREDENTIAL_PEPPER_CURRENT_VERSION') ?: 'v1',
        // JSON object, e.g. {"v1":"old secret","v2":"current secret"}.
        'credential_peppers' => $credentialPeppers,
        'credential_rotation_grace_seconds' => (int) (getenv('MCP_CREDENTIAL_ROTATION_GRACE_SECONDS') ?: 86400),
    ],
    'oauth' => [
        // Temporary interoperability diagnostics. Remove after the ChatGPT DCR capture.
        // Emits only a secret-free DCR summary to PHP's error log.
        'dcr_diagnostics_enabled' => true,
        // Enable only while diagnosing a specific MCP/OAuth interoperability issue.
        'http_diagnostics_enabled' => false,
        'authorization_request_pepper_current_version' => $mcpEnv('MCP_OAUTH_AUTHORIZATION_REQUEST_PEPPER_CURRENT_VERSION') ?: 'v1',
        'authorization_request_peppers' => $authorizationRequestPeppers,
        'authorization_code_pepper_current_version' => $mcpEnv('MCP_OAUTH_AUTHORIZATION_CODE_PEPPER_CURRENT_VERSION') ?: 'v1',
        'authorization_code_peppers' => $authorizationCodePeppers,
        'access_token_pepper_current_version' => $mcpEnv('MCP_OAUTH_ACCESS_TOKEN_PEPPER_CURRENT_VERSION') ?: 'v1',
        'access_token_peppers' => $accessTokenPeppers,
        'refresh_token_pepper_current_version' => $mcpEnv('MCP_OAUTH_REFRESH_TOKEN_PEPPER_CURRENT_VERSION') ?: 'v1',
        'refresh_token_peppers' => $refreshTokenPeppers,
        'access_token_ttl_seconds' => (int) ($mcpEnv('MCP_OAUTH_ACCESS_TOKEN_TTL_SECONDS') ?: 600),
        'refresh_token_ttl_seconds' => (int) ($mcpEnv('MCP_OAUTH_REFRESH_TOKEN_TTL_SECONDS') ?: 2592000),
    ],
    'rate_limit' => [
        'driver' => getenv('MCP_RATE_LIMIT_DRIVER') ?: 'apcu',
        // Secret distinct from credential peppers; it prevents readable limiter keys.
        'key_pepper' => getenv('MCP_RATE_LIMIT_KEY_PEPPER') ?: '',
        'preauth' => ['limit' => (int) (getenv('MCP_RATE_LIMIT_PREAUTH_PER_MINUTE') ?: 60), 'window_seconds' => 60],
        'authentication_failure' => ['limit' => (int) (getenv('MCP_RATE_LIMIT_FAILURES_PER_5_MINUTES') ?: 8), 'window_seconds' => 300],
        'connection' => ['limit' => (int) (getenv('MCP_RATE_LIMIT_CONNECTION_PER_MINUTE') ?: 120), 'window_seconds' => 60],
        // Applied in addition to the connection limit, so separate OAuth tokens cannot bypass it.
        'oauth_access_token' => ['limit' => (int) (getenv('MCP_RATE_LIMIT_OAUTH_ACCESS_TOKEN_PER_MINUTE') ?: 120), 'window_seconds' => 60],
        'dcr_registration' => ['limit' => (int) (getenv('MCP_RATE_LIMIT_DCR_PER_HOUR') ?: 20), 'window_seconds' => 3600],
        'oauth_token' => ['limit' => (int) (getenv('MCP_RATE_LIMIT_OAUTH_TOKEN_PER_MINUTE') ?: 30), 'window_seconds' => 60],
    ],
    'partner' => ['candidate_ref_pepper' => $mcpEnv('MCP_PARTNER_CANDIDATE_REF_PEPPER') ?: (getenv('MCP_RATE_LIMIT_KEY_PEPPER') ?: ''), 'candidate_ref_ttl_seconds' => (int) ($mcpEnv('MCP_PARTNER_CANDIDATE_REF_TTL_SECONDS') ?: 600)],
    'audit' => [
        // Retention is operational policy. Etapa 2 never deletes audit rows automatically.
        'retention_days' => (int) (getenv('MCP_AUDIT_RETENTION_DAYS') ?: 365),
    ],
];
