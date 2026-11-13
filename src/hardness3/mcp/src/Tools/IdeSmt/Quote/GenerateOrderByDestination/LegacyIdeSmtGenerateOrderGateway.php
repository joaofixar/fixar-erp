<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Legacy\LegacyBridgeException;
use HardnessMcp\Legacy\LegacyExecutionContextAdapter;
use HardnessMcp\Legacy\McpFrameworkBridge;
use HardnessMcp\Tenant\ResolvedTenantContext;
use InvalidArgumentException;

final class LegacyIdeSmtGenerateOrderGateway implements IdeSmtGenerateOrderGatewayInterface
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

    public function generate(int $quoteId, string $destination, ExecutionContext $context): array
    {
        if ($quoteId <= 0) {
            throw new LegacyBridgeException('ide_smt_order_invalid');
        }
        try {
            $type = IdeSmtOrderDestination::typeFor($destination);
        } catch (InvalidArgumentException $exception) {
            throw new LegacyBridgeException('ide_smt_order_invalid');
        }
        $legacyContext = $this->contextAdapter->adapt($context, $this->tenant, [
            'remote_ip' => $context->getTrustedRemoteIp(),
        ]);
        $result = $this->bridge->executeVen001IdeSmtGenerateOrder($legacyContext, $quoteId, $type);
        $result['destination'] = $destination;
        return $result;
    }
}
