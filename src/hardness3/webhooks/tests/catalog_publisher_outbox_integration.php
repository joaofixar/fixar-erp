<?php

declare(strict_types=1);

use HardnessWebhooks\Catalog\Catalog;
use HardnessWebhooks\Exception\EventNotAllowedException;
use HardnessWebhooks\Exception\UnknownEventException;
use HardnessWebhooks\Publisher\WebhookPublisher;

require_once dirname(__DIR__) . '/autoload.php';

/*
 * Synthetic integration test for Task 05. It is intentionally opt-in: supply
 * WEBHOOKS_TEST_DB_HOST, WEBHOOKS_TEST_DB_USER, WEBHOOKS_TEST_DB_PASSWORD and
 * WEBHOOKS_TEST_DB_NAME for the DEV database only. In the local ERP container,
 * WEBHOOKS_TEST_ERP_CONFIG may instead point at its existing confUsuario.php;
 * the file is read only to obtain the connection parameters and is never used
 * to boot the framework.
 */
$testConfig = getenv('WEBHOOKS_TEST_ERP_CONFIG');
$testDatabase = array();
if ($testConfig !== false && $testConfig !== '') {
    if (!is_file($testConfig)) {
        fwrite(STDERR, "WEBHOOKS_TEST_ERP_CONFIG does not exist.\n");
        exit(2);
    }
    $confUsuario = array();
    require $testConfig;
    $testDatabase = array(
        'host' => isset($confUsuario['dbHost']) ? $confUsuario['dbHost'] : '',
        'user' => isset($confUsuario['dbUser']) ? $confUsuario['dbUser'] : '',
        'password' => isset($confUsuario['dbPass']) ? $confUsuario['dbPass'] : '',
        'name' => isset($confUsuario['dbDatabase']) ? $confUsuario['dbDatabase'] : '',
    );
} else {
    $testDatabase = array(
        'host' => getenv('WEBHOOKS_TEST_DB_HOST') ?: '',
        'user' => getenv('WEBHOOKS_TEST_DB_USER') ?: '',
        'password' => getenv('WEBHOOKS_TEST_DB_PASSWORD') ?: '',
        'name' => getenv('WEBHOOKS_TEST_DB_NAME') ?: '',
    );
}
foreach (array('host', 'user', 'name') as $key) {
    if ($testDatabase[$key] === '') {
        fwrite(STDERR, "Missing database {$key}; this test only runs with explicit DEV database credentials.\n");
        exit(2);
    }
}

$connection = new mysqli(
    (string) $testDatabase['host'],
    (string) $testDatabase['user'],
    (string) $testDatabase['password'],
    (string) $testDatabase['name']
);
if ($connection->connect_errno !== 0) {
    throw new RuntimeException('Could not connect to the explicit DEV database: ' . $connection->connect_error);
}
$connection->set_charset('utf8mb4');

$companyId = 987654321;
$otherCompanyId = 987654322;
$catalog = new Catalog();
$testEvents = array(
    'financial_analysis.requested' => $catalog->get('financial_analysis.requested'),
    'webhooks.test.restricted' => array(
        'event_type' => 'webhooks.test.restricted',
        'label' => 'Evento restrito sintético',
        'group' => 'Testes técnicos',
        'visibility' => Catalog::VISIBILITY_RESTRICTED,
        'schema_version' => '1',
        'aggregate_type' => 'order',
        'allowed_filters' => array(),
        'payload_validator' => static function (array $payload): void {
            if (array_values($payload) === $payload) {
                throw new InvalidArgumentException('Webhook payload must be a JSON object.');
            }
        },
    ),
);
$testCatalog = new Catalog($testEvents);

function task05Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

function task05Exec(mysqli $connection, string $sql, string $types = '', array $values = array()): mysqli_stmt
{
    $statement = $connection->prepare($sql);
    if ($statement === false) { throw new RuntimeException($connection->error); }
    if ($types !== '') {
        $params = array($types);
        foreach ($values as $index => $value) { $params[] = &$values[$index]; }
        call_user_func_array(array($statement, 'bind_param'), $params);
    }
    if (!$statement->execute()) { throw new RuntimeException($statement->error); }
    return $statement;
}

