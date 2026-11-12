<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Catalog;

use HardnessMcp\Tools\ToolInterface;
use HardnessMcp\Tools\ToolRegistry;
use RuntimeException;

/** Composes the explicit domain modules into the runtime ToolRegistry. */
final class ToolCatalogFactory
{
    /** @var ToolModuleInterface[] */
    private array $modules;

    /** @param ToolModuleInterface[] $modules */
    public function __construct(array $modules)
    {
        $this->modules = array_values($modules);
    }

    public function createRegistry(): ToolRegistry
    {
        /** @var ToolInterface[] $tools */
        $tools = [];
        /** @var array<string, true> $names */
        $names = [];

        foreach ($this->modules as $module) {
            foreach ($module->tools() as $tool) {
                if (!$tool instanceof ToolInterface) {
                    throw new RuntimeException('Invalid MCP tool module registration.');
                }
                $name = $tool->getDefinition()->getName();
                if (isset($names[$name])) {
                    throw new RuntimeException('Duplicate MCP tool registration.');
                }
                $names[$name] = true;
                $tools[] = $tool;
            }
        }

        return new ToolRegistry($tools);
    }
}
