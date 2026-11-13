<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

final class DeliveryRepository
{
    /** @var int */
    private $leaseSeconds;

    public function __construct(int $leaseSeconds = 90)
    {
        if ($leaseSeconds < 1) {
            throw new \InvalidArgumentException('Webhook lease duration must be positive.');
        }
        $this->leaseSeconds = $leaseSeconds;
    }

    /** @return array<int,int> */
    public function findCandidateIds(\mysqli $connection, int $limit): array
    {
        if ($limit < 1) { return array(); }

        // Separate bounded queries let MySQL use each Task 04 worker index.
        $ids = $this->fetchCandidateIds($connection,
            "SELECT WHK004_Id FROM WHK004 WHERE WHK004_Status IN ('P', 'R') AND WHK004_Next_Attempt_At <= UTC_TIMESTAMP() ORDER BY WHK004_Next_Attempt_At ASC, WHK004_Id ASC LIMIT ?",
            $limit
        );
        if (count($ids) < $limit) {
            $leases = $this->fetchCandidateIds($connection,
                "SELECT WHK004_Id FROM WHK004 WHERE WHK004_Status = 'L' AND WHK004_Lease_Until < UTC_TIMESTAMP() ORDER BY WHK004_Lease_Until ASC, WHK004_Id ASC LIMIT ?",
                $limit - count($ids)
            );
            foreach ($leases as $id) { if (!in_array($id, $ids, true)) { $ids[] = $id; } }
        }
        return $ids;
    }

