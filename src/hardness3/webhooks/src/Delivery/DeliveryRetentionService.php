<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

/** Bounded, manually-invoked retention; scheduling belongs to a later task. */
final class DeliveryRetentionService
{
    public function purge(\mysqli $connection, int $limit = 100): array
    {
        if ($limit < 1) { throw new \InvalidArgumentException('Webhook retention limit must be positive.'); }
        $ids = $this->ids($connection, "SELECT WHK004_Id FROM WHK004 WHERE WHK004_Status = 'D' AND WHK004_Delivered_At < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 90 DAY) ORDER BY WHK004_Delivered_At ASC LIMIT ?", $limit);
        if (count($ids) < $limit) { $ids = array_merge($ids, $this->ids($connection, "SELECT WHK004_Id FROM WHK004 WHERE WHK004_Status = 'C' AND WHK004_Data_Alteracao < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 90 DAY) ORDER BY WHK004_Data_Alteracao ASC LIMIT ?", $limit - count($ids))); }
        if (count($ids) < $limit) { $ids = array_merge($ids, $this->ids($connection, "SELECT WHK004_Id FROM WHK004 WHERE WHK004_Status = 'X' AND WHK004_Data_Alteracao < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 180 DAY) ORDER BY WHK004_Data_Alteracao ASC LIMIT ?", $limit - count($ids))); }
        if ($ids === array()) { return array('attempts' => 0, 'deliveries' => 0, 'events' => 0); }
        $list = implode(',', array_map('intval', $ids)); $started = false;
        try { if (!$connection->begin_transaction()) { throw new \RuntimeException('Could not start webhook retention transaction.'); } $started = true;
            $connection->query("DELETE FROM WHK005 WHERE WHK005_WHK004_Id IN ({$list})"); $attempts = $connection->affected_rows;
            $connection->query("DELETE FROM WHK004 WHERE WHK004_Id IN ({$list}) AND WHK004_Status IN ('D','X','C')"); $deliveries = $connection->affected_rows;
            $connection->query("DELETE e FROM WHK003 e LEFT JOIN WHK004 d ON d.WHK004_WHK003_Id = e.WHK003_Id WHERE d.WHK004_Id IS NULL AND e.WHK003_Data_Criacao < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 180 DAY)"); $events = $connection->affected_rows;
            if (!$connection->commit()) { throw new \RuntimeException('Could not commit webhook retention.'); } return array('attempts' => $attempts, 'deliveries' => $deliveries, 'events' => $events);
        } catch (\Throwable $exception) { if ($started) { $connection->rollback(); } throw $exception; }
    }
    /** @return array<int,int> */ private function ids(\mysqli $connection, string $sql, int $limit): array
    { $statement = $connection->prepare($sql); if ($statement === false) { throw new \RuntimeException('Could not prepare webhook retention lookup.'); } try { $statement->bind_param('i', $limit); $statement->execute(); $result = $statement->get_result(); $ids = array(); while ($row = $result->fetch_assoc()) { $ids[] = (int) $row['WHK004_Id']; } return $ids; } finally { $statement->close(); } }
}
