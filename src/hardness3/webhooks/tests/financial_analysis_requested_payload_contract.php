<?php

declare(strict_types=1);

use HardnessWebhooks\Catalog\Catalog;

require_once dirname(__DIR__) . '/autoload.php';

function task28Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

$catalog = new Catalog();
$payload = array(
    'order' => array('id' => 12345, 'total' => 1500.90),
    'customer' => array('id' => 987, 'name' => 'EMPRESA TESTE LTDA', 'document' => '12345678000190'),
);

$catalog->validatePayload('financial_analysis.requested', $payload);
task28Assert(
    $catalog->publicationMetadata('financial_analysis.requested', $payload) === array('aggregate_type' => 'order', 'aggregate_id' => 12345),
    'order.id is the aggregate ID while remaining in the specific payload'
);
task28Assert(
    json_encode($payload, JSON_PRESERVE_ZERO_FRACTION) === '{"order":{"id":12345,"total":1500.9},"customer":{"id":987,"name":"EMPRESA TESTE LTDA","document":"12345678000190"}}',
    'the version 1 payload has only the requested nested fields and keeps document as a string'
);

try {
    $catalog->validatePayload('financial_analysis.requested', array(
        'order' => array('id' => 12345, 'total' => 1500.90, 'number' => 12345),
        'customer' => array('id' => 987, 'name' => 'EMPRESA TESTE LTDA', 'document' => '12345678000190'),
    ));
    task28Assert(false, 'order.number is not accepted by the initial contract');
} catch (InvalidArgumentException $exception) {
}

try {
    $invalidDocument = $payload;
    $invalidDocument['customer']['document'] = '12.345.678/0001-90';
    $catalog->validatePayload('financial_analysis.requested', $invalidDocument);
    task28Assert(false, 'formatted customer.document is not accepted');
} catch (InvalidArgumentException $exception) {
}

echo "Task 28 payload contract tests passed.\n";
