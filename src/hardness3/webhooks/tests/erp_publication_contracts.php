<?php

declare(strict_types=1);

use HardnessWebhooks\Catalog\Catalog;
use HardnessWebhooks\Payload\FinancialAnalysisRequestedPayloadBuilder;
use HardnessWebhooks\Publisher\WebhookPublisher;
use HardnessWebhooks\Secrets\SecretVersionService;
use HardnessWebhooks\Security\SecretCipher;

require_once dirname(__DIR__) . '/autoload.php';

function task11Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

function task11Exec(mysqli $connection, string $sql, string $types = '', array $values = array()): mysqli_stmt
{
    $statement = $connection->prepare($sql);
    if ($statement === false) {
        throw new RuntimeException('Database prepare failed: ' . $connection->error);
    }
    if ($types !== '') {
        $parameters = array($types);
        foreach ($values as $index => $value) {
            $parameters[] = &$values[$index];
        }
        call_user_func_array(array($statement, 'bind_param'), $parameters);
    }
    if (!$statement->execute()) {
        $error = $statement->error;
        $statement->close();
        throw new RuntimeException('Database execution failed: ' . $error);
    }
    return $statement;
}

function task11One(mysqli $connection, string $sql, string $types, array $values): array
{
    $statement = task11Exec($connection, $sql, $types, $values);
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();
    return is_array($row) ? $row : array();
}

function task11Count(mysqli $connection, string $sql, string $types, array $values): int
{
    $row = task11One($connection, $sql, $types, $values);
    return isset($row['total']) ? (int) $row['total'] : 0;
}

function task11Cleanup(mysqli $connection, int $companyId, array $orderIds): void
{
    if ($orderIds !== array()) {
        $ids = implode(',', array_map('intval', $orderIds));
        $connection->query("DELETE FROM T178 WHERE T178_T005_Id IN ({$ids})");
        $connection->query("DELETE FROM T005 WHERE T005_Id IN ({$ids})");
    }
    task11Exec($connection, 'DELETE a FROM WHK005 a INNER JOIN WHK004 d ON d.WHK004_Id = a.WHK005_WHK004_Id WHERE d.WHK004_C004_Id = ?', 'i', array($companyId))->close();
    task11Exec($connection, 'DELETE FROM WHK004 WHERE WHK004_C004_Id = ?', 'i', array($companyId))->close();
    task11Exec($connection, 'DELETE FROM WHK003 WHERE WHK003_C004_Id = ?', 'i', array($companyId))->close();
    task11Exec($connection, 'DELETE FROM WHK002 WHERE WHK002_C004_Id = ?', 'i', array($companyId))->close();
    task11Exec($connection, 'DELETE v FROM WHK007 v INNER JOIN WHK001 e ON e.WHK001_Id = v.WHK007_WHK001_Id WHERE e.WHK001_C004_Id = ?', 'i', array($companyId))->close();
    task11Exec($connection, 'DELETE FROM WHK001 WHERE WHK001_C004_Id = ?', 'i', array($companyId))->close();
}

function task11CreateOrder(mysqli $connection, int $companyId, int $customerId): int
{
    task11Exec($connection, "INSERT INTO T005 (T005_C004_Id, T005_D024_Id, T005_C007_Id_Embalagem, T005_Flag_Status, T005_Nome_Status, T005_Motivo_Pedido_Liberacao) VALUES (?, ?, 0, 0, 'PENDENTE', 'Task 11 synthetic')", 'ii', array($companyId, $customerId))->close();
    return (int) $connection->insert_id;
}

