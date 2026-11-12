<?php

declare(strict_types=1);

namespace hardness;

use HardnessWebhooks\Admin\FrameworkEntry;

require_once dirname(__DIR__, 3) . '/webhooks/autoload.php';

if (($g['r'][3] ?? '') === 'WebhooksAdminRead') {
    FrameworkEntry::read($g);
}

if (($g['r'][3] ?? '') === 'WebhooksAdminMutation') {
    FrameworkEntry::mutation($g);
}
