<?php

declare(strict_types=1);

namespace HardnessWebhooks\Publisher;

final class PublicationResult
{
    /** @var int */
    private $deliveryCount;
    /** @var int */
    private $secretlessEndpointCount;
    /** @var string|null */
    private $eventId;

    public function __construct(int $deliveryCount, int $secretlessEndpointCount, ?string $eventId)
    {
        $this->deliveryCount = $deliveryCount;
        $this->secretlessEndpointCount = $secretlessEndpointCount;
        $this->eventId = $eventId;
    }

    public function hasPersistedEvent(): bool { return $this->eventId !== null; }
    public function getDeliveryCount(): int { return $this->deliveryCount; }
    public function getSecretlessEndpointCount(): int { return $this->secretlessEndpointCount; }
    public function getEventId(): ?string { return $this->eventId; }
}
