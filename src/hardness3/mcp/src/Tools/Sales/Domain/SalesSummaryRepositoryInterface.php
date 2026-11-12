<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Sales\Domain;

interface SalesSummaryRepositoryInterface
{
    /** @return array{invoiced_total:string,invoice_count:int} */
    public function summary(int $companyId, string $dateFrom, string $dateTo, ?int $customerId, ?int $sellerInternalId): array;

    /** @return array<int,array<string,mixed>> */
    public function grouped(int $companyId, string $dateFrom, string $dateTo, string $groupBy, ?int $customerId, ?int $sellerInternalId, int $limit): array;
}
