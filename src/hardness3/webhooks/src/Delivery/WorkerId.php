<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

final class WorkerId
{
    public static function generate(): string
    {
        $hostname = gethostname();
        $safeHost = is_string($hostname) && $hostname !== '' ? preg_replace('/[^A-Za-z0-9._-]/', '-', $hostname) : 'unknown-host';
        return substr((string) $safeHost, 0, 100) . ':' . getmypid() . ':' . bin2hex(random_bytes(6));
    }
}
