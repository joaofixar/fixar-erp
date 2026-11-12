<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Commercial\Domain;

interface OpportunityRepositoryInterface
{
    /** @return array<int,array<string,mixed>> */
    public function find(int $companyId, string $from, string $to, string $type, int $minimumAge, ?int $customerId, ?int $sellerId, ?string $productCode, float $minimumPending, ?array $cursor, int $limit): array;
}
