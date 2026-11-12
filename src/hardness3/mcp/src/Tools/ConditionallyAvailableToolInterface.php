<?php

declare(strict_types=1);

namespace HardnessMcp\Tools;

use HardnessMcp\Context\ExecutionContext;

/** Allows a registered Tool to remain undiscoverable outside its tenant capability. */
interface ConditionallyAvailableToolInterface
{
    public function isAvailable(ExecutionContext $executionContext): bool;
}
