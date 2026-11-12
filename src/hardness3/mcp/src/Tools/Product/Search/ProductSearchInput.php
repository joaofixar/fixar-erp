<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\Search;

final class ProductSearchInput
{
    private string $query;
    private int $limit;
    /** @param array<string,mixed> $arguments */
    public static function fromArguments(array $arguments): self
    {
        $input = new self();
        $input->query = (string) $arguments['query'];
        $input->limit = isset($arguments['limit']) ? (int) $arguments['limit'] : 25;
        return $input;
    }
    public function getQuery(): string { return $this->query; }
    public function getLimit(): int { return $this->limit; }
}
