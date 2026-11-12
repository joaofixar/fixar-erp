<?php
declare(strict_types=1); namespace HardnessMcp\Tools\Quote\Item; use HardnessMcp\Context\ExecutionContext;
final class UnavailableQuoteItemGateway implements QuoteItemGatewayInterface { public function add(array $a,ExecutionContext $c):array{throw new \RuntimeException('quote_item_unavailable');} public function update(array $a,ExecutionContext $c):array{throw new \RuntimeException('quote_item_unavailable');} public function delete(int $id,ExecutionContext $c):array{throw new \RuntimeException('quote_item_unavailable');} }
