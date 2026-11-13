<?php
declare(strict_types=1);

define('HARDNESS_OBSERVABILITY_ROOT', dirname(__DIR__));

require HARDNESS_OBSERVABILITY_ROOT . '/src/Support/Autoloader.php';

Hardness\Observability\Support\Autoloader::register(HARDNESS_OBSERVABILITY_ROOT . '/src');

error_reporting(E_ALL);
ini_set('display_errors', '0');
date_default_timezone_set('America/Sao_Paulo');
