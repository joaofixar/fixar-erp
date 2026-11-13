<?php

declare(strict_types=1);

use HardnessWebhooks\Core\Bootstrap;

require_once __DIR__ . '/autoload.php';

return (new Bootstrap(__DIR__))->boot();
