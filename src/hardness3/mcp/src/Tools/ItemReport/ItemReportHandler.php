<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\ItemReport;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\ItemReport\Domain\ItemReportRepositoryInterface;

final class ItemReportHandler
{
    private ItemReportRepositoryInterface $repository; private string $source;
    public function __construct(ItemReportRepositoryInterface $repository, string $source) { $this->repository = $repository; $this->source = $source; }
    /** @return array<string,mixed> */
    public function handle(ItemReportInput $input, ExecutionContext $context): array
    {
        $summary = $this->repository->summary($context->getCompanyId(), $input->from(), $input->to(), $input->customer(), $input->seller(), $input->productCode());
        $rows = $this->repository->products($context->getCompanyId(), $input->from(), $input->to(), $input->customer(), $input->seller(), $input->productCode(), $input->groupBy(), $input->limit() + 1);
        $hasMore = count($rows) > $input->limit(); if ($hasMore) $rows = array_slice($rows, 0, $input->limit());
        $summary = $this->metrics($summary); foreach ($rows as &$row) $row = $this->metrics($row); unset($row);
        $labels = ['quote' => 'Itens de orçamentos emitidos.', 'order' => 'Itens de pedidos emitidos.', 'sales' => 'Itens de notas fiscais de saída autorizadas.'];
        return ['report' => $this->source . '_item_summary', 'definition' => $labels[$this->source], 'period' => ['from' => $input->from(), 'to' => $input->to()], 'group_by' => $input->groupBy(), 'filters_applied' => array_filter(['customer_id' => $input->customer(), 'seller_internal_id' => $input->seller(), 'product_code' => $input->productCode()], static fn($value) => $value !== null), 'summary' => $summary, 'rows' => $rows, 'has_more' => $hasMore];
    }
    /** @param array<string,mixed> $row @return array<string,mixed> */
    private function metrics(array $row): array
    {
        $quantity = (float) ($row['quantity'] ?? 0); $net = (float) ($row['net_total'] ?? 0); $cost = (float) ($row['cost_total'] ?? 0);
        $row['quantity'] = (string) ($row['quantity'] ?? '0'); $row['net_total'] = (string) ($row['net_total'] ?? '0'); $row['cost_total'] = (string) ($row['cost_total'] ?? '0');
        $row['gross_profit'] = (string) ($net - $cost); $row['gross_margin_percent'] = $net == 0.0 ? null : (string) (($net - $cost) * 100 / $net);
        $row['average_net_unit_price'] = $quantity == 0.0 ? null : (string) ($net / $quantity); $row['document_count'] = (int) ($row['document_count'] ?? 0);
        return $row;
    }
}
