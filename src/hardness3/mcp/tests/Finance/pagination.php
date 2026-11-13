<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/autoload.php';

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Finance\Domain\FinanceSummaryRepositoryInterface;
use HardnessMcp\Tools\Finance\FinanceSummaryHandler;
use HardnessMcp\Tools\Finance\FinanceSummaryInput;

final class FinancePaginationRepository implements FinanceSummaryRepositoryInterface
{
    /** @var array<int,array<string,mixed>> */
    public array $rows = [
        ['title_id' => 10, 'due_date' => '2026-08-01', 'partner_id' => 7, 'partner_name' => 'Cliente', 'carrier_id' => 3, 'carrier_name' => 'Banco', 'origin' => 'manual', 'invoice_number' => null, 'invoice_issued_date' => null, 'document_number' => 'A', 'settlement_date' => null, 'total_amount' => '10.00', 'open_amount' => '10.00', 'title_status' => 'open'],
        ['title_id' => 11, 'due_date' => '2026-08-01', 'partner_id' => 7, 'partner_name' => 'Cliente', 'carrier_id' => 3, 'carrier_name' => 'Banco', 'origin' => 'manual', 'invoice_number' => null, 'invoice_issued_date' => null, 'document_number' => 'B', 'settlement_date' => null, 'total_amount' => '20.00', 'open_amount' => '20.00', 'title_status' => 'open'],
        ['title_id' => 12, 'due_date' => '2026-08-02', 'partner_id' => 7, 'partner_name' => 'Cliente', 'carrier_id' => 3, 'carrier_name' => 'Banco', 'origin' => 'manual', 'invoice_number' => null, 'invoice_issued_date' => null, 'document_number' => 'C', 'settlement_date' => null, 'total_amount' => '30.00', 'open_amount' => '30.00', 'title_status' => 'open'],
    ];
    /** @var array<string,mixed>|null */ public ?array $lastCall = null;
    public function summary(string $kind, int $companyId, string $dateFrom, string $dateTo, string $status, ?int $partnerId, ?int $carrierId, string $dateBasis = 'due_date'): array { return []; }
    public function grouped(string $kind, int $companyId, string $dateFrom, string $dateTo, string $status, ?int $partnerId, ?int $carrierId, string $groupBy, int $limit, string $dateBasis = 'due_date'): array { return []; }
    public function titles(string $kind, int $companyId, string $dateFrom, string $dateTo, string $status, ?int $partnerId, ?int $carrierId, int $limit, string $dateBasis = 'due_date', ?array $cursor = null): array
    {
        $this->lastCall = compact('kind', 'companyId', 'dateFrom', 'dateTo', 'status', 'partnerId', 'carrierId', 'limit', 'dateBasis', 'cursor');
        $rows = array_values(array_filter($this->rows, static fn(array $row): bool => $cursor === null || $row['due_date'] > $cursor['due_date'] || ($row['due_date'] === $cursor['due_date'] && $row['title_id'] > $cursor['title_id'])));
        return array_slice($rows, 0, $limit);
    }
}

function assertFinancePagination(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }

$repository = new FinancePaginationRepository();
$handler = new FinanceSummaryHandler('receivable', $repository);
$context = new ExecutionContext(1, 1, 1, null, ['finance:receivables:read']);
$arguments = ['date_from' => '2026-08-01', 'date_to' => '2026-08-31', 'group_by' => 'day', 'view' => 'titles', 'status' => 'open', 'partner_id' => 7, 'carrier_id' => 3, 'limit' => 2];
$first = $handler->handle(FinanceSummaryInput::fromArguments($arguments), $context);
assertFinancePagination(array_column($first['rows'], 'title_id') === [10, 11], 'first page must contain only the requested limit');
assertFinancePagination($first['has_more'] === true && is_string($first['next_cursor']), 'first page must expose a continuation cursor');
assertFinancePagination(($repository->lastCall['limit'] ?? null) === 3 && ($repository->lastCall['status'] ?? null) === 'open' && ($repository->lastCall['partnerId'] ?? null) === 7 && ($repository->lastCall['carrierId'] ?? null) === 3, 'first page must request limit + 1 with its filters');
$second = $handler->handle(FinanceSummaryInput::fromArguments($arguments + ['cursor' => $first['next_cursor']]), $context);
assertFinancePagination(array_column($second['rows'], 'title_id') === [12] && $second['has_more'] === false && $second['next_cursor'] === null, 'second page must continue strictly after due date and title ID');
assertFinancePagination(($repository->lastCall['cursor'] ?? null) === ['due_date' => '2026-08-01', 'title_id' => 11], 'decoded cursor must be passed to the repository');
assertFinancePagination(($second['filters_applied'] ?? null) === ['date_basis' => 'due_date', 'status' => 'open', 'partner_id' => 7, 'carrier_id' => 3], 'filters must be identical and visible on every titles page');

echo "Finance titles cursor pagination: OK\n";
