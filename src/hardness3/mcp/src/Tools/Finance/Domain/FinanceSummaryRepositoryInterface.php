<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Finance\Domain;
interface FinanceSummaryRepositoryInterface {
    /** @return array<string,mixed> */
    public function summary(string $kind, int $companyId, string $dateFrom, string $dateTo, string $status, ?int $partnerId, ?int $carrierId, string $dateBasis = 'due_date'): array;
    /** @return array<int,array<string,mixed>> */
    public function grouped(string $kind, int $companyId, string $dateFrom, string $dateTo, string $status, ?int $partnerId, ?int $carrierId, string $groupBy, int $limit, string $dateBasis = 'due_date'): array;
    /** @return array<int,array<string,mixed>> */
    public function titles(string $kind, int $companyId, string $dateFrom, string $dateTo, string $status, ?int $partnerId, ?int $carrierId, int $limit, string $dateBasis = 'due_date', ?array $cursor = null): array;
}
