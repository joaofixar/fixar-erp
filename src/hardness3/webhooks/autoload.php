<?php

declare(strict_types=1);

$webhooksRootPath = __DIR__;

spl_autoload_register(static function (string $class) use ($webhooksRootPath): void {
    $prefix = 'HardnessWebhooks\\';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    if ($relativeClass === 'Admin\\FrameworkEntry') {
        require_once $webhooksRootPath . '/admin/FrameworkEntry.php';
        return;
    }
    $file = $webhooksRootPath . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
