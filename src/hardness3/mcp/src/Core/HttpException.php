<?php

declare(strict_types=1);

namespace HardnessMcp\Core;

use RuntimeException;

final class HttpException extends RuntimeException
{
    private int $httpStatus;
    /** @var array<string, string> */ private array $headers;
    /** @param array<string, string> $headers */
    public function __construct(int $httpStatus, array $headers = []) { parent::__construct('HTTP request failed.'); $this->httpStatus = $httpStatus; $this->headers = $headers; }
    public function getHttpStatus(): int { return $this->httpStatus; }
    /** @return array<string, string> */ public function getHeaders(): array { return $this->headers; }
}