function task05Count(mysqli $connection, string $table, int $companyId): int
{
    $column = $table . '_C004_Id';
    $statement = task05Exec($connection, "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?", 'i', array($companyId));
    $statement->bind_result($count);
    $statement->fetch();
    $statement->close();
    return (int) $count;
}

function task05Cleanup(mysqli $connection, int $companyId): void
{
    task05Exec($connection, 'DELETE d FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id = d.WHK004_WHK003_Id WHERE e.WHK003_C004_Id = ?', 'i', array($companyId))->close();
    task05Exec($connection, 'DELETE FROM WHK003 WHERE WHK003_C004_Id = ?', 'i', array($companyId))->close();
    task05Exec($connection, 'DELETE FROM WHK002 WHERE WHK002_C004_Id = ?', 'i', array($companyId))->close();
    task05Exec($connection, 'DELETE v FROM WHK007 v INNER JOIN WHK001 e ON e.WHK001_Id = v.WHK007_WHK001_Id WHERE e.WHK001_C004_Id = ?', 'i', array($companyId))->close();
    task05Exec($connection, 'DELETE FROM WHK001 WHERE WHK001_C004_Id = ?', 'i', array($companyId))->close();
    task05Exec($connection, 'DELETE FROM WHK006 WHERE WHK006_C004_Id = ?', 'i', array($companyId))->close();
}

function task05Endpoint(mysqli $connection, int $companyId, string $url, string $endpointStatus, string $subscriptionStatus, bool $activeSecret): int
{
    $hash = hash('sha256', $url, true);
    $statement = task05Exec($connection, 'INSERT INTO WHK001 (WHK001_C004_Id, WHK001_Nome, WHK001_URL, WHK001_URL_Hash, WHK001_Status) VALUES (?, ?, ?, ?, ?)', 'issss', array($companyId, 'Task 05 synthetic', $url, $hash, $endpointStatus));
    $endpointId = (int) $connection->insert_id;
    $statement->close();
    $statement = task05Exec($connection, 'INSERT INTO WHK007 (WHK007_WHK001_Id, WHK007_Versao, WHK007_Secret_Criptografado, WHK007_Status) VALUES (?, 1, ?, ?)', 'iss', array($endpointId, 'synthetic-only', $activeSecret ? 'A' : 'I'));
    $secretId = (int) $connection->insert_id;
    $statement->close();
    task05Exec($connection, 'UPDATE WHK001 SET WHK001_WHK007_Id_Atual = ? WHERE WHK001_Id = ?', 'ii', array($secretId, $endpointId))->close();
    task05Exec($connection, 'INSERT INTO WHK002 (WHK002_C004_Id, WHK002_WHK001_Id, WHK002_Event_Type, WHK002_Status) VALUES (?, ?, \'financial_analysis.requested\', ?)', 'iis', array($companyId, $endpointId, $subscriptionStatus))->close();
    return $endpointId;
}

