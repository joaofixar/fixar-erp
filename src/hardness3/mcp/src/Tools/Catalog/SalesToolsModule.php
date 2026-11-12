<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\Sales\Domain\SalesSummaryRepositoryInterface;
use HardnessMcp\Tools\Sales\Infrastructure\PdoSalesSummaryRepository;
use HardnessMcp\Tools\Sales\SalesSummaryHandler;
use HardnessMcp\Tools\Sales\SalesSummaryTool;
use HardnessMcp\Tools\ToolInterface;
use PDO;

final class SalesToolsModule implements ToolModuleInterface
{
    private SalesSummaryRepositoryInterface $repository;
    public function __construct(SalesSummaryRepositoryInterface $repository) { $this->repository = $repository; }
    public static function fromPdo(PDO $pdo, bool $proposalCustomerAvailable = false): self { return new self(new PdoSalesSummaryRepository($pdo, $proposalCustomerAvailable)); }
    /** @return ToolInterface[] */
    public function tools(): array { return [new SalesSummaryTool(new SalesSummaryHandler($this->repository))]; }
}
