<?php

return [
    'app_name' => 'Hardness REST API',
    'api_version' => 'v1',
    'base_path' => '/api/v1',
    'app_key' => getenv('REST_API_APP_KEY') ?: '',
    'debug' => [
        'enable_tenant_check' => getenv('REST_API_ENABLE_TENANT_CHECK') === '1',
    ],
    'tenant' => [
        // Production default: /var/www/sites/{host}-confUsuario.php
        'conf_path' => getenv('REST_API_TENANT_CONF_PATH') ?: '/var/www/sites',
    ],
    'database' => [
        'charset' => 'utf8mb4',
        'default_port' => 3306,
        'pdo_options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
    'schema_cache' => [
        'enabled' => true,
        'path' => __DIR__ . '/../storage/cache',
        'ttl_seconds' => 300,
    ],
    'audit' => [
        'enabled' => true,
        'log_path' => __DIR__ . '/../storage/logs/rest_api_audit_error.log',
        'max_payload_bytes' => 16384,
        'max_response_bytes' => 16384,
    ],
    'docs' => [
        'swagger_ui_enabled' => true,
        'swagger_ui_path' => '/api/docs',
        'openapi_path' => '/api/docs/openapi.yaml',
        'postman_collection_path' => '/api/docs/postman_collection.json',
    ],
    'policy' => [
        'tenant_data_base_path' => dirname(__DIR__, 2) . '/dados_usuarios',
        'tenant_data_relative_path' => 'rest_api/access_policy.php',
        'base_path' => getenv('REST_API_POLICY_PATH') ?: (__DIR__ . '/policies/clients'),
    ],
    'limits' => [
        'default_limit' => 25,
        'max_limit' => 100,
        'max_offset' => 10000,
        'max_in_values' => 100,
        'max_payload_kb' => 256,
    ],
];
