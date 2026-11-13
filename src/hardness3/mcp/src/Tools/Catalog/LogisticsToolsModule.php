<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\Logistics\LoadReportTool;
use HardnessMcp\Tools\Logistics\PdoLoadReportRepository;
use HardnessMcp\Tools\ToolInterface;
use PDO;

final class LogisticsToolsModule implements ToolModuleInterface
{
    private PdoLoadReportRepository $repository;
    public function __construct(PdoLoadReportRepository $repository) { $this->repository = $repository; }
    public static function fromPdo(PDO $pdo, bool $orderContextAvailable, bool $resourcesAvailable): self { return new self(new PdoLoadReportRepository($pdo, $orderContextAvailable, $resourcesAvailable)); }
    /** @return ToolInterface[] */
    public function tools(): array { return [new LoadReportTool('summary', $this->repository), new LoadReportTool('list', $this->repository), new LoadReportTool('detail', $this->repository)]; }
}
