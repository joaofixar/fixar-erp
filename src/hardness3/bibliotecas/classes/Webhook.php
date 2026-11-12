<?php

declare(strict_types=1);

namespace hardness;

use HardnessWebhooks\Catalog\Catalog;
use HardnessWebhooks\Payload\PayloadBuilderRegistry;
use HardnessWebhooks\Publisher\PublicationResult;
use HardnessWebhooks\Publisher\WebhookPublisher;

// Fixed ERP boundary: customer overrides must not bypass outbox guarantees.
require_once dirname(__DIR__, 2) . '/webhooks/bootstrap.php';

final class Webhook
{
    /**
     * Persists in the caller's current ERP transaction; never opens, commits
     * or rolls back a database transaction.
     *
     */
    public static function publishForCompany(int $companyId, string $eventType, int $aggregateId): PublicationResult
    {
        global $g;

        if ($companyId <= 0) {
            throw new \InvalidArgumentException('Webhook publication requires a positive company ID.');
        }
        if ($aggregateId <= 0) {
            throw new \InvalidArgumentException('Webhook publication requires a positive aggregate ID.');
        }
        if (!isset($g['conexaoBanco']) || !$g['conexaoBanco'] instanceof \mysqli) {
            throw new \RuntimeException('Webhook publication requires the current ERP mysqli connection.');
        }

        $catalog = new Catalog();
        $payload = (new PayloadBuilderRegistry())->get($eventType)->build($g['conexaoBanco'], $companyId, $aggregateId);
        $catalog->validatePayload($eventType, $payload);
        $metadata = $catalog->publicationMetadata($eventType, $payload);

        return (new WebhookPublisher($catalog))->publish($g['conexaoBanco'], $companyId, $eventType, $payload, $metadata);
    }

}
