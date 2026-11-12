<?php

declare(strict_types=1);

namespace HardnessWebhooks\Publisher;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use HardnessWebhooks\Catalog\Catalog;
use HardnessWebhooks\Exception\EventNotAllowedException;

final class WebhookPublisher
{
    /** @var Catalog */
    private $catalog;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * Persists a single event and one delivery for each eligible endpoint.
     * The supplied connection belongs to the caller: this method never begins,
     * commits or rolls back a transaction.
     *
     * @param array<string,mixed> $payload
     * @param array{aggregate_type:string,aggregate_id:string|int,occurred_at?:DateTimeInterface} $metadata
     */
    public function publish(\mysqli $connection, int $companyId, string $eventType, array $payload, array $metadata): PublicationResult
    {
        $event = $this->catalog->get($eventType);
        if (!$this->catalog->isAllowedForCompany($connection, $eventType, $companyId)) {
            throw new EventNotAllowedException('Webhook event is not allowed for this company.');
        }
        if (!isset($metadata['aggregate_type'], $metadata['aggregate_id']) || !is_scalar($metadata['aggregate_id'])) {
            throw new \InvalidArgumentException('Webhook publication requires aggregate_type and scalar aggregate_id.');
        }
        if ($metadata['aggregate_type'] !== $event['aggregate_type']) {
            throw new \InvalidArgumentException('Webhook aggregate_type does not match the event catalog.');
        }

        $this->catalog->validatePayload($eventType, $payload);
        $payloadJson = $this->encodePayload($payload);
        $recipients = $this->findEligibleRecipients($connection, $companyId, $eventType);
        if ($recipients['eligible'] === array()) {
            return new PublicationResult(0, $recipients['secretless'], null);
        }

        $eventId = self::uuidV4();
        $eventRecordId = 0;
        try {
            $occurredAt = $this->formatOccurredAt($metadata);
            $eventRecordId = $this->insertEvent(
                $connection,
                $eventId,
                $companyId,
                $eventType,
                (string) $event['schema_version'],
                (string) $metadata['aggregate_type'],
                (string) $metadata['aggregate_id'],
                $payloadJson,
                $occurredAt
            );

            foreach ($recipients['eligible'] as $recipient) {
                $this->insertDelivery($connection, self::uuidV4(), $companyId, $eventRecordId, $recipient);
            }
        } catch (\Throwable $exception) {
            if ($eventRecordId > 0) {
                $this->discardPartialPublication($connection, $eventRecordId);
            }
            throw $exception;
        }

        return new PublicationResult(count($recipients['eligible']), $recipients['secretless'], $eventId);
    }

    /** @return array{eligible:array<int,array<string,int|string|null>>,secretless:int} */
    private function findEligibleRecipients(\mysqli $connection, int $companyId, string $eventType): array
    {
        $sql = 'SELECT s.WHK002_Id, e.WHK001_Id, e.WHK001_URL, e.WHK001_WHK007_Id_Atual, '
            . 'v.WHK007_Id, v.WHK007_Status '
            . 'FROM WHK002 s INNER JOIN WHK001 e ON e.WHK001_Id = s.WHK002_WHK001_Id '
            . 'LEFT JOIN WHK007 v ON v.WHK007_Id = e.WHK001_WHK007_Id_Atual AND v.WHK007_WHK001_Id = e.WHK001_Id '
            . 'WHERE s.WHK002_C004_Id = ? AND s.WHK002_Event_Type = ? AND s.WHK002_Status = \'A\' '
            . 'AND e.WHK001_C004_Id = ? AND e.WHK001_Status = \'A\'';
        $statement = $connection->prepare($sql);
        if ($statement === false) {
            throw new \RuntimeException('Could not prepare webhook recipient lookup: ' . $connection->error);
        }
        try {
            $statement->bind_param('isi', $companyId, $eventType, $companyId);
            if (!$statement->execute()) {
                throw new \RuntimeException('Could not execute webhook recipient lookup: ' . $statement->error);
            }
            $statement->bind_result($subscriptionId, $endpointId, $destinationUrl, $currentSecretId, $secretId, $secretStatus);
            $eligible = array();
            $secretless = 0;
            while ($statement->fetch()) {
                $eligible[] = array(
                    'subscription_id' => (int) $subscriptionId,
                    'endpoint_id' => (int) $endpointId,
                    'secret_id' => $secretId !== null && $secretStatus === 'A' ? (int) $secretId : null,
                    'destination_url' => (string) $destinationUrl,
                );
            }

            return array('eligible' => $eligible, 'secretless' => $secretless);
        } finally {
            $statement->close();
        }
    }

