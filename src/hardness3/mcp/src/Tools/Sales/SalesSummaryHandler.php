<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Sales;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Sales\Domain\SalesSummaryRepositoryInterface;

final class SalesSummaryHandler
{
    private SalesSummaryRepositoryInterface $repository;
    public function __construct(SalesSummaryRepositoryInterface $repository) { $this->repository = $repository; }

    /** @return array<string,mixed> */
    public function handle(SalesSummaryInput $input, ExecutionContext $context): array
    {
        $companyId = $context->getCompanyId();
        $summary = $this->repository->summary($companyId, $input->getDateFrom(), $input->getDateTo(), $input->getCustomerId(), $input->getSellerInternalId());
        $rows = $this->repository->grouped($companyId, $input->getDateFrom(), $input->getDateTo(), $input->getGroupBy(), $input->getCustomerId(), $input->getSellerInternalId(), $input->getLimit() + 1);
        $hasMore = in_array($input->getGroupBy(), ['customer', 'seller_internal', 'document'], true) && count($rows) > $input->getLimit();
        if ($hasMore) { $rows = array_slice($rows, 0, $input->getLimit()); }

        return [
            'report' => 'sales_summary',
            'definition' => 'Notas fiscais de saída não canceladas e com protocolo de NF-e, agrupadas pela data de emissão.',
            'period' => ['from' => $input->getDateFrom(), 'to' => $input->getDateTo()],
            'filters_applied' => array_filter(['customer_id' => $input->getCustomerId(), 'seller_internal_id' => $input->getSellerInternalId()], static function ($value): bool { return $value !== null; }),
            'group_by' => $input->getGroupBy(),
            'summary' => SalesSummaryOutput::summary($summary),
            'rows' => SalesSummaryOutput::rows($rows, $input->getGroupBy()),
            'has_more' => $hasMore,
        ];
    }
}
