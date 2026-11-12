<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);
require $rootPath . '/autoload.php';

$bootstrap = new RestApi\Core\Bootstrap($rootPath);
$bootstrap->run();
