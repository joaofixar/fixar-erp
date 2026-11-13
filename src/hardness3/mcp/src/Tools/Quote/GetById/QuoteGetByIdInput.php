<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\GetById;

final class QuoteGetByIdInput
{
    private int $id;
    /** @param array<string,mixed> $arguments */ public static function fromArguments(array $arguments): self { $input = new self(); $input->id = (int) $arguments['id']; return $input; }
    public function getId(): int { return $this->id; }
}
