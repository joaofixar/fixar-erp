<?php

declare(strict_types=1);

use HardnessWebhooks\Catalog\Catalog;
use HardnessWebhooks\Delivery\DeliveryRepository;
use HardnessWebhooks\Delivery\DeliveryWorker;
use HardnessWebhooks\Delivery\DestinationValidator;
use HardnessWebhooks\Delivery\HttpClientInterface;
use HardnessWebhooks\Delivery\HttpResult;
use HardnessWebhooks\Delivery\RetryPolicy;
use HardnessWebhooks\Delivery\ValidatedDestination;
use HardnessWebhooks\Delivery\WebhookHttpDispatcher;
use HardnessWebhooks\Publisher\WebhookPublisher;
use HardnessWebhooks\Secrets\SecretVersionService;
use HardnessWebhooks\Security\HmacVerifier;
use HardnessWebhooks\Security\SecretCipher;

require_once dirname(__DIR__) . '/autoload.php';

function task10Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

function task10Exec(mysqli $connection, string $sql, string $types = '', array $values = array()): mysqli_stmt
{
    $statement = $connection->prepare($sql);
    if ($statement === false) {
        throw new RuntimeException('Database prepare failed.');
    }
    if ($types !== '') {
        $parameters = array($types);
        foreach ($values as $index => $value) {
            $parameters[] = &$values[$index];
        }
        call_user_func_array(array($statement, 'bind_param'), $parameters);
    }
    if (!$statement->execute()) {
        $statement->close();
        throw new RuntimeException('Database execution failed.');
    }
    return $statement;
}

function task10One(mysqli $connection, string $sql, string $types, array $values): array
{
    $statement = task10Exec($connection, $sql, $types, $values);
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();
    return is_array($row) ? $row : array();
}

function task10Cleanup(mysqli $connection, int $companyId): void
{
    task10Exec($connection, 'DELETE a FROM WHK005 a INNER JOIN WHK004 d ON d.WHK004_Id = a.WHK005_WHK004_Id WHERE d.WHK004_C004_Id = ?', 'i', array($companyId))->close();
    task10Exec($connection, 'DELETE FROM WHK004 WHERE WHK004_C004_Id = ?', 'i', array($companyId))->close();
    task10Exec($connection, 'DELETE FROM WHK003 WHERE WHK003_C004_Id = ?', 'i', array($companyId))->close();
    task10Exec($connection, 'DELETE FROM WHK002 WHERE WHK002_C004_Id = ?', 'i', array($companyId))->close();
    task10Exec($connection, 'DELETE v FROM WHK007 v INNER JOIN WHK001 e ON e.WHK001_Id = v.WHK007_WHK001_Id WHERE e.WHK001_C004_Id = ?', 'i', array($companyId))->close();
    task10Exec($connection, 'DELETE FROM WHK001 WHERE WHK001_C004_Id = ?', 'i', array($companyId))->close();
}

final class Task10Validator extends DestinationValidator
{
    /** @return array<int,string> */
    protected function resolve(string $host): array
    {
        return array('93.184.216.34');
    }
}

/** Controlled in-process receiver. It never writes body, signature or secret to logs/database. */
final class Task10Receiver implements HttpClientInterface
{
    /** @var string */
    private $secret;
    /** @var HttpResult */
    private $response;
    /** @var string */
    private $expectedEventId;
    /** @var string */
    private $expectedDeliveryId;
    /** @var string */
    private $expectedRawBody;
    /** @var bool */
    private $received = false;
    /** @var bool */
    private $hmacValid = false;
    /** @var bool */
    private $identifiersValid = false;
    /** @var bool */
    private $literalBodyValid = false;

    public function __construct(string $secret, HttpResult $response, string $expectedEventId, string $expectedDeliveryId, string $expectedRawBody)
    {
        $this->secret = $secret;
        $this->response = $response;
        $this->expectedEventId = $expectedEventId;
        $this->expectedDeliveryId = $expectedDeliveryId;
        $this->expectedRawBody = $expectedRawBody;
    }

    public function post(ValidatedDestination $destination, string $rawBody, array $headers): HttpResult
    {
        $timestamp = isset($headers['Hardness-Timestamp']) ? (int) $headers['Hardness-Timestamp'] : 0;
        $signature = isset($headers['Hardness-Signature']) ? (string) $headers['Hardness-Signature'] : '';
        $this->received = true;
        $this->literalBodyValid = $rawBody === $this->expectedRawBody;
        $this->hmacValid = HmacVerifier::verify($this->secret, $timestamp, $rawBody, $signature, $timestamp);
        $this->identifiersValid = isset($headers['Hardness-Event-Id'], $headers['Hardness-Delivery-Id'])
            && $headers['Hardness-Event-Id'] === $this->expectedEventId
            && $headers['Hardness-Delivery-Id'] === $this->expectedDeliveryId;

        return $this->response;
    }

