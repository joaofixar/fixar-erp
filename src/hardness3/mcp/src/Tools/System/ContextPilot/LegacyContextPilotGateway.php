<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\System\ContextPilot;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Legacy\LegacyExecutionContextAdapter;
use HardnessMcp\Legacy\McpFrameworkBridge;
use HardnessMcp\Tenant\ResolvedTenantContext;

final class LegacyContextPilotGateway implements ContextPilotGatewayInterface
{
    private LegacyExecutionContextAdapter $contextAdapter;
    private McpFrameworkBridge $bridge;
    private ResolvedTenantContext $tenant;

    public function __construct(LegacyExecutionContextAdapter $contextAdapter, McpFrameworkBridge $bridge, ResolvedTenantContext $tenant)
    {
        $this->contextAdapter = $contextAdapter;
        $this->bridge = $bridge;
        $this->tenant = $tenant;
    }

    /** @return array<string,int|string> */
    public function execute(ExecutionContext $executionContext): array
    {
        $legacyContext = $this->contextAdapter->adapt($executionContext, $this->tenant, ['remote_ip' => $executionContext->getTrustedRemoteIp()]);
        return $this->bridge->executeMcp001ContextPilot($legacyContext);
    }
}
