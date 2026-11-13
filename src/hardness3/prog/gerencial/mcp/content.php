<?php

declare(strict_types=1);

namespace hardness;

use HardnessMcp\AdminUi\FrameworkEntry;

require_once dirname(__DIR__, 3) . '/mcp/autoload.php';

if (($g['r'][3] ?? '') === 'McpAdminContent') {
    FrameworkEntry::render($g, $confUsuario);
}

if (($g['r'][3] ?? '') === 'McpAdminStandalone') {
    FrameworkEntry::renderStandalone($g, $confUsuario);
}
