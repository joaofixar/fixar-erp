<?php

declare(strict_types=1);

namespace HardnessMcp\Tools;

use HardnessMcp\Context\ExecutionContext;

interface ToolInterface
{
    public function getDefinition(): ToolDefinition;

    /** @param array<string, mixed> $arguments */
    public function execute(array $arguments, ExecutionContext $executionContext): ToolExecutionResult;
}