    public function wasReceived(): bool { return $this->received; }
    public function hasValidHmac(): bool { return $this->hmacValid; }
    public function hasValidIdentifiers(): bool { return $this->identifiersValid; }
    public function hasLiteralBody(): bool { return $this->literalBodyValid; }
}

function task10Publish(mysqli $connection, WebhookPublisher $publisher, int $companyId, int $orderId, string $marker): array
{
    if (!$connection->begin_transaction()) {
        throw new RuntimeException('Could not start synthetic publication transaction.');
    }
    try {
        $publication = $publisher->publish($connection, $companyId, 'financial_analysis.requested', array(
            'order' => array('id' => $orderId, 'total' => 1500.90),
            'customer' => array('id' => 900000000 + $orderId, 'name' => 'Task 10 customer', 'document' => '12345678000190'),
        ), array('aggregate_type' => 'order', 'aggregate_id' => $orderId));
        task10Assert($publication->getDeliveryCount() === 1 && $publication->getEventId() !== null, 'Publisher creates one synthetic outbox delivery');
        if (!$connection->commit()) {
            throw new RuntimeException('Could not commit synthetic publication transaction.');
        }
    } catch (Throwable $exception) {
        $connection->rollback();
        throw $exception;
    }

    return task10One($connection, 'SELECT d.WHK004_Id, d.WHK004_Delivery_Id, d.WHK004_Status, e.WHK003_Id, e.WHK003_Event_Id, e.WHK003_Payload FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id = d.WHK004_WHK003_Id WHERE e.WHK003_Event_Id = ?', 's', array((string) $publication->getEventId()));
}

$configPath = (string) getenv('WEBHOOKS_TEST_ERP_CONFIG');
if ($configPath === '' || !is_file($configPath)) {
    fwrite(STDERR, "WEBHOOKS_TEST_ERP_CONFIG must identify the explicit DEV ERP configuration.\n");
    exit(2);
}
$confUsuario = array();
require $configPath;
$connection = new mysqli((string) $confUsuario['dbHost'], (string) $confUsuario['dbUser'], (string) $confUsuario['dbPass'], (string) $confUsuario['dbDatabase']);
if ($connection->connect_errno !== 0) {
    throw new RuntimeException('Could not connect to explicit DEV database.');
}
$connection->set_charset('utf8mb4');

$companyId = 987654327;
$url = 'https://task10.example.test/controlled-receiver';
$repository = new DeliveryRepository(90);
$secrets = new SecretVersionService(new SecretCipher(random_bytes(32))); // Process-local test key, never printed or persisted.
$publisher = new WebhookPublisher(new Catalog());
$policy = new RetryPolicy(array(60, 300, 900, 3600, 21600), 0.0, 21600, static function (int $minimum, int $maximum): int { return 0; });

