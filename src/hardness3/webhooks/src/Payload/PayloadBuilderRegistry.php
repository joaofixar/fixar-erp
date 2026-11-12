<?php

declare(strict_types=1);

namespace HardnessWebhooks\Payload;

use HardnessWebhooks\Exception\UnknownEventException;

final class PayloadBuilderRegistry
{
    /** @var array<string,PayloadBuilderInterface> */
    private $builders = array();

    /** @param array<int,PayloadBuilderInterface>|null $builders */
    public function __construct(?array $builders = null)
    {
        $builders = $builders ?? array(new FinancialAnalysisRequestedPayloadBuilder());
        foreach ($builders as $builder) {
            $this->builders[$builder->eventType()] = $builder;
        }
    }

    public function get(string $eventType): PayloadBuilderInterface
    {
        if (!isset($this->builders[$eventType])) {
            throw new UnknownEventException('Webhook event does not have an internal payload builder: ' . $eventType);
        }

        return $this->builders[$eventType];
    }
}
