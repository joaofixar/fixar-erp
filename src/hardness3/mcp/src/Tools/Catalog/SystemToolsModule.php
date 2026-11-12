<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Legacy\LegacyExecutionContextAdapter;
use HardnessMcp\Legacy\McpFrameworkBridge;
use HardnessMcp\Legacy\PdoLegacyActorResolver;
use HardnessMcp\Tenant\ResolvedTenantContext;
use HardnessMcp\Tools\System\ContextPilot\ContextPilotGatewayInterface;
use HardnessMcp\Tools\System\ContextPilot\ContextPilotTool;
use HardnessMcp\Tools\System\ContextPilot\LegacyContextPilotGateway;
use HardnessMcp\Tools\System\ContextPilot\UnavailableContextPilotGateway;
use HardnessMcp\Tools\ToolInterface;
use PDO;

final class SystemToolsModule implements ToolModuleInterface
{
    private ContextPilotGatewayInterface $contextPilotGateway;

    public function __construct(ContextPilotGatewayInterface $contextPilotGateway)
    {
        $this->contextPilotGateway = $contextPilotGateway;
    }

    public static function fromRuntime(PDO $pdo, ResolvedTenantContext $tenant, string $erpRootPath): self
    {
        $adapter = new LegacyExecutionContextAdapter(new PdoLegacyActorResolver($pdo), $erpRootPath);
        return new self(new LegacyContextPilotGateway($adapter, new McpFrameworkBridge($erpRootPath), $tenant));
    }

    public static function unavailable(): self
    {
        return new self(new UnavailableContextPilotGateway());
    }

    /** @return ToolInterface[] */
    public function tools(): array
    {
        return [new ContextPilotTool($this->contextPilotGateway)];
    }
}
