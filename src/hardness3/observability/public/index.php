<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/bootstrap.php';

use Hardness\Observability\Application;

$application = getenv('OBSERVABILITY_DEVELOPMENT_MOCK') === '1' ? Application::createPrototype() : Application::createProduction();
$application->run();
