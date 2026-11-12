<?php

declare(strict_types=1);

namespace HardnessMcp\Protocol;

class JsonRpcResult
{
    /** @var array<string, mixed>|null */
    private ?array $payload;
    private int $httpStatus;
    private string $auditEvent;
    private ?string $auditSafeCode;

    /**
     * @param array<string, mixed>|null $payload
     */
    private function __construct(?array $payload, int $httpStatus, string $auditEvent = 'request', ?string $auditSafeCode = null)
    {
        $this->payload = $payload;
        $this->httpStatus = $httpStatus;
        $this->auditEvent = $auditEvent;
        $this->auditSafeCode = $auditSafeCode;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function response(array $payload, int $httpStatus = 200, string $auditEvent = 'request', ?string $auditSafeCode = null): self
    {
        return new self($payload, $httpStatus, $auditEvent, $auditSafeCode);
    }

    public static function acceptedNotification(): self
    {
        return new self(null, 202);
    }

    public static function rejectedNotification(): self
    {
        return new self(null, 400);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getAuditEvent(): string { return $this->auditEvent; }
    public function getAuditSafeCode(): ?string { return $this->auditSafeCode; }
}
