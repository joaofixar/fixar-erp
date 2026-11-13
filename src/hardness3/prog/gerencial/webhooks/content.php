<?php

declare(strict_types=1);

namespace hardness;

use HardnessWebhooks\Admin\FrameworkEntry;

require_once dirname(__DIR__, 3) . '/webhooks/autoload.php';

if (($g['r'][3] ?? '') === 'WebhooksAdminContent') {
    FrameworkEntry::render($g, isset($confUsuario) && is_array($confUsuario) ? $confUsuario : array());
}