try {
    task10Cleanup($connection, $companyId);
    task10Exec($connection, "INSERT INTO WHK001 (WHK001_C004_Id, WHK001_Nome, WHK001_URL, WHK001_URL_Hash, WHK001_Status) VALUES (?, 'Task 10 controlled receiver', ?, ?, 'A')", 'iss', array($companyId, $url, hash('sha256', $url, true)))->close();
    $endpointId = (int) $connection->insert_id;
    $secretVersion = $secrets->createVersion($connection, $endpointId);
    task10Exec($connection, "INSERT INTO WHK002 (WHK002_C004_Id, WHK002_WHK001_Id, WHK002_Event_Type, WHK002_Status) VALUES (?, ?, 'financial_analysis.requested', 'A')", 'ii', array($companyId, $endpointId))->close();

    // Success: publisher -> outbox -> worker -> controlled HTTP receiver -> WHK005/D.
    $success = task10Publish($connection, $publisher, $companyId, 1001, 'success');
    task10Assert($success['WHK004_Status'] === 'P' && $success['WHK003_Payload'] === '{"order":{"id":1001,"total":1500.9},"customer":{"id":900001001,"name":"Task 10 customer","document":"12345678000190"}}', 'WHK003 and WHK004 persist the synthetic event before delivery');
    $successReceiver = new Task10Receiver($secretVersion->getPlaintextSecret(), new HttpResult(204, null, 7, 0), $success['WHK003_Event_Id'], $success['WHK004_Delivery_Id'], $success['WHK003_Payload']);
    $successWorker = new DeliveryWorker($repository, new WebhookHttpDispatcher($repository, $secrets, new Task10Validator(), $successReceiver, $policy), 1);
    $summary = $successWorker->runTenant($connection, 'task10-success-worker');
    $successState = task10One($connection, 'SELECT d.WHK004_Status, d.WHK004_Last_Http_Status, d.WHK004_Attempt_Count, a.WHK005_Result, a.WHK005_Http_Status FROM WHK004 d INNER JOIN WHK005 a ON a.WHK005_WHK004_Id = d.WHK004_Id WHERE d.WHK004_Id = ?', 'i', array((int) $success['WHK004_Id']));
    task10Assert($summary['claimed'] === 1 && $summary['dispatched'] === 1 && $successState['WHK004_Status'] === 'D' && (int) $successState['WHK004_Last_Http_Status'] === 204 && (int) $successState['WHK004_Attempt_Count'] === 1 && $successState['WHK005_Result'] === 'http_success', '2xx produces WHK004 D and one WHK005 success');
    task10Assert($successReceiver->wasReceived() && $successReceiver->hasLiteralBody() && $successReceiver->hasValidHmac() && $successReceiver->hasValidIdentifiers(), 'controlled receiver verifies literal body, HMAC, Event-Id and Delivery-Id');

    // Retry: a controlled 429 with Retry-After proves R, WHK005 and the persisted next attempt.
    $retry = task10Publish($connection, $publisher, $companyId, 1002, 'retry');
    $retryReceiver = new Task10Receiver($secretVersion->getPlaintextSecret(), new HttpResult(429, null, 5, 0, array('Retry-After' => '120')), $retry['WHK003_Event_Id'], $retry['WHK004_Delivery_Id'], $retry['WHK003_Payload']);
    (new DeliveryWorker($repository, new WebhookHttpDispatcher($repository, $secrets, new Task10Validator(), $retryReceiver, $policy), 1))->runTenant($connection, 'task10-retry-worker');
    $retryState = task10One($connection, 'SELECT d.WHK004_Status, d.WHK004_Attempt_Count, TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(), d.WHK004_Next_Attempt_At) AS seconds_until_retry, a.WHK005_Result, a.WHK005_Http_Status FROM WHK004 d INNER JOIN WHK005 a ON a.WHK005_WHK004_Id = d.WHK004_Id WHERE d.WHK004_Id = ?', 'i', array((int) $retry['WHK004_Id']));
    task10Assert($retryState['WHK004_Status'] === 'R' && (int) $retryState['WHK004_Attempt_Count'] === 1 && (int) $retryState['seconds_until_retry'] >= 119 && (int) $retryState['seconds_until_retry'] <= 120 && $retryState['WHK005_Result'] === 'http_retryable' && (int) $retryState['WHK005_Http_Status'] === 429, 'transient failure records R, Retry-After next attempt and WHK005');
    task10Assert($retryReceiver->wasReceived() && $retryReceiver->hasValidHmac() && $retryReceiver->hasValidIdentifiers(), 'retry receiver validates the same request contract');

    // Definitive failure: controlled 404 must never be scheduled again.
    $dead = task10Publish($connection, $publisher, $companyId, 1003, 'dead');
    $deadReceiver = new Task10Receiver($secretVersion->getPlaintextSecret(), new HttpResult(404, null, 4, 0), $dead['WHK003_Event_Id'], $dead['WHK004_Delivery_Id'], $dead['WHK003_Payload']);
    (new DeliveryWorker($repository, new WebhookHttpDispatcher($repository, $secrets, new Task10Validator(), $deadReceiver, $policy), 1))->runTenant($connection, 'task10-dead-worker');
    $deadState = task10One($connection, 'SELECT d.WHK004_Status, d.WHK004_Next_Attempt_At, d.WHK004_Attempt_Count, a.WHK005_Result, a.WHK005_Http_Status FROM WHK004 d INNER JOIN WHK005 a ON a.WHK005_WHK004_Id = d.WHK004_Id WHERE d.WHK004_Id = ?', 'i', array((int) $dead['WHK004_Id']));
    task10Assert($deadState['WHK004_Status'] === 'X' && (int) $deadState['WHK004_Attempt_Count'] === 1 && $deadState['WHK005_Result'] === 'http_client_error' && (int) $deadState['WHK005_Http_Status'] === 404, 'definitive failure records WHK004 X and WHK005 without retry');
    task10Assert($deadReceiver->wasReceived() && $deadReceiver->hasValidHmac() && $deadReceiver->hasValidIdentifiers(), 'definitive receiver validates the request contract');

    // Remaining Task 08/09 classifications are exercised here through the same end-to-end path.
    $serverError = task10Publish($connection, $publisher, $companyId, 1004, 'server-error');
    $serverReceiver = new Task10Receiver($secretVersion->getPlaintextSecret(), new HttpResult(500, null, 3, 0), $serverError['WHK003_Event_Id'], $serverError['WHK004_Delivery_Id'], $serverError['WHK003_Payload']);
    (new DeliveryWorker($repository, new WebhookHttpDispatcher($repository, $secrets, new Task10Validator(), $serverReceiver, $policy), 1))->runTenant($connection, 'task10-server-worker');
    $serverState = task10One($connection, 'SELECT d.WHK004_Status, a.WHK005_Result FROM WHK004 d INNER JOIN WHK005 a ON a.WHK005_WHK004_Id = d.WHK004_Id WHERE d.WHK004_Id = ?', 'i', array((int) $serverError['WHK004_Id']));
    task10Assert($serverState['WHK004_Status'] === 'R' && $serverState['WHK005_Result'] === 'http_server_error' && $serverReceiver->hasValidHmac(), '5xx is retryable through the controlled receiver');

    $redirect = task10Publish($connection, $publisher, $companyId, 1005, 'redirect');
    $redirectReceiver = new Task10Receiver($secretVersion->getPlaintextSecret(), new HttpResult(302, null, 3, 0), $redirect['WHK003_Event_Id'], $redirect['WHK004_Delivery_Id'], $redirect['WHK003_Payload']);
    (new DeliveryWorker($repository, new WebhookHttpDispatcher($repository, $secrets, new Task10Validator(), $redirectReceiver, $policy), 1))->runTenant($connection, 'task10-redirect-worker');
    $redirectState = task10One($connection, 'SELECT d.WHK004_Status, a.WHK005_Result FROM WHK004 d INNER JOIN WHK005 a ON a.WHK005_WHK004_Id = d.WHK004_Id WHERE d.WHK004_Id = ?', 'i', array((int) $redirect['WHK004_Id']));
    task10Assert($redirectState['WHK004_Status'] === 'X' && $redirectState['WHK005_Result'] === 'http_redirect' && $redirectReceiver->hasValidHmac(), '3xx is definitive and redirects are not followed');

    $transport = task10Publish($connection, $publisher, $companyId, 1006, 'transport');
    $transportReceiver = new Task10Receiver($secretVersion->getPlaintextSecret(), new HttpResult(null, 'simulated_transport_failure', 3, 0), $transport['WHK003_Event_Id'], $transport['WHK004_Delivery_Id'], $transport['WHK003_Payload']);
    (new DeliveryWorker($repository, new WebhookHttpDispatcher($repository, $secrets, new Task10Validator(), $transportReceiver, $policy), 1))->runTenant($connection, 'task10-transport-worker');
    $transportState = task10One($connection, 'SELECT d.WHK004_Status, a.WHK005_Result, a.WHK005_Transport_Error FROM WHK004 d INNER JOIN WHK005 a ON a.WHK005_WHK004_Id = d.WHK004_Id WHERE d.WHK004_Id = ?', 'i', array((int) $transport['WHK004_Id']));
    task10Assert($transportState['WHK004_Status'] === 'R' && $transportState['WHK005_Result'] === 'transport_error' && $transportState['WHK005_Transport_Error'] === 'transport_error' && $transportReceiver->hasValidHmac(), 'transport failure is retryable with safe WHK005 metadata');

    $cancelled = task10Publish($connection, $publisher, $companyId, 1007, 'cancelled');
    $cancelReceiver = new Task10Receiver($secretVersion->getPlaintextSecret(), new HttpResult(204, null, 1, 0), $cancelled['WHK003_Event_Id'], $cancelled['WHK004_Delivery_Id'], $cancelled['WHK003_Payload']);
    $secrets->revokeVersion($connection, $secretVersion->getId());
    (new DeliveryWorker($repository, new WebhookHttpDispatcher($repository, $secrets, new Task10Validator(), $cancelReceiver, $policy), 1))->runTenant($connection, 'task10-cancel-worker');
    $cancelState = task10One($connection, 'SELECT WHK004_Status FROM WHK004 WHERE WHK004_Id = ?', 'i', array((int) $cancelled['WHK004_Id']));
    task10Assert($cancelState['WHK004_Status'] === 'C' && !$cancelReceiver->wasReceived(), 'unavailable secret cancels safely before HTTP');

    $columnsResult = $connection->query('SHOW COLUMNS FROM WHK005');
    $columns = array();
    while ($column = $columnsResult->fetch_assoc()) {
        $columns[] = $column['Field'];
    }
    $columnsResult->free();
    task10Assert(!array_intersect($columns, array('WHK005_Payload', 'WHK005_Secret', 'WHK005_Signature', 'WHK005_Response_Body')), 'WHK005 persists operational metadata only; test output emits no payload, secret or signature');

    echo "Task 10 synthetic end-to-end tests passed.\n";
} finally {
    task10Cleanup($connection, $companyId);
    $connection->close();
}
