<?php

declare(strict_types=1);

namespace HardnessMcp\Tools;

final class ToolExecutionResult
{
    /** @var array<string, mixed> */
    private array $result;
    private string $auditEvent;
    private ?string $auditSafeCode;

    /** @param array<string, mixed> $result */
    public function __construct(array $result, string $auditEvent, ?string $auditSafeCode = null)
    {
        $this->result = $result;
        $this->auditEvent = $auditEvent;
        $this->auditSafeCode = $auditSafeCode;
    }

    /** @return array<string, mixed> */
    public function getResult(): array { return $this->result; }
    public function getAuditEvent(): string { return $this->auditEvent; }
    public function getAuditSafeCode(): ?string { return $this->auditSafeCode; }
}
