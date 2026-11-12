<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination\IdeSmtGenerateOrderGatewayInterface;
use HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination\IdeSmtGenerateOrderTool;
use HardnessMcp\Tools\ToolInterface;

final class IdeSmtToolsModule implements ToolModuleInterface
{
    private IdeSmtGenerateOrderGatewayInterface $gateway;
    private ?int $enabledCompanyId;

    public function __construct(IdeSmtGenerateOrderGatewayInterface $gateway, ?int $enabledCompanyId)
    {
        $this->gateway = $gateway;
        $this->enabledCompanyId = $enabledCompanyId;
    }

    /** @return ToolInterface[] */
    public function tools(): array
    {
        return [new IdeSmtGenerateOrderTool($this->gateway, $this->enabledCompanyId)];
    }
}
