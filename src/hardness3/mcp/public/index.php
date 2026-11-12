<?php

declare(strict_types=1);

// Never allow PHP diagnostics to corrupt the MCP HTTP response with HTML.
ini_set('display_errors', '0');
ini_set('html_errors', '0');

$rootPath = dirname(__DIR__);
require $rootPath . '/autoload.php';

$bootstrap = new HardnessMcp\Core\Bootstrap($rootPath);
$bootstrap->run();
