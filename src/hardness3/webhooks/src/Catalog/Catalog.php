<?php

declare(strict_types=1);

namespace HardnessWebhooks\Catalog;

use HardnessWebhooks\Exception\UnknownEventException;

/**
 * Controlled technical event catalog.  It deliberately has no dependency on
 * the ERP framework; the database is used only to resolve RESTRICTED grants.
 */
final class Catalog
{
    public const VISIBILITY_PUBLIC = 'PUBLIC';
    public const VISIBILITY_RESTRICTED = 'RESTRICTED';

    /** @var array<string,array<string,mixed>> */
    private $events;

    /**
     * @param array<string,array<string,mixed>>|null $events Test-only custom
     * event definitions. Production code must use the default catalog.
     */
    public function __construct(?array $events = null)
    {
        $this->events = $events === null ? self::defaultEvents() : $events;
    }

    /** @return array<string,mixed> */
    public function get(string $eventType): array
    {
        if (!$this->exists($eventType)) {
            throw new UnknownEventException('Webhook event type is not in the controlled catalog: ' . $eventType);
        }

        return $this->events[$eventType];
    }

    public function exists(string $eventType): bool
    {
        return isset($this->events[$eventType]);
    }

    public function isAllowedForCompany(\mysqli $connection, string $eventType, int $companyId): bool
    {
        if (!$this->exists($eventType) || $companyId <= 0) {
            return false;
        }

        $event = $this->events[$eventType];
        if ($event['visibility'] === self::VISIBILITY_PUBLIC) {
            return true;
        }

        $statement = $connection->prepare(
            'SELECT 1 FROM WHK006 WHERE WHK006_C004_Id = ? AND WHK006_Event_Type = ? AND WHK006_Status = \'A\' LIMIT 1'
        );
        if ($statement === false) {
            throw new \RuntimeException('Could not prepare webhook grant lookup: ' . $connection->error);
        }

        try {
            $statement->bind_param('is', $companyId, $eventType);
            if (!$statement->execute()) {
                throw new \RuntimeException('Could not execute webhook grant lookup: ' . $statement->error);
            }
            $statement->store_result();

            return $statement->num_rows === 1;
        } finally {
            $statement->close();
        }
    }

    /** @return array<int,array<string,mixed>> */
    public function availableForCompany(\mysqli $connection, int $companyId): array
    {
        if ($companyId <= 0) {
            return array();
        }

        $available = array();
        foreach ($this->events as $eventType => $event) {
            if ($this->isAllowedForCompany($connection, $eventType, $companyId)) {
                unset($event['payload_validator']);
                $available[] = $event;
            }
        }

        return $available;
    }

    /**
     * Safe administrative projection of contracts that may be selected for a
     * new subscription by this company.  This deliberately does not affect
     * publication: a defined contract can remain publishable while it is not
     * yet offered for new subscriptions.
     *
     * @return array<int,array<string,mixed>>
     */
    public function subscriptionEventsForCompany(\mysqli $connection, int $companyId): array
    {
        if ($companyId <= 0) {
            return array();
        }

        $events = array();
        foreach ($this->events as $eventType => $event) {
            if (empty($event['subscription_available']) || !$this->isAllowedForCompany($connection, $eventType, $companyId)) {
                continue;
            }

            $events[] = array(
                'event_type' => $event['event_type'],
                'label' => $event['label'],
                'description' => isset($event['description']) ? (string) $event['description'] : '',
                'aggregate_label' => isset($event['aggregate_label']) && is_string($event['aggregate_label']) ? $event['aggregate_label'] : 'Registro',
                'group' => $event['group'],
                'schema_version' => (string) $event['schema_version'],
                'visibility' => $event['visibility'],
                'subscription_available' => true,
                // This is public, fictional contract documentation only. It is
                // deliberately distinct from the runtime validator and never
                // contains an event snapshot, credentials, or other secrets.
                'payload' => isset($event['payload']) && is_array($event['payload']) ? $event['payload'] : array(),
            );
        }

        return $events;
    }

    /**
     * Validates the stable minimum contract shared by the publisher and the
     * event definition. The caller keeps ownership of domain-specific values.
     *
     * @param array<string,mixed> $payload
     */
    public function validatePayload(string $eventType, array $payload): void
    {
        $event = $this->get($eventType);
        $validator = $event['payload_validator'];
        $validator($payload);
    }

