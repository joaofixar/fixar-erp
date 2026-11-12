<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi\Context;

/** Immutable server-derived ERP administrative context. */
final class TenantAdminContext
{
    private string $installationHost;
    private string $databaseName;
    private int $userId;
    private int $accessLevel;
    private string $clientIp;

    public function __construct(string $installationHost, string $databaseName, int $userId, int $accessLevel, string $clientIp)
    {
        $this->installationHost = $installationHost;
        $this->databaseName = $databaseName;
        $this->userId = $userId;
        $this->accessLevel = $accessLevel;
        $this->clientIp = $clientIp;
    }

    public function getInstallationHost(): string { return $this->installationHost; }
    public function getDatabaseName(): string { return $this->databaseName; }
    public function getUserId(): int { return $this->userId; }
    public function getAccessLevel(): int { return $this->accessLevel; }
    public function getClientIp(): string { return $this->clientIp; }
}