try {
    task05Cleanup($connection, $companyId);
    task05Cleanup($connection, $otherCompanyId);

    task05Assert($catalog->exists('financial_analysis.requested'), 'PUBLIC catalog event exists');
    try { $catalog->get('event.inventado'); task05Assert(false, 'unknown event must throw'); } catch (UnknownEventException $exception) { }
    task05Assert(!$testCatalog->isAllowedForCompany($connection, 'webhooks.test.restricted', $companyId), 'RESTRICTED without grant is denied');
    task05Exec($connection, 'INSERT INTO WHK006 (WHK006_C004_Id, WHK006_Event_Type, WHK006_Status) VALUES (?, \'webhooks.test.restricted\', \'A\')', 'i', array($companyId))->close();
    task05Assert($testCatalog->isAllowedForCompany($connection, 'webhooks.test.restricted', $companyId), 'active grant permits RESTRICTED event');
    task05Exec($connection, 'UPDATE WHK006 SET WHK006_Status = \'I\' WHERE WHK006_C004_Id = ? AND WHK006_Event_Type = \'webhooks.test.restricted\'', 'i', array($companyId))->close();
    task05Assert(!$testCatalog->isAllowedForCompany($connection, 'webhooks.test.restricted', $companyId), 'inactive grant is denied');
    task05Exec($connection, 'INSERT INTO WHK006 (WHK006_C004_Id, WHK006_Event_Type, WHK006_Status) VALUES (?, \'webhooks.test.restricted\', \'A\')', 'i', array($otherCompanyId))->close();
    $available = $testCatalog->availableForCompany($connection, $companyId);
    task05Assert(count($available) === 1 && $available[0]['event_type'] === 'financial_analysis.requested', 'availableForCompany does not leak another company grant');

    $publisher = new WebhookPublisher($catalog);
    $payload = array('order' => array('id' => 5005, 'total' => 1500.90), 'customer' => array('id' => 6005, 'name' => 'Task 05 customer', 'document' => '12345678000190'));
    $metadata = array('aggregate_type' => 'order', 'aggregate_id' => 5005);
    task05Assert(!$publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata)->hasPersistedEvent(), 'no endpoint creates no outbox');
    task05Endpoint($connection, $companyId, 'https://inactive.example.test/hook', 'I', 'A', true);
    task05Assert(!$publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata)->hasPersistedEvent(), 'inactive endpoint creates no outbox');
    task05Endpoint($connection, $companyId, 'https://inactive-subscription.example.test/hook', 'A', 'I', true);
    task05Assert(!$publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata)->hasPersistedEvent(), 'inactive subscription creates no outbox');
    task05Endpoint($connection, $otherCompanyId, 'https://other-company.example.test/hook', 'A', 'A', true);
    task05Assert(!$publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata)->hasPersistedEvent(), 'other company endpoint creates no outbox');
    try { (new WebhookPublisher($testCatalog))->publish($connection, $companyId, 'webhooks.test.restricted', $payload, $metadata); task05Assert(false, 'unauthorized event must throw'); } catch (EventNotAllowedException $exception) { }
    task05Endpoint($connection, $companyId, 'https://no-active-secret.example.test/hook', 'A', 'A', false);
    $secretless = $publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata);
    $unsigned = task05Exec($connection, 'SELECT WHK004_WHK007_Id FROM WHK004 WHERE WHK004_WHK003_Id=(SELECT WHK003_Id FROM WHK003 WHERE WHK003_Event_Id=?) LIMIT 1', 's', array($secretless->getEventId())); $unsigned->bind_result($unsignedSecretId); $unsigned->fetch(); $unsigned->close();
    task05Assert($secretless->hasPersistedEvent() && $secretless->getDeliveryCount() === 1 && $unsignedSecretId === null, 'endpoint without HMAC creates an unsigned delivery');
    $endpointOne = task05Endpoint($connection, $companyId, 'https://one.example.test/hook', 'A', 'A', true);
    $first = $publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata);
    task05Assert($first->getDeliveryCount() === 2, 'unsigned and signed endpoints both create deliveries');
    $endpointTwo = task05Endpoint($connection, $companyId, 'https://two.example.test/hook', 'A', 'A', true);
    $second = $publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata);
    task05Assert($second->getDeliveryCount() === 3 && $second->getEventId() !== $first->getEventId(), 'unsigned and two signed endpoints fan out from one new event');
    $row = task05Exec($connection, 'SELECT e.WHK003_Event_Id, e.WHK003_Payload, d.WHK004_Delivery_Id, d.WHK004_Destination_URL, d.WHK004_WHK007_Id FROM WHK003 e INNER JOIN WHK004 d ON d.WHK004_WHK003_Id = e.WHK003_Id WHERE e.WHK003_Event_Id = ? AND d.WHK004_WHK001_Id = ? LIMIT 1', 'si', array($second->getEventId(), $endpointOne));
    $row->bind_result($eventId, $storedPayload, $deliveryId, $destinationUrl, $secretId); $row->fetch(); $row->close();
    task05Assert($eventId === $second->getEventId() && $deliveryId !== $eventId, 'event and delivery UUIDs are distinct');
    task05Assert($destinationUrl === 'https://one.example.test/hook', 'delivery freezes destination URL');
    $secretCheck = task05Exec($connection, 'SELECT WHK001_WHK007_Id_Atual FROM WHK001 WHERE WHK001_Id = ?', 'i', array($endpointOne));
    $secretCheck->bind_result($currentSecretId); $secretCheck->fetch(); $secretCheck->close();
    task05Assert((int) $secretId === (int) $currentSecretId && json_decode($storedPayload, true) === $payload, 'delivery references current secret and preserves the current payload contract');

    $connection->begin_transaction();
    $rolledBack = $publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata);
    $connection->rollback();
    task05Assert(task05Count($connection, 'WHK003', $companyId) === 3 && task05Count($connection, 'WHK004', $companyId) === 6, 'rollback removes publisher records');
    $connection->begin_transaction();
    $committed = $publisher->publish($connection, $companyId, 'financial_analysis.requested', $payload, $metadata);
    $connection->commit();
    task05Assert($committed->hasPersistedEvent() && task05Count($connection, 'WHK003', $companyId) === 4 && task05Count($connection, 'WHK004', $companyId) === 9, 'commit preserves publisher records');

    echo "Task 05 synthetic tests passed.\n";
} finally {
    if ($connection->errno === 0) {
        task05Cleanup($connection, $companyId);
        task05Cleanup($connection, $otherCompanyId);
    }
    $connection->close();
}


