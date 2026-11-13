<?php

declare(strict_types=1);

namespace RestApi\Core;

use RuntimeException;

class ApiException extends RuntimeException
{
    private string $errorCode;

    /** @var array<string, mixed> */
    private array $details;

    /**
     * @param array<string, mixed> $details
     */
    public function __construct(string $errorCode, string $message, int $statusCode, array $details = [])
    {
        parent::__construct($message, $statusCode);
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
