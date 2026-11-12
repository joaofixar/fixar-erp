<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

/**
 * Deliberately small seam for the future HTTP transport. Task 07 dispatchers
 * must not perform network I/O; the production default is a no-op.
 */
interface DeliveryDispatcherInterface
{
    /** @param array<string,mixed> $delivery */
    public function dispatch(\mysqli $connection, string $workerId, array $delivery): void;
}
