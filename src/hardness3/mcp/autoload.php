<?php

declare(strict_types=1);

$rootPath = __DIR__;
$vendorAutoload = $rootPath . '/vendor/autoload.php';

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
    return;
}

spl_autoload_register(static function (string $class) use ($rootPath): void {
    $adminPrefix = 'HardnessMcp\\AdminUi\\';
    if (strpos($class, $adminPrefix) === 0) {
        $relativeClass = substr($class, strlen($adminPrefix));
        $file = $rootPath . '/admin/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
        return;
    }

    $prefix = 'HardnessMcp\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $rootPath . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
