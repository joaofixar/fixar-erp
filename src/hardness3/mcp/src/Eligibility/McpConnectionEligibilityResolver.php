<?php
declare(strict_types=1);
namespace HardnessMcp\Eligibility;
use PDO;

/** Answers only whether a C007 may authorize a MCP001; it never calculates scopes. */
final class McpConnectionEligibilityResolver
{
    private McpConnectionEligibilityRepository $repository;
    public function __construct(PDO $pdo) { $this->repository = new McpConnectionEligibilityRepository($pdo); }
    public function canAuthorizeConnection(int $userId, int $connectionId): bool
    { return $userId > 0 && $connectionId > 0 && $this->repository->canAuthorize($userId, $connectionId); }
    /** @return array<int,array<string,mixed>> */
    public function listEligibleConnections(int $userId, int $companyId = 0): array
    { return $userId > 0 ? $this->repository->listEligibleConnections($userId, $companyId) : []; }
}
