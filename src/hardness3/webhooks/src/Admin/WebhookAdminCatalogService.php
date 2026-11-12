<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin;

use HardnessWebhooks\Admin\Context\WebhookAdminContext;
use HardnessWebhooks\Catalog\Catalog;

/** Safe UI projection; the technical catalog remains its sole authority. */
final class WebhookAdminCatalogService
{
    private $catalog;
    public function __construct(?Catalog $catalog = null) { $this->catalog = $catalog ?? new Catalog(); }
    /** @return array<int,array<string,mixed>> */
    public function available(\mysqli $connection, WebhookAdminContext $context): array
    {
        $result = array();
        foreach ($this->catalog->subscriptionEventsForCompany($connection, $context->getCompanyId()) as $event) {
            $result[] = array(
                'type' => $event['event_type'],
                'label' => $event['label'],
                'description' => isset($event['description']) ? $event['description'] : '',
                'aggregate_label' => isset($event['aggregate_label']) && is_string($event['aggregate_label']) ? $event['aggregate_label'] : 'Registro',
                'payload' => isset($event['payload']) && is_array($event['payload']) ? $event['payload'] : array(),
            );
        }
        return $result;
    }
    /** @param array<int,string> $events */
    public function assertSelectable(\mysqli $connection, WebhookAdminContext $context, array $events): void
    {
        $available = array();
        foreach ($this->available($connection, $context) as $event) { $available[$event['type']] = true; }
        foreach ($events as $event) { if (!isset($available[$event])) { throw new \InvalidArgumentException('Evento não disponível para assinatura.'); } }
    }
}
