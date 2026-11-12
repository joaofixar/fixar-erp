<?php

declare(strict_types=1);

namespace HardnessMcp\Core;

use RuntimeException;

class McpException extends RuntimeException
{
    private int $jsonRpcCode;
    private int $httpStatus;

    /** @var array<string, mixed>|null */
    private ?array $errorData;

    /**
     * @param array<string, mixed>|null $errorData
     */
    public function __construct(string $message, int $jsonRpcCode, ?array $errorData = null, int $httpStatus = 400)
    {
        parent::__construct($message);
        $this->jsonRpcCode = $jsonRpcCode;
        $this->errorData = $errorData;
        $this->httpStatus = $httpStatus;
    }

    public function getJsonRpcCode(): int
    {
        return $this->jsonRpcCode;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getErrorData(): ?array
    {
        return $this->errorData;
    }
}
