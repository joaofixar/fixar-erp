<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\ItemReport\Infrastructure\PdoItemReportRepository;
use HardnessMcp\Tools\ItemReport\ItemReportHandler;
use HardnessMcp\Tools\ItemReport\ItemReportTool;
use HardnessMcp\Tools\Conversion\Infrastructure\PdoProductConversionRepository;
use HardnessMcp\Tools\Conversion\ProductConversionHandler;
use HardnessMcp\Tools\Conversion\ProductConversionTool;
use HardnessMcp\Tools\ToolInterface;
use PDO;

final class ItemReportToolsModule implements ToolModuleInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    public static function fromPdo(PDO $pdo): self { return new self($pdo); }
    /** @return ToolInterface[] */
    public function tools(): array
    {
        $tools = [];
        foreach (['quote' => 'quote:item-report:read', 'order' => 'order:item-report:read', 'sales' => 'sales:item-report:read'] as $source => $scope) $tools[] = new ItemReportTool(new ItemReportHandler(new PdoItemReportRepository($this->pdo, $source), $source), $source, $scope);
        $tools[] = new ProductConversionTool(new ProductConversionHandler(new PdoProductConversionRepository($this->pdo)));
        return $tools;
    }
}