/** Mirrors the approved status-6 unit of work; it never performs HTTP. */
function task11Transition(mysqli $connection, WebhookPublisher $publisher, int $companyId, int $orderId, int $customerId, bool $failBeforePublish = false): int
{
    if (!$connection->begin_transaction()) {
        throw new RuntimeException('Could not begin Task 11 synthetic transaction.');
    }
    try {
        $update = task11Exec($connection, "UPDATE T005 SET T005_Flag_Status = 6, T005_Nome_Status = 'ANALISE DE CREDITO' WHERE T005_Id = ? AND T005_Flag_Status = 0", 'i', array($orderId));
        $affected = $update->affected_rows;
        $update->close();
        if ($affected !== 1) {
            throw new RuntimeException('The order did not transition to status 6.');
        }
        task11Exec($connection, "INSERT INTO T178 (T178_T005_Id, T178_C007_Id, T178_Data, T178_Descricao, T178_Observacao) VALUES (?, 0, CURRENT_TIMESTAMP(), 'Task 11 analysis requested', '')", 'i', array($orderId))->close();
        if ($failBeforePublish) {
            throw new RuntimeException('Controlled publisher failure.');
        }
        $result = $publisher->publish($connection, $companyId, 'financial_analysis.requested', array(
            'order' => array('id' => $orderId, 'total' => 1500.90),
            'customer' => array('id' => $customerId, 'name' => 'Task 11 customer', 'document' => '12345678000190'),
        ), array('aggregate_type' => 'order', 'aggregate_id' => $orderId));
        if (!$connection->commit()) {
            throw new RuntimeException('Could not commit Task 11 synthetic transaction.');
        }
        return $result->getDeliveryCount();
    } catch (Throwable $exception) {
        $connection->rollback();
        throw $exception;
    }
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

$companyId = 987654328;
$customerId = 987654328;
$orders = array();
$publisher = new WebhookPublisher(new Catalog());
$secrets = new SecretVersionService(new SecretCipher(random_bytes(32)));

try {
    $catalog = new Catalog();
    $event = $catalog->get('financial_analysis.requested');
    task11Assert($event['visibility'] === Catalog::VISIBILITY_PUBLIC && $event['schema_version'] === '1' && $event['aggregate_type'] === 'order', 'requested event is PUBLIC schema 1 for order aggregate');
    $catalog->validatePayload('financial_analysis.requested', array('order' => array('id' => 1, 'total' => 1500.90), 'customer' => array('id' => 2, 'name' => 'Task 11 customer', 'document' => '12345678000190')));
    task11Assert($catalog->publicationMetadata('financial_analysis.requested', array('order' => array('id' => 1, 'total' => 1500.90), 'customer' => array('id' => 2, 'name' => 'Task 11 customer', 'document' => '12345678000190'))) === array('aggregate_type' => 'order', 'aggregate_id' => 1), 'catalog derives requested aggregate metadata from its payload contract');
    try {
        $catalog->validatePayload('financial_analysis.requested', array('order' => array('id' => 1, 'total' => 1500.90)));
        task11Assert(false, 'requested event requires customer');
    } catch (InvalidArgumentException $exception) {
    }

    $ven001 = file_get_contents(dirname(__DIR__, 2) . '/bibliotecas/classes/VEN001.php');
    $ven002 = file_get_contents(dirname(__DIR__, 2) . '/bibliotecas/classes/VEN002.php');
    task11Assert(strpos($ven001, "T005_Flag_Status=6") !== false && strpos($ven001, "financial_analysis.requested") !== false, 'VEN001 publishes at the status-6 transition');
    $publicationCall = "Webhook::publishForCompany((int) \$mT005['T005_C004_Id'], 'financial_analysis.requested', (int) \$T005_Id);";
    task11Assert(strpos($ven001, $publicationCall) !== false && strpos($ven002, $publicationCall) !== false, 'VEN001 and VEN002 publish only the order identity');
    task11Assert(strpos($ven002, "\$g['conexaoBanco']->begin_transaction()") === false && strpos($ven002, "\$g['conexaoBanco']->commit()") === false && strpos($ven002, "\$g['conexaoBanco']->rollback()") === false, 'VEN002 preserves legacy transaction ownership while publishing the requested event');
    task11Assert(strpos($ven001 . $ven002, 'WebhookHttpDispatcher') === false, 'business flows do not dispatch HTTP');

    task11Cleanup($connection, $companyId, $orders);
    $url = 'https://task11.example.test/hook';
    task11Exec($connection, "INSERT INTO WHK001 (WHK001_C004_Id, WHK001_Nome, WHK001_URL, WHK001_URL_Hash, WHK001_Status) VALUES (?, 'Task 11 synthetic', ?, ?, 'A')", 'iss', array($companyId, $url, hash('sha256', $url, true)))->close();
    $endpointId = (int) $connection->insert_id;
    $secrets->createVersion($connection, $endpointId);
    task11Exec($connection, "INSERT INTO WHK002 (WHK002_C004_Id, WHK002_WHK001_Id, WHK002_Event_Type, WHK002_Status) VALUES (?, ?, 'financial_analysis.requested', 'A')", 'ii', array($companyId, $endpointId))->close();

    $cpfCustomer = task11One($connection, "SELECT D024_Id, D024_Nome_Empresa, D024_Cpf FROM D024 WHERE D024_Flag_Pessoa_Fisica_Juridica = 'F' AND D024_Cpf IS NOT NULL AND D024_Cpf <> '' LIMIT 1", '', array());
    $cnpjCustomer = task11One($connection, "SELECT D024_Id, D024_Nome_Empresa, D024_Cnpj FROM D024 WHERE D024_Flag_Pessoa_Fisica_Juridica <> 'F' AND D024_Cnpj IS NOT NULL AND D024_Cnpj <> '' LIMIT 1", '', array());
    task11Assert($cpfCustomer !== array() && $cnpjCustomer !== array(), 'DEV fixture has customers for CPF and CNPJ payload coverage');

    $cpfOrder = task11CreateOrder($connection, $companyId, (int) $cpfCustomer['D024_Id']);
    $cnpjOrder = task11CreateOrder($connection, $companyId, (int) $cnpjCustomer['D024_Id']);
    $orders[] = $cpfOrder;
    $orders[] = $cnpjOrder;
    task11Exec($connection, 'UPDATE T005 SET T005_Valor_Total = 1500.90 WHERE T005_Id IN (?, ?)', 'ii', array($cpfOrder, $cnpjOrder))->close();

    $builder = new FinancialAnalysisRequestedPayloadBuilder();
    $cpfPayload = $builder->build($connection, $companyId, $cpfOrder);
    $cnpjPayload = $builder->build($connection, $companyId, $cnpjOrder);
    task11Assert($cpfPayload['order']['id'] === $cpfOrder && $cpfPayload['order']['total'] === 1500.90 && $cpfPayload['customer']['id'] === (int) $cpfCustomer['D024_Id'] && $cpfPayload['customer']['name'] === (string) $cpfCustomer['D024_Nome_Empresa'] && $cpfPayload['customer']['document'] === preg_replace('/\\D+/', '', (string) $cpfCustomer['D024_Cpf']), 'builder preserves the CPF public payload contract');
    task11Assert($cnpjPayload['order']['id'] === $cnpjOrder && $cnpjPayload['order']['total'] === 1500.90 && $cnpjPayload['customer']['id'] === (int) $cnpjCustomer['D024_Id'] && $cnpjPayload['customer']['name'] === (string) $cnpjCustomer['D024_Nome_Empresa'] && $cnpjPayload['customer']['document'] === preg_replace('/\\D+/', '', (string) $cnpjCustomer['D024_Cnpj']), 'builder preserves the CNPJ public payload contract');
    $catalog->validatePayload('financial_analysis.requested', $cpfPayload);
    $catalog->validatePayload('financial_analysis.requested', $cnpjPayload);
    try { $builder->build($connection, $companyId + 1, $cpfOrder); task11Assert(false, 'builder rejects an order from another company'); } catch (RuntimeException $exception) { }
    try { $builder->build($connection, $companyId, PHP_INT_MAX); task11Assert(false, 'builder rejects a nonexistent order'); } catch (RuntimeException $exception) { }

    require_once dirname(__DIR__, 2) . '/bibliotecas/classes/Webhook.php';
    $hadGlobalContext = array_key_exists('g', $GLOBALS);
    $previousGlobalContext = $GLOBALS['g'] ?? null;
    $facadeTransactionStarted = false;
    try {
        $GLOBALS['g'] = array('conexaoBanco' => $connection);
        if (!$connection->begin_transaction()) {
            throw new RuntimeException('Could not begin facade transaction.');
        }
        $facadeTransactionStarted = true;
        $facadePublication = \hardness\Webhook::publishForCompany($companyId, 'financial_analysis.requested', $cnpjOrder);
        task11Assert($facadePublication->getDeliveryCount() === 1, 'facade resolves the public payload through the current transaction connection');
        if (!$connection->commit()) { throw new RuntimeException('Could not commit facade transaction.'); }
        $facadeTransactionStarted = false;
        $snapshot = task11One($connection, 'SELECT WHK003_Payload FROM WHK003 WHERE WHK003_Event_Id = ?', 's', array((string) $facadePublication->getEventId()));
        task11Exec($connection, 'UPDATE T005 SET T005_Valor_Total = 1.00 WHERE T005_Id = ?', 'i', array($cnpjOrder))->close();
        task11Assert(json_decode((string) $snapshot['WHK003_Payload'], true) === $cnpjPayload, 'WHK003 preserves the payload snapshot after the order changes');
    } finally {
        if ($facadeTransactionStarted) {
            $connection->rollback();
        }
        if ($hadGlobalContext) {
            $GLOBALS['g'] = $previousGlobalContext;
        } else {
            unset($GLOBALS['g']);
        }
    }

    // Equivalent to either approved production point: status 0 -> 6, T178 and outbox commit together.
    $order = task11CreateOrder($connection, $companyId, $customerId);
    $orders[] = $order;
    task11Assert(task11Transition($connection, $publisher, $companyId, $order, $customerId) === 1, 'eligible subscription receives one delivery');
    $state = task11One($connection, 'SELECT T005_Flag_Status FROM T005 WHERE T005_Id = ?', 'i', array($order));
    task11Assert((int) $state['T005_Flag_Status'] === 6, 'order persisted in status 6');
    task11Assert(task11Count($connection, 'SELECT COUNT(*) AS total FROM T178 WHERE T178_T005_Id = ?', 'i', array($order)) === 1, 'status transition persists one history entry');
    $eventRow = task11One($connection, 'SELECT WHK003_C004_Id, WHK003_Type, WHK003_Schema_Version, WHK003_Aggregate_Type, WHK003_Aggregate_Id, WHK003_Payload FROM WHK003 WHERE WHK003_C004_Id = ? AND WHK003_Aggregate_Id = ?', 'is', array($companyId, (string) $order));
    task11Assert((int) $eventRow['WHK003_C004_Id'] === $companyId && $eventRow['WHK003_Type'] === 'financial_analysis.requested' && $eventRow['WHK003_Schema_Version'] === '1' && $eventRow['WHK003_Aggregate_Type'] === 'order' && (int) $eventRow['WHK003_Aggregate_Id'] === $order && $eventRow['WHK003_Payload'] === '{"order":{"id":' . $order . ',"total":1500.9},"customer":{"id":' . $customerId . ',"name":"Task 11 customer","document":"12345678000190"}}', 'both flows share the approved public contract');
    task11Assert(task11Count($connection, 'SELECT COUNT(*) AS total FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id = d.WHK004_WHK003_Id WHERE e.WHK003_Aggregate_Id = ?', 's', array((string) $order)) === 1, 'one outbox delivery is created without HTTP');

    // A second attempt cannot re-enter status 6 or publish another domain event.
    try {
        task11Transition($connection, $publisher, $companyId, $order, $customerId);
        task11Assert(false, 'second transition must be rejected');
    } catch (RuntimeException $exception) {
    }
    task11Assert(task11Count($connection, 'SELECT COUNT(*) AS total FROM WHK003 WHERE WHK003_C004_Id = ? AND WHK003_Aggregate_Id = ?', 'is', array($companyId, (string) $order)) === 1, 'conditional transition prevents duplicate event');

    // No subscription does not block the domain transition and leaves no outbox.
    task11Exec($connection, 'UPDATE WHK002 SET WHK002_Status = \'I\' WHERE WHK002_C004_Id = ?', 'i', array($companyId))->close();
    $withoutSubscription = task11CreateOrder($connection, $companyId, $customerId);
    $orders[] = $withoutSubscription;
    task11Assert(task11Transition($connection, $publisher, $companyId, $withoutSubscription, $customerId) === 0, 'no subscription returns normally');
    task11Assert((int) task11One($connection, 'SELECT T005_Flag_Status FROM T005 WHERE T005_Id = ?', 'i', array($withoutSubscription))['T005_Flag_Status'] === 6 && task11Count($connection, 'SELECT COUNT(*) AS total FROM WHK003 WHERE WHK003_C004_Id = ? AND WHK003_Aggregate_Id = ?', 'is', array($companyId, (string) $withoutSubscription)) === 0, 'no subscription commits status/history without outbox');

    // Any publisher-side failure rolls back the status and history in the same unit.
    $rollbackOrder = task11CreateOrder($connection, $companyId, $customerId);
    $orders[] = $rollbackOrder;
    try {
        task11Transition($connection, $publisher, $companyId, $rollbackOrder, $customerId, true);
        task11Assert(false, 'controlled publisher failure must abort transaction');
    } catch (RuntimeException $exception) {
    }
    task11Assert((int) task11One($connection, 'SELECT T005_Flag_Status FROM T005 WHERE T005_Id = ?', 'i', array($rollbackOrder))['T005_Flag_Status'] === 0 && task11Count($connection, 'SELECT COUNT(*) AS total FROM T178 WHERE T178_T005_Id = ?', 'i', array($rollbackOrder)) === 0 && task11Count($connection, 'SELECT COUNT(*) AS total FROM WHK003 WHERE WHK003_C004_Id = ? AND WHK003_Aggregate_Id = ?', 'is', array($companyId, (string) $rollbackOrder)) === 0, 'rollback leaves no partial status, history or outbox');

    echo "Task 11 integration tests passed.\n";
} finally {
    task11Cleanup($connection, $companyId, $orders);
    $connection->close();
}


/* Static guards for the final ERP publication contract and schema. */

function task22(bool $value, string $message): void
{
    if (!$value) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

$ven001 = file_get_contents(dirname(__DIR__, 2) . '/bibliotecas/classes/VEN001.php');
$ven002 = file_get_contents(dirname(__DIR__, 2) . '/bibliotecas/classes/VEN002.php');
$catalog = file_get_contents(dirname(__DIR__) . '/src/Catalog/Catalog.php');
$schema = file_get_contents(dirname(__DIR__, 2) . '/scripts/atualizacaoBase.php');

task22($ven001 !== false && $ven002 !== false && $catalog !== false && $schema !== false, 'Task 22 sources are present');
task22(strpos((string) $ven002, "Webhook::publishForCompany((int) \$mT005['T005_C004_Id'], 'financial_analysis.requested', (int) \$T005_Id);") !== false, 'VEN002 publishes the requested event by aggregate ID');
task22(strpos((string) $ven002, "\$g['conexaoBanco']->begin_transaction()") === false && strpos((string) $ven002, "\$g['conexaoBanco']->commit()") === false && strpos((string) $ven002, "\$g['conexaoBanco']->rollback()") === false, 'VEN002 preserves its legacy transaction ownership');
task22(strpos((string) $ven001 . $ven002, 'Erro ao publicar entrada em análise de crédito:') === false && strpos((string) $ven001 . $ven002, 'error_log(') === false, 'webhook publication failure does not interrupt ERP or create per-event production logs');
task22(strpos((string) $ven002, "T005_Flag_Status='{\$statusAnteriorAnalise}'") === false, 'VEN002 preserves its original status update');
task22(strpos((string) $catalog, 'financial_analysis.approved') === false && strpos((string) $catalog, 'financial_analysis.requested') !== false, 'only the publishable requested event remains in the production catalog');
task22(strpos((string) $schema, 'LONGTEXT NULL DEFAULT NULL') === false, 'WHK LONGTEXT definitions are compatible with MySQL 5.6');

echo "Task 22 static transaction/catalog/schema tests passed.\n";
