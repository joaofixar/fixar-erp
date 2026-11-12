<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\Search;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Product\Domain\ProductRepositoryInterface;

final class ProductSearchHandler
{
    private ProductRepositoryInterface $repository;
    public function __construct(ProductRepositoryInterface $repository) { $this->repository = $repository; }
    /** @return array<string,mixed> */
    public function handle(ProductSearchInput $input, ExecutionContext $context): array
    {
        $products = $this->repository->search($context->getCompanyId(), $input->getQuery(), $input->getLimit() + 1);
        $hasMore = count($products) > $input->getLimit();
        return ProductSearchOutput::fromProducts(array_slice($products, 0, $input->getLimit()), $hasMore);
    }
}
