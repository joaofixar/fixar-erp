<?php
declare(strict_types=1);

namespace Hardness\Observability\Support;

final class Autoloader
{
    public static function register(string $sourceDirectory): void
    {
        spl_autoload_register(function (string $class) use ($sourceDirectory): void {
            $prefix = 'Hardness\\Observability\\';
            if (strpos($class, $prefix) !== 0) {
                return;
            }

            $relative = substr($class, strlen($prefix));
            $file = $sourceDirectory . '/' . str_replace('\\', '/', $relative) . '.php';
            if (is_file($file)) {
                require $file;
            }
        });
    }
}