    public function tryClaim(\mysqli $connection, int $deliveryId, string $workerId): bool
    {
        $sql = "UPDATE WHK004 SET WHK004_Status = 'L', WHK004_Worker_Id = ?, WHK004_Leased_At = UTC_TIMESTAMP(), WHK004_Lease_Until = DATE_ADD(UTC_TIMESTAMP(), INTERVAL ? SECOND), WHK004_Attempt_Count = WHK004_Attempt_Count + 1, WHK004_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK004_Id = ? AND ((WHK004_Status IN ('P', 'R') AND WHK004_Next_Attempt_At <= UTC_TIMESTAMP()) OR (WHK004_Status = 'L' AND WHK004_Lease_Until < UTC_TIMESTAMP()))";
        $statement = $connection->prepare($sql);
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook delivery claim.'); }
        try {
            $statement->bind_param('sii', $workerId, $this->leaseSeconds, $deliveryId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook delivery claim.'); }
            return $statement->affected_rows === 1;
        } finally { $statement->close(); }
    }

    /**
     * Snapshot used only for the cycle summary.  The subsequent conditional
     * claim remains the sole authority, so a concurrent change can only make
     * this metric conservative.
     */
    public function isExpiredLeaseCandidate(\mysqli $connection, int $deliveryId): bool
    {
        $statement = $connection->prepare("SELECT 1 FROM WHK004 WHERE WHK004_Id = ? AND WHK004_Status = 'L' AND WHK004_Lease_Until < UTC_TIMESTAMP()");
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook expired lease lookup.'); }
        try {
            $statement->bind_param('i', $deliveryId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook expired lease lookup.'); }
            return $statement->get_result()->num_rows === 1;
        } finally { $statement->close(); }
    }

    /** @return array<string,mixed>|null */
    public function loadClaimed(\mysqli $connection, int $deliveryId, string $workerId): ?array
    {
        $sql = "SELECT d.WHK004_Id, d.WHK004_Delivery_Id, d.WHK004_C004_Id, d.WHK004_WHK003_Id, d.WHK004_WHK001_Id, d.WHK004_WHK002_Id, d.WHK004_WHK007_Id, d.WHK004_Destination_URL, d.WHK004_Attempt_Count, d.WHK004_Lease_Until, e.WHK003_Event_Id, e.WHK003_Type, e.WHK003_Schema_Version, e.WHK003_Payload FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id = d.WHK004_WHK003_Id WHERE d.WHK004_Id = ? AND d.WHK004_Status = 'L' AND d.WHK004_Worker_Id = ?";
        $statement = $connection->prepare($sql);
        if ($statement === false) { throw new \RuntimeException('Could not prepare claimed delivery lookup.'); }
        try {
            $statement->bind_param('is', $deliveryId, $workerId);
            if (!$statement->execute()) { throw new \RuntimeException('Could not execute claimed delivery lookup.'); }
            $result = $statement->get_result(); $row = $result->fetch_assoc();
            return is_array($row) ? $row : null;
        } finally { $statement->close(); }
    }

    public function ownsLease(\mysqli $connection, int $deliveryId, string $workerId): bool
    {
        $statement = $connection->prepare("SELECT 1 FROM WHK004 WHERE WHK004_Id = ? AND WHK004_Status = 'L' AND WHK004_Worker_Id = ?");
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook lease ownership lookup.'); }
        try { $statement->bind_param('is', $deliveryId, $workerId); if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook lease ownership lookup.'); } return $statement->get_result()->num_rows === 1; } finally { $statement->close(); }
    }

    /**
     * Revalidates the mutable administrative state immediately before HTTP.
     * A delivery is an immutable outbox record, but disabled endpoints or
     * subscriptions must fail closed even when it was claimed moments earlier.
     */
    public function isDispatchEligible(\mysqli $connection, int $deliveryId, string $workerId): bool
    {
        $sql = "SELECT 1 FROM WHK004 d INNER JOIN WHK001 e ON e.WHK001_Id=d.WHK004_WHK001_Id AND e.WHK001_C004_Id=d.WHK004_C004_Id AND e.WHK001_Status='A' INNER JOIN WHK002 s ON s.WHK002_Id=d.WHK004_WHK002_Id AND s.WHK002_WHK001_Id=e.WHK001_Id AND s.WHK002_C004_Id=d.WHK004_C004_Id AND s.WHK002_Status='A' WHERE d.WHK004_Id=? AND d.WHK004_Status='L' AND d.WHK004_Worker_Id=? LIMIT 1";
        $statement = $connection->prepare($sql);
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook dispatch eligibility lookup.'); }
        try { $statement->bind_param('is', $deliveryId, $workerId); if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook dispatch eligibility lookup.'); } return $statement->get_result()->num_rows === 1; } finally { $statement->close(); }
    }

    /** Controlled test-only completion. HTTP/retry/dead policy is intentionally absent. */
    public function markDeliveredForTesting(\mysqli $connection, int $deliveryId, string $workerId): bool
    {
        $statement = $connection->prepare("UPDATE WHK004 SET WHK004_Status = 'D', WHK004_Delivered_At = UTC_TIMESTAMP(), WHK004_Lease_Until = NULL, WHK004_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK004_Id = ? AND WHK004_Status = 'L' AND WHK004_Worker_Id = ?");
        if ($statement === false) { throw new \RuntimeException('Could not prepare controlled delivery completion.'); }
        try { $statement->bind_param('is', $deliveryId, $workerId); if (!$statement->execute()) { throw new \RuntimeException('Could not execute controlled delivery completion.'); } return $statement->affected_rows === 1; } finally { $statement->close(); }
    }

    /** Cancellation is idempotent and only the current lease owner may cancel L. */
    public function cancelClaimed(\mysqli $connection, int $deliveryId, string $workerId, string $reason): bool
    {
        if (!preg_match('/^[a-z_]{1,64}$/', $reason)) { throw new \InvalidArgumentException('Webhook cancellation reason is invalid.'); }
        $statement = $connection->prepare("UPDATE WHK004 SET WHK004_Status = 'C', WHK004_Lease_Until = NULL, WHK004_Last_Error = ?, WHK004_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK004_Id = ? AND WHK004_Status = 'L' AND WHK004_Worker_Id = ?");
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook cancellation.'); }
        try { $statement->bind_param('sis', $reason, $deliveryId, $workerId); if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook cancellation.'); } return $statement->affected_rows === 1; } finally { $statement->close(); }
    }

    /**
     * Persists one completed HTTP attempt after network I/O.  A lost lease is
     * deliberately a no-op: the current owner alone may change the delivery.
     */
    public function recordHttpResult(\mysqli $connection, int $deliveryId, string $workerId, int $attemptNumber, string $result, ?int $httpStatus, ?string $transportError, int $durationMs, int $responseBytes, string $nextStatus, ?int $deferSeconds): bool
    {
        if ($attemptNumber < 1 || $durationMs < 0 || $responseBytes < 0 || !in_array($nextStatus, array('D', 'R', 'X'), true) || ($nextStatus === 'R' && ($deferSeconds === null || $deferSeconds < 1))) {
            throw new \InvalidArgumentException('Webhook HTTP result values are invalid.');
        }
        if (!preg_match('/^[a-z_]{1,32}$/', $result)) {
            throw new \InvalidArgumentException('Webhook HTTP result classification is invalid.');
        }
        $started = false;
        try {
            if (!$connection->begin_transaction()) { throw new \RuntimeException('Could not start webhook HTTP result transaction.'); }
            $started = true;
            if ($nextStatus === 'D') {
                $sql = "UPDATE WHK004 SET WHK004_Status = 'D', WHK004_Delivered_At = UTC_TIMESTAMP(), WHK004_Lease_Until = NULL, WHK004_Last_Http_Status = ?, WHK004_Last_Error = NULL, WHK004_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK004_Id = ? AND WHK004_Status = 'L' AND WHK004_Worker_Id = ?";
                $statement = $connection->prepare($sql);
                if ($statement === false) { throw new \RuntimeException('Could not prepare webhook HTTP completion.'); }
                $statement->bind_param('iis', $httpStatus, $deliveryId, $workerId);
            } elseif ($nextStatus === 'R') {
                $error = $transportError === null ? null : substr($transportError, 0, 1000);
                $sql = "UPDATE WHK004 SET WHK004_Status = 'R', WHK004_Next_Attempt_At = DATE_ADD(UTC_TIMESTAMP(), INTERVAL ? SECOND), WHK004_Lease_Until = NULL, WHK004_Last_Http_Status = ?, WHK004_Last_Error = ?, WHK004_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK004_Id = ? AND WHK004_Status = 'L' AND WHK004_Worker_Id = ?";
                $statement = $connection->prepare($sql);
                if ($statement === false) { throw new \RuntimeException('Could not prepare webhook HTTP deferral.'); }
                $statement->bind_param('iisis', $deferSeconds, $httpStatus, $error, $deliveryId, $workerId);
            } else {
                $error = $transportError === null ? null : substr($transportError, 0, 1000);
                $sql = "UPDATE WHK004 SET WHK004_Status = 'X', WHK004_Lease_Until = NULL, WHK004_Last_Http_Status = ?, WHK004_Last_Error = ?, WHK004_Data_Alteracao = UTC_TIMESTAMP() WHERE WHK004_Id = ? AND WHK004_Status = 'L' AND WHK004_Worker_Id = ?";
                $statement = $connection->prepare($sql);
                if ($statement === false) { throw new \RuntimeException('Could not prepare webhook dead-letter transition.'); }
                $statement->bind_param('isis', $httpStatus, $error, $deliveryId, $workerId);
            }
            try {
                if (!$statement->execute()) { throw new \RuntimeException('Could not update webhook HTTP delivery.'); }
                if ($statement->affected_rows !== 1) { $connection->rollback(); $started = false; return false; }
            } finally { $statement->close(); }
            $statement = $connection->prepare('INSERT INTO WHK005 (WHK005_WHK004_Id, WHK005_Attempt_Number, WHK005_Worker_Id, WHK005_Started_At, WHK005_Finished_At, WHK005_Result, WHK005_Http_Status, WHK005_Transport_Error, WHK005_Duration_Ms, WHK005_Response_Bytes) VALUES (?, ?, ?, UTC_TIMESTAMP(), UTC_TIMESTAMP(), ?, ?, ?, ?, ?)');
            if ($statement === false) { throw new \RuntimeException('Could not prepare webhook HTTP attempt history.'); }
            try {
                $error = $transportError === null ? null : substr($transportError, 0, 1000);
                $statement->bind_param('iissisii', $deliveryId, $attemptNumber, $workerId, $result, $httpStatus, $error, $durationMs, $responseBytes);
                if (!$statement->execute()) { throw new \RuntimeException('Could not insert webhook HTTP attempt history.'); }
            } finally { $statement->close(); }
            if (!$connection->commit()) { throw new \RuntimeException('Could not commit webhook HTTP result.'); }
            $started = false;
            return true;
        } catch (\Throwable $exception) {
            if ($started) { $connection->rollback(); }
            throw $exception;
        }
    }

    /** @return array<int,int> */
    private function fetchCandidateIds(\mysqli $connection, string $sql, int $limit): array
    {
        $statement = $connection->prepare($sql);
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook delivery candidate lookup.'); }
        try { $statement->bind_param('i', $limit); if (!$statement->execute()) { throw new \RuntimeException('Could not execute webhook delivery candidate lookup.'); } $result = $statement->get_result(); $ids = array(); while ($row = $result->fetch_assoc()) { $ids[] = (int) $row['WHK004_Id']; } return $ids; } finally { $statement->close(); }
    }
}
