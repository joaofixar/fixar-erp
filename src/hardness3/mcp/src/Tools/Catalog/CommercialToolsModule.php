<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\Commercial\Infrastructure\PdoOpportunityRepository;
use HardnessMcp\Tools\Commercial\OpportunityHandler;
use HardnessMcp\Tools\Commercial\OpportunityTool;
use HardnessMcp\Tools\ToolInterface;
use PDO;

final class CommercialToolsModule implements ToolModuleInterface
{
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    public static function fromPdo(PDO $pdo): self { return new self($pdo); }
    /** @return ToolInterface[] */
    public function tools(): array { return [new OpportunityTool(new OpportunityHandler(new PdoOpportunityRepository($this->pdo)))]; }
}