/* Subscription catalog projection regression coverage. */

/* Reuses the explicit DEV credentials already validated by the outbox scenario. */
$connection = new mysqli($testDatabase['host'], $testDatabase['user'], $testDatabase['password'], $testDatabase['name']);
if ($connection->connect_errno !== 0) { throw new RuntimeException('Could not connect to DEV database: ' . $connection->connect_error); }
$connection->set_charset('utf8mb4');
$companyId = 987654334;

function task14Assert(bool $condition, string $message): void { if (!$condition) { throw new RuntimeException('Assertion failed: ' . $message); } }
function task14Exec(mysqli $connection, string $sql, string $types = '', array $values = array()): mysqli_stmt {
    $statement = $connection->prepare($sql);
    if ($statement === false) { throw new RuntimeException($connection->error); }
    if ($types !== '') { $params = array($types); foreach ($values as $index => $value) { $params[] = &$values[$index]; } call_user_func_array(array($statement, 'bind_param'), $params); }
    if (!$statement->execute()) { throw new RuntimeException($statement->error); }
    return $statement;
}
function task14Cleanup(mysqli $connection, int $companyId): void {
    task14Exec($connection, 'DELETE d FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id = d.WHK004_WHK003_Id WHERE e.WHK003_C004_Id = ?', 'i', array($companyId))->close();
    task14Exec($connection, 'DELETE FROM WHK003 WHERE WHK003_C004_Id = ?', 'i', array($companyId))->close();
    task14Exec($connection, 'DELETE FROM WHK002 WHERE WHK002_C004_Id = ?', 'i', array($companyId))->close();
    task14Exec($connection, 'DELETE v FROM WHK007 v INNER JOIN WHK001 e ON e.WHK001_Id = v.WHK007_WHK001_Id WHERE e.WHK001_C004_Id = ?', 'i', array($companyId))->close();
    task14Exec($connection, 'DELETE FROM WHK001 WHERE WHK001_C004_Id = ?', 'i', array($companyId))->close();
    task14Exec($connection, 'DELETE FROM WHK006 WHERE WHK006_C004_Id = ?', 'i', array($companyId))->close();
}

