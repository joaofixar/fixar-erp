<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Create;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Legacy\LegacyExecutionContextAdapter;
use HardnessMcp\Legacy\McpFrameworkBridge;
use HardnessMcp\Tenant\ResolvedTenantContext;

final class LegacyQuoteCreateGateway implements QuoteCreateGatewayInterface
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

    public function create(array $arguments, ExecutionContext $context): array
    {
        $legacyContext = $this->contextAdapter->adapt($context, $this->tenant, ['remote_ip' => $context->getTrustedRemoteIp()]);
        return $this->bridge->executeMcp001CreateQuote($legacyContext, $arguments);
    }
}
