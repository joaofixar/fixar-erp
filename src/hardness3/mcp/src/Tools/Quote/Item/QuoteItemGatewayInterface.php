<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Quote\Item;
use HardnessMcp\Context\ExecutionContext;
interface QuoteItemGatewayInterface { /** @return array<string,mixed> */ public function add(array $arguments, ExecutionContext $context): array; /** @return array<string,mixed> */ public function update(array $arguments, ExecutionContext $context): array; /** @return array<string,mixed> */ public function delete(int $t004Id, ExecutionContext $context): array; }
