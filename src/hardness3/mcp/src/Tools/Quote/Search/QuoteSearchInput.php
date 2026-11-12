<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Search;

final class QuoteSearchInput
{
    private string $issuedFrom; private string $issuedTo; private ?int $customerId; private ?string $origin; private int $limit;
    /** @param array<string,mixed> $arguments */
    public static function fromArguments(array $arguments): self { $input = new self(); $input->issuedFrom = (string) $arguments['issued_from']; $input->issuedTo = (string) $arguments['issued_to']; $input->customerId = isset($arguments['customer_id']) ? (int) $arguments['customer_id'] : null; $input->origin = isset($arguments['origin']) ? (string) $arguments['origin'] : null; $input->limit = isset($arguments['limit']) ? (int) $arguments['limit'] : 25; return $input; }
    public function getIssuedFrom(): string { return $this->issuedFrom; } public function getIssuedTo(): string { return $this->issuedTo; } public function getCustomerId(): ?int { return $this->customerId; } public function getOrigin(): ?string { return $this->origin; } public function getLimit(): int { return $this->limit; }
}
