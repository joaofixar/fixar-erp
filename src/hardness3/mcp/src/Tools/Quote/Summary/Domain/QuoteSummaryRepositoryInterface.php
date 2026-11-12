<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Summary\Domain;

interface QuoteSummaryRepositoryInterface
{
    /** @return array{quoted_total:string,quote_count:int} */
    public function summary(int $companyId, string $dateFrom, string $dateTo, ?int $customerId, ?int $sellerInternalId, ?int $supervisorId, ?string $status, ?string $origin): array;
    /** @return array<int,array<string,mixed>> */
    public function grouped(int $companyId, string $dateFrom, string $dateTo, string $groupBy, ?int $customerId, ?int $sellerInternalId, ?int $supervisorId, ?string $status, ?string $origin, int $limit): array;
}