    /**
     * Resolves outbox aggregate metadata from the controlled event contract.
     * ERP callers pass only the event payload.
     *
     * @param array<string,mixed> $payload
     * @return array{aggregate_type:string,aggregate_id:string|int}
     */
    public function publicationMetadata(string $eventType, array $payload): array
    {
        $event = $this->get($eventType);
        $field = $event['aggregate_id_payload_field'] ?? null;
        if (!is_string($field) || $field === '') {
            throw new \LogicException('Webhook event does not declare an aggregate ID payload field.');
        }
        $value = $payload;
        foreach (explode('.', $field) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                throw new \InvalidArgumentException('Webhook event requires scalar aggregate ID payload field ' . $field . '.');
            }
            $value = $value[$segment];
        }
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException('Webhook event requires scalar aggregate ID payload field ' . $field . '.');
        }

        return array(
            'aggregate_type' => (string) $event['aggregate_type'],
            'aggregate_id' => $value,
        );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    private static function defaultEvents(): array
    {
        return array(
            'financial_analysis.requested' => array(
                'event_type' => 'financial_analysis.requested',
                'label' => 'Pedido enviado para análise de crédito',
                'description' => 'Disparado quando um pedido é enviado para análise de crédito.',
                'group' => 'Análise de Crédito',
                'visibility' => self::VISIBILITY_PUBLIC,
                'subscription_available' => true,
                'schema_version' => '1',
                'aggregate_type' => 'order',
                // Safe operational label used by the administrative delivery history.
                'aggregate_label' => 'Pedido',
                'aggregate_id_payload_field' => 'order.id',
                'allowed_filters' => array(),
                'payload' => array(
                    'body_format' => 'payload_only',
                    'example' => array(
                        'order' => array('id' => 12345, 'total' => 1500.90),
                        'customer' => array('id' => 987, 'name' => 'EMPRESA EXEMPLO LTDA', 'document' => '12345678000190'),
                    ),
                    'fields' => array(
                        array('path' => 'order.id', 'type' => 'integer', 'description' => 'Identificador do pedido.', 'required' => true, 'nullable' => false),
                        array('path' => 'order.total', 'type' => 'number', 'description' => 'Valor total do pedido.', 'required' => true, 'nullable' => false),
                        array('path' => 'customer.id', 'type' => 'integer', 'description' => 'Identificador do cliente.', 'required' => true, 'nullable' => false),
                        array('path' => 'customer.name', 'type' => 'string', 'description' => 'Nome/Razão Social do cliente.', 'required' => true, 'nullable' => false),
                        array('path' => 'customer.document', 'type' => 'string', 'description' => 'CPF ou CNPJ do cliente, somente números.', 'required' => true, 'nullable' => false),
                    ),
                ),
                'payload_validator' => static function (array $payload): void {
                    if (array_values($payload) === $payload) {
                        throw new \InvalidArgumentException('Webhook payload must be a JSON object.');
                    }
                    if (!isset($payload['order'], $payload['customer']) || !is_array($payload['order']) || !is_array($payload['customer'])) {
                        throw new \InvalidArgumentException('financial_analysis.requested requires order and customer objects.');
                    }
                    if (array_keys($payload) !== array('order', 'customer')
                        || array_keys($payload['order']) !== array('id', 'total')
                        || array_keys($payload['customer']) !== array('id', 'name', 'document')) {
                        throw new \InvalidArgumentException('financial_analysis.requested payload fields do not match its version 1 contract.');
                    }
                    if (!isset($payload['order']['id']) || !is_int($payload['order']['id']) || $payload['order']['id'] <= 0) {
                        throw new \InvalidArgumentException('financial_analysis.requested requires positive integer order.id.');
                    }
                    if (!isset($payload['order']['total']) || !is_float($payload['order']['total']) || $payload['order']['total'] < 0) {
                        throw new \InvalidArgumentException('financial_analysis.requested requires non-negative numeric order.total.');
                    }
                    if (!isset($payload['customer']['id']) || !is_int($payload['customer']['id']) || $payload['customer']['id'] <= 0) {
                        throw new \InvalidArgumentException('financial_analysis.requested requires positive integer customer.id.');
                    }
                    foreach (array('name', 'document') as $field) {
                        if (!isset($payload['customer'][$field]) || !is_string($payload['customer'][$field]) || $payload['customer'][$field] === '') {
                            throw new \InvalidArgumentException('financial_analysis.requested requires non-empty string customer.' . $field . '.');
                        }
                    }
                    if (preg_match('/^\\d+$/', $payload['customer']['document']) !== 1) {
                        throw new \InvalidArgumentException('financial_analysis.requested requires digits-only customer.document.');
                    }
                },
            ),
        );
    }
}
