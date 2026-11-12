<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\Product\Domain\ProductRepositoryInterface;
use HardnessMcp\Tools\Product\Infrastructure\PdoProductRepository;
use HardnessMcp\Tools\Product\PartnerReference\Domain\PartnerReferenceProductRepositoryInterface;
use HardnessMcp\Tools\Product\PartnerReference\PartnerReferenceProductHandler;
use HardnessMcp\Tools\Product\PartnerReference\PartnerReferenceProductTool;
use HardnessMcp\Tools\Product\Search\ProductSearchHandler;
use HardnessMcp\Tools\Product\Search\ProductSearchTool;
use HardnessMcp\Tools\ToolInterface;
use PDO;
use HardnessMcp\Tools\Product\Prices\ApplyPricesTool;
use HardnessMcp\Tools\Product\Prices\ApplyPricesRepository;
use HardnessMcp\Tools\Product\Prices\ApplyReversalTool;
use HardnessMcp\Tools\Product\Prices\PrepareReversalTool;
use HardnessMcp\Tools\Product\Prices\PriceHistoryRepository;
use HardnessMcp\Tools\Product\Prices\PriceHistoryTool;
use HardnessMcp\Tools\Product\Prices\PreparePricesTool;
use HardnessMcp\Tools\Product\Prices\PreparePricesRepository;
use HardnessMcp\Tools\Product\Prices\PriceReportExportService;
use HardnessMcp\Tools\Product\Prices\PriceReportExportTool;
use HardnessMcp\Tenant\ResolvedTenantContext;

final class ProductToolsModule implements ToolModuleInterface
{
    private ProductRepositoryInterface $repository;
    private ?PreparePricesRepository $prices;
    private ?ApplyPricesRepository $applyPrices;
    private ?PriceHistoryRepository $priceHistory;
    private ?PriceReportExportService $priceExport;
    private PartnerReferenceProductRepositoryInterface $partnerReferenceRepository;
    public function __construct(ProductRepositoryInterface $repository, PartnerReferenceProductRepositoryInterface $partnerReferenceRepository, ?PreparePricesRepository $prices = null, ?ApplyPricesRepository $applyPrices = null, ?PriceHistoryRepository $priceHistory = null, ?PriceReportExportService $priceExport=null) { $this->applyPrices=$applyPrices; $this->priceHistory=$priceHistory; $this->priceExport=$priceExport; $this->prices = $prices; $this->repository = $repository; $this->partnerReferenceRepository = $partnerReferenceRepository; }
    public static function fromPdo(PDO $pdo, ?PDO $writePdo = null, ?ResolvedTenantContext $tenant=null, ?string $erpRootPath=null): self { $repository = new PdoProductRepository($pdo); $write=$writePdo ?? $pdo; return new self($repository, $repository, new PreparePricesRepository($write), new ApplyPricesRepository($write), new PriceHistoryRepository($write), $tenant!==null&&$erpRootPath!==null?new PriceReportExportService($write,$tenant,$erpRootPath):null); }
    /** @return ToolInterface[] */
    public function tools(): array { return [new ProductSearchTool(new ProductSearchHandler($this->repository)), new PartnerReferenceProductTool(new PartnerReferenceProductHandler($this->partnerReferenceRepository)), new PreparePricesTool($this->prices), new ApplyPricesTool($this->applyPrices,$this->priceExport), new PriceHistoryTool($this->priceHistory), new PriceReportExportTool($this->priceExport), new PrepareReversalTool($this->priceHistory), new ApplyReversalTool($this->applyPrices)]; }
}