try {
    task14Cleanup($connection, $companyId);
    $catalog = new Catalog();
    $events = array(
        'financial_analysis.requested' => $catalog->get('financial_analysis.requested'),
        'webhooks.test.restricted' => array(
            'event_type' => 'webhooks.test.restricted', 'label' => 'Evento restrito sintético', 'group' => 'Testes técnicos',
            'visibility' => Catalog::VISIBILITY_RESTRICTED, 'subscription_available' => true, 'schema_version' => '1',
            'aggregate_type' => 'order', 'allowed_filters' => array(), 'payload_validator' => static function (array $payload): void { if (array_values($payload) === $payload) { throw new InvalidArgumentException('Webhook payload must be a JSON object.'); } },
        ),
    );
    $testCatalog = new Catalog($events);

    task14Assert($catalog->exists('financial_analysis.requested'), 'defined available event exists');
    task14Assert($catalog->get('financial_analysis.requested')['subscription_available'] === true, 'requested is explicitly subscription available');
    task14Assert(!$catalog->exists('financial_analysis.approved'), 'obsolete approved event is absent from the production catalog');
    try { $catalog->get('webhooks.unknown'); task14Assert(false, 'unknown event must throw'); } catch (UnknownEventException $exception) { }

    $public = $catalog->subscriptionEventsForCompany($connection, $companyId);
    task14Assert(count($public) === 1 && $public[0]['event_type'] === 'financial_analysis.requested' && $public[0]['visibility'] === Catalog::VISIBILITY_PUBLIC, 'PUBLIC available event is projected and unavailable event is omitted');
    task14Assert(array_keys($public[0]) === array('event_type', 'label', 'description', 'aggregate_label', 'group', 'schema_version', 'visibility', 'subscription_available', 'payload') && !isset($public[0]['payload_validator'], $public[0]['aggregate_type']), 'administrative projection exposes public payload documentation and safe aggregate labels, but not runtime implementation details');
    task14Assert($public[0]['payload']['example']['customer']['name'] === 'EMPRESA EXEMPLO LTDA' && $public[0]['payload']['fields'][4] === array('path' => 'customer.document', 'type' => 'string', 'description' => 'CPF ou CNPJ do cliente, somente números.', 'required' => true, 'nullable' => false), 'administrative projection preserves the fictional payload example and field documentation');

    $withoutGrant = $testCatalog->subscriptionEventsForCompany($connection, $companyId);
    task14Assert(count($withoutGrant) === 1, 'RESTRICTED event without grant is not offered');
    task14Exec($connection, "INSERT INTO WHK006 (WHK006_C004_Id, WHK006_Event_Type, WHK006_Status) VALUES (?, 'webhooks.test.restricted', 'A')", 'i', array($companyId))->close();
    $withGrant = $testCatalog->subscriptionEventsForCompany($connection, $companyId);
    task14Assert(count($withGrant) === 2 && $withGrant[1]['event_type'] === 'webhooks.test.restricted' && $withGrant[1]['visibility'] === Catalog::VISIBILITY_RESTRICTED, 'RESTRICTED available event with grant is offered');

    $url = 'https://task14.example.test/hook';
    task14Exec($connection, 'INSERT INTO WHK001 (WHK001_C004_Id, WHK001_Nome, WHK001_URL, WHK001_URL_Hash, WHK001_Status) VALUES (?, ?, ?, ?, \'A\')', 'isss', array($companyId, 'Task 14 synthetic', $url, hash('sha256', $url, true)))->close();
    $endpointId = (int) $connection->insert_id;
    task14Exec($connection, "INSERT INTO WHK007 (WHK007_WHK001_Id, WHK007_Versao, WHK007_Secret_Criptografado, WHK007_Status) VALUES (?, 1, 'synthetic-only', 'A')", 'i', array($endpointId))->close();
    $secretId = (int) $connection->insert_id;
    task14Exec($connection, 'UPDATE WHK001 SET WHK001_WHK007_Id_Atual = ? WHERE WHK001_Id = ?', 'ii', array($secretId, $endpointId))->close();
    task14Exec($connection, "INSERT INTO WHK002 (WHK002_C004_Id, WHK002_WHK001_Id, WHK002_Event_Type, WHK002_Status) VALUES (?, ?, 'financial_analysis.requested', 'A')", 'ii', array($companyId, $endpointId))->close();
    $published = (new WebhookPublisher($catalog))->publish($connection, $companyId, 'financial_analysis.requested', array('order' => array('id' => 14014, 'total' => 1500.90), 'customer' => array('id' => 24014, 'name' => 'Task 14 customer', 'document' => '12345678000190')), array('aggregate_type' => 'order', 'aggregate_id' => 14014));
    task14Assert($published->getDeliveryCount() === 1, 'Publisher remains independent from the subscription projection');
    echo "Task 14 synthetic tests passed.\n";
} finally {
    task14Cleanup($connection, $companyId);
    $connection->close();
}
