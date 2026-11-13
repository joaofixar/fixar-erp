<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Commercial;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Commercial\Domain\OpportunityRepositoryInterface;

final class OpportunityHandler
{
    private OpportunityRepositoryInterface $repository;
    public function __construct(OpportunityRepositoryInterface $repository) { $this->repository = $repository; }
    /** @return array<string,mixed> */
    public function handle(OpportunityInput $input, ExecutionContext $context): array
    {
        $rows = $this->repository->find($context->getCompanyId(), $input->from(), $input->to(), $input->type(), $input->minimumAge(), $input->customer(), $input->seller(), $input->productCode(), $input->minimumPending(), $input->cursor(), $input->limit() + 1);
        $hasMore = count($rows) > $input->limit(); if ($hasMore) $rows = array_slice($rows, 0, $input->limit());
        $pending = 0.0; foreach ($rows as &$row) { $row['original_total'] = (string) ($row['original_total'] ?? '0'); $row['progressed_total'] = (string) ($row['progressed_total'] ?? '0'); $row['pending_total'] = (string) ($row['pending_total'] ?? '0'); $row['age_days'] = (int) ($row['age_days'] ?? 0); $row['document_id'] = (int) ($row['document_id'] ?? 0); $pending += (float) $row['pending_total']; } unset($row);
        $nextCursor = $hasMore && $rows !== [] ? OpportunityCursor::encode($rows[count($rows) - 1]) : null;
        return ['report' => 'commercial_opportunities', 'definition' => 'Oportunidades baseadas em valor pendente de conversão de orçamento em pedido ou de pedido em NF-e autorizada.', 'period' => ['from' => $input->from(), 'to' => $input->to()], 'filters_applied' => array_filter(['opportunity_type' => $input->type() === 'all' ? null : $input->type(), 'minimum_age_days' => $input->minimumAge(), 'minimum_pending_value' => $input->minimumPending() > 0 ? $input->minimumPending() : null, 'customer_id' => $input->customer(), 'seller_internal_id' => $input->seller(), 'product_code' => $input->productCode()], static fn($value) => $value !== null), 'summary' => ['opportunity_count' => count($rows), 'pending_total' => (string) $pending], 'rows' => $rows, 'has_more' => $hasMore, 'next_cursor' => $nextCursor];
    }
}
