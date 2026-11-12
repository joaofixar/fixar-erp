<?php

declare(strict_types=1);

namespace HardnessMcp\Tools;

final class ToolRegistry
{
    /** @var array<string, ToolInterface> */
    private array $tools = [];

    /** @param ToolInterface[] $tools */
    public function __construct(array $tools)
    {
        foreach ($tools as $tool) {
            $name = $tool->getDefinition()->getName();
            if (isset($this->tools[$name])) {
                throw new \RuntimeException('Duplicate MCP tool registration.');
            }
            $this->tools[$name] = $tool;
        }
    }

    public function find(string $name): ?ToolInterface { return $this->tools[$name] ?? null; }

    /** @return ToolInterface[] */
    public function all(): array { return array_values($this->tools); }
}
