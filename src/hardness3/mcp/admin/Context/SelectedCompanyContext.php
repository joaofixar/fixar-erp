<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi\Context;

/** Immutable, database-validated company selected only within the MCP administration UI. */
final class SelectedCompanyContext
{
    private int $companyId;
    private string $legalName;
    private string $shortName;
    private string $status;

    public function __construct(int $companyId, string $legalName, string $shortName, string $status)
    {
        $this->companyId = $companyId;
        $this->legalName = $legalName;
        $this->shortName = $shortName;
        $this->status = $status;
    }

    public function getCompanyId(): int { return $this->companyId; }
    public function getLegalName(): string { return $this->legalName; }
    public function getShortName(): string { return $this->shortName; }
    public function getStatus(): string { return $this->status; }
}
