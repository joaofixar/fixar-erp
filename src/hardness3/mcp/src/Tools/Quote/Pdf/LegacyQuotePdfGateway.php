<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Pdf;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Legacy\LegacyExecutionContextAdapter;
use HardnessMcp\Legacy\McpFrameworkBridge;
use HardnessMcp\Tenant\ResolvedTenantContext;

final class LegacyQuotePdfGateway implements QuotePdfGatewayInterface
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

    public function generate(int $quoteId, ExecutionContext $context): array
    {
        $legacyContext = $this->contextAdapter->adapt($context, $this->tenant, ['remote_ip' => $context->getTrustedRemoteIp()]);
        $webPath = $this->bridge->executeVen001GenerateQuotePdf($legacyContext, $quoteId);
        return ['pdf_url' => rtrim($this->tenant->getRootUrl(), '/') . $webPath];
    }
}
