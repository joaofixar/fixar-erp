<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\ToolInterface;

/** A domain module supplies its explicitly composed MCP Tools. */
interface ToolModuleInterface
{
    /** @return ToolInterface[] */
    public function tools(): array;
}
