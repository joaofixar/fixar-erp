<?php

declare(strict_types=1);

namespace HardnessWebhooks\Payload;

interface PayloadBuilderInterface
{
    public function eventType(): string;

    /**
     * Resolves the public event snapshot through the caller's current
     * connection. Implementations must not manage transactions.
     *
     * @return array<string,mixed>
     */
    public function build(\mysqli $connection, int $companyId, int $aggregateId): array;
}
