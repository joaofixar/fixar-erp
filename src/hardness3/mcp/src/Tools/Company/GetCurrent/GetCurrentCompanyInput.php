<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Company\GetCurrent;

use InvalidArgumentException;

/** The public contract intentionally accepts only an empty object. */
final class GetCurrentCompanyInput
{
    /** @param array<string, mixed> $arguments */
    public static function fromArguments(array $arguments): self
    {
        if ($arguments !== []) {
            throw new InvalidArgumentException('Get current company does not accept arguments.');
        }
        return new self();
    }
}
