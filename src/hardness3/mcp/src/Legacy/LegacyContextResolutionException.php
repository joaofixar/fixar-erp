<?php

declare(strict_types=1);

namespace HardnessMcp\Legacy;

use RuntimeException;

final class LegacyContextResolutionException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unable to prepare legacy execution context.');
    }
}
