<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Domain;

interface QuoteRepositoryInterface
{
    /** @return array<int,array<string,mixed>> */
    public function search(int $companyId, string $issuedFrom, string $issuedTo, ?int $customerId, ?string $origin, int $limit): array;
    /** @return array{header:array<string,mixed>,items:array<int,array<string,mixed>>}|null */
    public function findById(int $companyId, int $quoteId): ?array;
}
