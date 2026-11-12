<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Conversion;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Conversion\Domain\ProductConversionRepositoryInterface;
use HardnessMcp\Tools\ItemReport\ItemReportInput;

final class ProductConversionHandler
{
    private ProductConversionRepositoryInterface $repository;
    public function __construct(ProductConversionRepositoryInterface $repository) { $this->repository = $repository; }
    /** @return array<string,mixed> */
    public function handle(ItemReportInput $input, ExecutionContext $context): array
    {
        $summary = $this->metrics($this->repository->summary($context->getCompanyId(), $input->from(), $input->to(), $input->customer(), $input->seller(), $input->productCode()));
        $rows = $this->repository->products($context->getCompanyId(), $input->from(), $input->to(), $input->customer(), $input->seller(), $input->productCode(), $input->groupBy(), $input->limit() + 1);
        $hasMore = count($rows) > $input->limit(); if ($hasMore) $rows = array_slice($rows, 0, $input->limit()); foreach ($rows as &$row) $row = $this->metrics($row); unset($row);
        return ['report' => 'product_conversion_summary', 'definition' => 'Funil por produto: orçamento pela data de emissão, com pedidos e NF-e autorizadas vinculados aos seus itens.', 'period' => ['from' => $input->from(), 'to' => $input->to()], 'group_by' => $input->groupBy(), 'filters_applied' => array_filter(['customer_id' => $input->customer(), 'seller_internal_id' => $input->seller(), 'product_code' => $input->productCode()], static fn($value) => $value !== null), 'summary' => $summary, 'rows' => $rows, 'has_more' => $hasMore];
    }
    /** @param array<string,mixed> $row @return array<string,mixed> */
    private function metrics(array $row): array
    {
        $quoted = (float) ($row['quoted_quantity'] ?? 0); $ordered = (float) ($row['ordered_quantity'] ?? 0); $invoiced = (float) ($row['invoiced_quantity'] ?? 0); $net = (float) ($row['invoiced_net_total'] ?? 0); $cost = (float) ($row['invoiced_cost_total'] ?? 0);
        foreach (['quoted_quantity', 'ordered_quantity', 'invoiced_quantity', 'invoiced_net_total', 'invoiced_cost_total'] as $field) $row[$field] = (string) ($row[$field] ?? '0');
        $row['quote_to_order_percent'] = $quoted == 0.0 ? null : (string) ($ordered * 100 / $quoted); $row['order_to_invoice_percent'] = $ordered == 0.0 ? null : (string) ($invoiced * 100 / $ordered); $row['quote_to_invoice_percent'] = $quoted == 0.0 ? null : (string) ($invoiced * 100 / $quoted);
        $row['pending_order_quantity'] = (string) max(0, $quoted - $ordered); $row['pending_invoice_quantity'] = (string) max(0, $ordered - $invoiced); $row['invoiced_gross_profit'] = (string) ($net - $cost); $row['invoiced_gross_margin_percent'] = $net == 0.0 ? null : (string) (($net - $cost) * 100 / $net);
        return $row;
    }
}