    private function encodePayload(array $payload): string
    {
        try {
            return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \InvalidArgumentException('Webhook payload cannot be encoded as JSON.', 0, $exception);
        }
    }

    /** @param array<string,mixed> $metadata */
    private function formatOccurredAt(array $metadata): string
    {
        $occurredAt = isset($metadata['occurred_at']) ? $metadata['occurred_at'] : new DateTimeImmutable('now');
        if (!$occurredAt instanceof DateTimeInterface) {
            throw new \InvalidArgumentException('Webhook occurred_at must be a DateTimeInterface.');
        }

        return $occurredAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }

    private function insertEvent(\mysqli $connection, string $eventId, int $companyId, string $eventType, string $schemaVersion, string $aggregateType, string $aggregateId, string $payload, string $occurredAt): int
    {
        $statement = $connection->prepare('INSERT INTO WHK003 (WHK003_Event_Id, WHK003_C004_Id, WHK003_Type, WHK003_Schema_Version, WHK003_Aggregate_Type, WHK003_Aggregate_Id, WHK003_Payload, WHK003_Occurred_At) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook event insert: ' . $connection->error); }
        try {
            $statement->bind_param('sissssss', $eventId, $companyId, $eventType, $schemaVersion, $aggregateType, $aggregateId, $payload, $occurredAt);
            if (!$statement->execute()) { throw new \RuntimeException('Could not insert webhook event: ' . $statement->error); }
            return (int) $connection->insert_id;
        } finally { $statement->close(); }
    }

    /** @param array<string,int|string|null> $recipient */
    private function insertDelivery(\mysqli $connection, string $deliveryId, int $companyId, int $eventRecordId, array $recipient): void
    {
        $statement = $connection->prepare('INSERT INTO WHK004 (WHK004_Delivery_Id, WHK004_C004_Id, WHK004_WHK003_Id, WHK004_WHK001_Id, WHK004_WHK002_Id, WHK004_WHK007_Id, WHK004_Destination_URL, WHK004_Next_Attempt_At) VALUES (?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())');
        if ($statement === false) { throw new \RuntimeException('Could not prepare webhook delivery insert: ' . $connection->error); }
        try {
            $statement->bind_param('siiiiis', $deliveryId, $companyId, $eventRecordId, $recipient['endpoint_id'], $recipient['subscription_id'], $recipient['secret_id'], $recipient['destination_url']);
            if (!$statement->execute()) { throw new \RuntimeException('Could not insert webhook delivery: ' . $statement->error); }
        } finally { $statement->close(); }
    }

    /** Removes an incomplete event best-effort without taking transaction ownership. */
    private function discardPartialPublication(\mysqli $connection, int $eventRecordId): void
    {
        try {
            $deliveryStatement = $connection->prepare('DELETE FROM WHK004 WHERE WHK004_WHK003_Id = ?');
            if ($deliveryStatement !== false) {
                try { $deliveryStatement->bind_param('i', $eventRecordId); $deliveryStatement->execute(); } finally { $deliveryStatement->close(); }
            }
            $eventStatement = $connection->prepare('DELETE FROM WHK003 WHERE WHK003_Id = ?');
            if ($eventStatement !== false) {
                try { $eventStatement->bind_param('i', $eventRecordId); $eventStatement->execute(); } finally { $eventStatement->close(); }
            }
        } catch (\Throwable $ignored) {
            // The caller reports the original publication failure independently.
        }
    }

    private static function uuidV4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        $hex = bin2hex($bytes);

        return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20);
    }
}
