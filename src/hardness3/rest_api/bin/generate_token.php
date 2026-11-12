<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);
$vendorAutoload = $rootPath . '/vendor/autoload.php';

if (file_exists($vendorAutoload)) {
    require $vendorAutoload;
} else {
    spl_autoload_register(static function (string $class) use ($rootPath): void {
        $prefix = 'RestApi\\';
        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $rootPath . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });
}

$config = new RestApi\Core\Config($rootPath . '/config');
$appConfig = $config->load('app');
$appKey = (string) ($appConfig['app_key'] ?? '');

if ($appKey === '') {
    fwrite(STDERR, "APP key não configurada.\n");
    exit(1);
}

$environment = getenv('REST_API_TOKEN_ENV') ?: 'live';
$prefixBase = $environment === 'test' ? 'hrd_test_' : 'hrd_live_';
$token = $prefixBase . rtrim(strtr(base64_encode(random_bytes(24)), '+/', '-_'), '=');
$hash = hash_hmac('sha256', $token, $appKey);
$prefixLength = (int) (($appConfig['auth']['token_prefix_length'] ?? 12));
$tokenPrefix = substr($token, 0, $prefixLength);

echo "Token gerado com sucesso.\n";
echo "Mostre o token completo apenas uma vez.\n\n";
echo "Token: {$token}\n";
echo "Prefixo: {$tokenPrefix}\n";
echo "Hash HMAC: {$hash}\n";
echo "Status sugerido: active\n";
