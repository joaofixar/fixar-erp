<?php

declare(strict_types=1);

use HardnessMcp\Tools\Order\OrderBacklogInput;
use HardnessMcp\Tools\Quote\Summary\QuoteSummaryInput;
use HardnessMcp\Tools\Sales\SalesSummaryInput;

/** @param callable(string,string,string):object $factory */
function mcpAssertReportPeriodRejected(callable $factory, string $groupBy, string $from, string $to, string $messagePart, string $label): void
{
    try {
        $factory($groupBy, $from, $to);
        throw new RuntimeException($label . ' should have rejected the period');
    } catch (InvalidArgumentException $exception) {
        mcpAssert(strpos($exception->getMessage(), $messagePart) !== false, $label . ' must explain the applicable period limit');
        mcpAssert(strpos($exception->getMessage(), 'Não divida') !== false, $label . ' must prohibit splitting a longer period into sequential calls');
    }
}

$reportPeriodFactories = [
    'quote' => static function (string $groupBy, string $from, string $to): QuoteSummaryInput {
        return QuoteSummaryInput::fromArguments(['date_from' => $from, 'date_to' => $to, 'group_by' => $groupBy]);
    },
    'order' => static function (string $groupBy, string $from, string $to): OrderBacklogInput {
        return OrderBacklogInput::fromArguments(['date_from' => $from, 'date_to' => $to, 'group_by' => $groupBy]);
    },
    'sales' => static function (string $groupBy, string $from, string $to): SalesSummaryInput {
        return SalesSummaryInput::fromArguments(['date_from' => $from, 'date_to' => $to, 'group_by' => $groupBy]);
    },
];

$historicalGroups = [
    'quote' => ['month', 'customer', 'seller_internal', 'supervisor', 'status'],
    'order' => ['month', 'customer', 'seller_internal', 'status'],
    'sales' => ['month', 'customer', 'seller_internal'],
];

foreach ($reportPeriodFactories as $report => $factory) {
    foreach ($historicalGroups[$report] as $groupBy) {
        $accepted = $factory($groupBy, '2021-01-01', '2026-01-01');
        mcpAssert($accepted !== null, $report . ' ' . $groupBy . ' must accept an exact five-year historical period');
        mcpAssertReportPeriodRejected($factory, $groupBy, '2021-01-01', '2026-01-02', '5 anos', $report . ' ' . $groupBy);
    }

    $factory('day', '2026-01-01', '2026-03-31');
    mcpAssertReportPeriodRejected($factory, 'day', '2026-01-01', '2026-04-01', '90 dias', $report . ' day');

    foreach (['week', 'document'] as $groupBy) {
        $factory($groupBy, '2023-03-01', '2024-03-01');
        mcpAssertReportPeriodRejected($factory, $groupBy, '2025-01-01', '2026-01-02', '12 meses', $report . ' ' . $groupBy);
    }
}
