<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Conversion\Domain;

interface ProductConversionRepositoryInterface
{
    /** @return array<string,mixed> */
    public function summary(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?string $productCode): array;
    /** @return array<int,array<string,mixed>> */
    public function products(int $companyId, string $from, string $to, ?int $customerId, ?int $sellerId, ?string $productCode, string $groupBy, int $limit): array;
}
