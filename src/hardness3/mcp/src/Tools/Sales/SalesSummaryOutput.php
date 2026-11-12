<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Sales;

final class SalesSummaryOutput
{
    /** @param array<string,mixed> $summary @return array{invoiced_total:string,invoice_count:int} */
    public static function summary(array $summary): array
    {
        return ['invoiced_total' => (string) ($summary['invoiced_total'] ?? '0'), 'invoice_count' => (int) ($summary['invoice_count'] ?? 0)];
    }

    /** @param array<int,array<string,mixed>> $rows @return array<int,array<string,mixed>> */
    public static function rows(array $rows, string $groupBy): array
    {
        $result = [];
        foreach ($rows as $row) {
            $base = ['invoiced_total' => (string) ($row['invoiced_total'] ?? '0'), 'invoice_count' => (int) ($row['invoice_count'] ?? 0)];
            if ($groupBy === 'day') { $result[] = array_merge(['date' => (string) $row['date']], $base); }
            if ($groupBy === 'week') { $result[] = array_merge(['week_start' => (string) $row['week_start']], $base); }
            if ($groupBy === 'month') { $result[] = array_merge(['month' => (string) $row['month']], $base); }
            if ($groupBy === 'customer') { $result[] = array_merge(['customer_id' => $row['customer_id'] === null ? null : (int) $row['customer_id'], 'customer_name' => self::nullableString($row['customer_name'] ?? null)], $base); }
            if ($groupBy === 'document') { $result[] = ['document_id' => (int) $row['document_id'], 'invoice_number' => self::nullableString($row['invoice_number'] ?? null), 'issued_at' => self::nullableString($row['issued_at'] ?? null), 'customer_name' => self::nullableString($row['customer_name'] ?? null), 'customer_group_name' => self::nullableString($row['customer_group_name'] ?? null), 'proposal_customer_name' => self::nullableString($row['proposal_customer_name'] ?? null), 'invoiced_total' => (string) ($row['invoiced_total'] ?? '0'), 'invoice_count' => 1]; }
            if ($groupBy === 'seller_internal') { $result[] = array_merge(['seller_internal_id' => $row['seller_internal_id'] === null ? null : (int) $row['seller_internal_id'], 'seller_internal_name' => self::nullableString($row['seller_internal_name'] ?? null)], $base); }
        }
        return $result;
    }

    /** @param mixed $value */
    private static function nullableString($value): ?string { return $value === null ? null : (string) $value; }
}
