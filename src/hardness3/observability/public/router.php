<?php
declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    if (is_string($path) && strpos($path, '/observability/') === 0) {
        $path = substr($path, strlen('/observability'));
    }
    if (is_string($path) && strpos($path, '/assets/') === 0) {
        $asset = realpath(__DIR__ . $path);
        $assetsRoot = realpath(__DIR__ . '/assets');
        if ($asset !== false && $assetsRoot !== false && strpos($asset, $assetsRoot . DIRECTORY_SEPARATOR) === 0 && is_file($asset)) {
            $extension = pathinfo($asset, PATHINFO_EXTENSION);
            header($extension === 'css' ? 'Content-Type: text/css; charset=UTF-8' : 'Content-Type: application/javascript; charset=UTF-8');
            readfile($asset);
            exit;
        }
    }
    if (is_string($path) && is_file(__DIR__ . $path)) {
        return false;
    }
}

require __DIR__ . '/index.php';
