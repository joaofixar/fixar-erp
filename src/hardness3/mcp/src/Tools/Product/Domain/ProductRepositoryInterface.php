<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\Domain;

interface ProductRepositoryInterface
{
    /** @return array<int,array<string,mixed>> */
    public function search(int $companyId, string $query, int $limit): array;
}
