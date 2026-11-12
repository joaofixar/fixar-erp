<?php

declare(strict_types=1);

use HardnessWebhooks\Catalog\Catalog;
use HardnessWebhooks\Exception\MasterKeyException;
use HardnessWebhooks\Exception\SecretCipherException;
use HardnessWebhooks\Exception\SecretUnavailableException;
use HardnessWebhooks\Publisher\WebhookPublisher;
use HardnessWebhooks\Secrets\SecretVersionService;
use HardnessWebhooks\Security\HmacSigner;
use HardnessWebhooks\Security\HmacVerifier;
use HardnessWebhooks\Security\MasterKeyProvider;
use HardnessWebhooks\Security\SecretCipher;
use HardnessWebhooks\Security\SecretGenerator;

require_once dirname(__DIR__) . '/autoload.php';

$configPath = (string) (getenv('WEBHOOKS_TEST_ERP_CONFIG') ?: '');
if ($configPath === '' || !is_file($configPath)) {
    fwrite(STDERR, "WEBHOOKS_TEST_ERP_CONFIG must identify the explicit DEV ERP configuration.\n");
    exit(2);
}
$confUsuario = array();
require $configPath;
$connection = new mysqli((string) ($confUsuario['dbHost'] ?? ''), (string) ($confUsuario['dbUser'] ?? ''), (string) ($confUsuario['dbPass'] ?? ''), (string) ($confUsuario['dbDatabase'] ?? ''));
if ($connection->connect_errno !== 0) { throw new RuntimeException('Could not connect to explicit DEV database.'); }
$connection->set_charset('utf8mb4');

function task06Assert(bool $condition, string $message): void { if (!$condition) { throw new RuntimeException('Assertion failed: ' . $message); } }
function task06Exec(mysqli $connection, string $sql, string $types = '', array $values = array()): mysqli_stmt {
    $statement = $connection->prepare($sql); if ($statement === false) { throw new RuntimeException('Database prepare failed.'); }
    if ($types !== '') { $params = array($types); foreach ($values as $index => $value) { $params[] = &$values[$index]; } call_user_func_array(array($statement, 'bind_param'), $params); }
    if (!$statement->execute()) { $statement->close(); throw new RuntimeException('Database execution failed.'); }
    return $statement;
}
function task06Cleanup(mysqli $connection, int $companyId): void {
    task06Exec($connection, 'DELETE d FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id = d.WHK004_WHK003_Id WHERE e.WHK003_C004_Id = ?', 'i', array($companyId))->close();
    task06Exec($connection, 'DELETE FROM WHK003 WHERE WHK003_C004_Id = ?', 'i', array($companyId))->close();
    task06Exec($connection, 'DELETE FROM WHK002 WHERE WHK002_C004_Id = ?', 'i', array($companyId))->close();
    task06Exec($connection, 'DELETE v FROM WHK007 v INNER JOIN WHK001 e ON e.WHK001_Id = v.WHK007_WHK001_Id WHERE e.WHK001_C004_Id = ?', 'i', array($companyId))->close();
    task06Exec($connection, 'DELETE FROM WHK001 WHERE WHK001_C004_Id = ?', 'i', array($companyId))->close();
}
function task06FetchOne(mysqli $connection, string $sql, string $types, array $values): array {
    $statement = task06Exec($connection, $sql, $types, $values); $result = $statement->get_result(); $row = $result->fetch_assoc(); $statement->close(); return is_array($row) ? $row : array();
}
function task06CorruptEnvelope(string $envelope, string $field): string {
    $decoded = json_decode($envelope, true);
    $value = (string) $decoded[$field];
    $decoded[$field] = ($value[0] === 'A' ? 'B' : 'A') . substr($value, 1);
    return (string) json_encode($decoded, JSON_UNESCAPED_SLASHES);
}

$companyId = 987654323;
$testKey = random_bytes(32); // Process-local DEV test key; never persisted or printed.
$cipher = new SecretCipher($testKey);
$service = new SecretVersionService($cipher);

try {
    task06Cleanup($connection, $companyId);

    $generatedOne = SecretGenerator::generate(); $generatedTwo = SecretGenerator::generate();
    task06Assert($generatedOne !== $generatedTwo && strlen($generatedOne) === 43 && preg_match('/^[A-Za-z0-9_-]{43}$/', $generatedOne) === 1, 'SecretGenerator creates distinct 32-byte Base64URL secrets');
    $encryptedOne = $cipher->encrypt($generatedOne); $encryptedTwo = $cipher->encrypt($generatedOne);
    task06Assert($cipher->decrypt($encryptedOne) === $generatedOne && $encryptedOne !== $encryptedTwo, 'AES-GCM roundtrip and nonce uniqueness');
    foreach (array(str_replace('ciphertext', 'ciphertexT', $encryptedOne), task06CorruptEnvelope($encryptedOne, 'tag'), task06CorruptEnvelope($encryptedOne, 'ciphertext')) as $invalid) {
        try { $cipher->decrypt((string) $invalid); task06Assert(false, 'invalid envelope must fail'); } catch (SecretCipherException $exception) { }
    }
    try { (new SecretCipher(random_bytes(32)))->decrypt($encryptedOne); task06Assert(false, 'wrong key must fail'); } catch (SecretCipherException $exception) { }
    try { (new SecretCipher(str_repeat('x', 31))); task06Assert(false, 'invalid key length must fail'); } catch (SecretCipherException $exception) { }
    $oldMaster = getenv('WEBHOOKS_MASTER_KEY'); putenv('WEBHOOKS_MASTER_KEY'); putenv('WEBHOOKS_ENVIRONMENT=production');
    try { MasterKeyProvider::fromEnvironment(); task06Assert(false, 'missing master key must fail'); } catch (MasterKeyException $exception) { }
    if (is_string($oldMaster) && $oldMaster !== '') { putenv('WEBHOOKS_MASTER_KEY=' . $oldMaster); } else { putenv('WEBHOOKS_MASTER_KEY'); }
    putenv('WEBHOOKS_ENVIRONMENT');

    $url = 'https://task06.example.test/hook'; $urlHash = hash('sha256', $url, true);
    task06Exec($connection, "INSERT INTO WHK001 (WHK001_C004_Id, WHK001_Nome, WHK001_URL, WHK001_URL_Hash, WHK001_Status) VALUES (?, 'Task 06 synthetic', ?, ?, 'A')", 'iss', array($companyId, $url, $urlHash))->close();
    $endpointId = (int) $connection->insert_id;
    $first = $service->createVersion($connection, $endpointId);
    $firstRow = task06FetchOne($connection, 'SELECT WHK007_Versao, WHK007_Secret_Criptografado, WHK007_Status FROM WHK007 WHERE WHK007_Id = ?', 'i', array($first->getId()));
    task06Assert($first->getVersion() === 1 && $firstRow['WHK007_Versao'] === 1 && $firstRow['WHK007_Status'] === 'A' && strpos($firstRow['WHK007_Secret_Criptografado'], $first->getPlaintextSecret()) === false, 'first WHK007 version is encrypted and active');
    $second = $service->createVersion($connection, $endpointId);
    $endpointRow = task06FetchOne($connection, 'SELECT WHK001_WHK007_Id_Atual FROM WHK001 WHERE WHK001_Id = ?', 'i', array($endpointId));
    $firstAgain = task06FetchOne($connection, 'SELECT WHK007_Versao, WHK007_Secret_Criptografado FROM WHK007 WHERE WHK007_Id = ?', 'i', array($first->getId()));
    task06Assert($second->getVersion() === 2 && (int) $endpointRow['WHK001_WHK007_Id_Atual'] === $second->getId() && $firstAgain['WHK007_Versao'] === 1 && $firstAgain['WHK007_Secret_Criptografado'] === $firstRow['WHK007_Secret_Criptografado'], 'WHK007 is immutable and endpoint advances atomically');
    try { task06Exec($connection, "INSERT INTO WHK007 (WHK007_WHK001_Id, WHK007_Versao, WHK007_Secret_Criptografado, WHK007_Status) VALUES (?, 2, '{}', 'A')", 'i', array($endpointId)); task06Assert(false, 'WHK007 version uniqueness must hold'); } catch (RuntimeException $exception) { }
    task06Assert($service->loadActiveSecret($connection, $second->getId()) === $second->getPlaintextSecret(), 'active secret can be decrypted on demand');

    task06Exec($connection, "INSERT INTO WHK002 (WHK002_C004_Id, WHK002_WHK001_Id, WHK002_Event_Type, WHK002_Status) VALUES (?, ?, 'financial_analysis.requested', 'A')", 'ii', array($companyId, $endpointId))->close();
    $publisher = new WebhookPublisher(new Catalog());
    $publication = $publisher->publish($connection, $companyId, 'financial_analysis.requested', array('order' => array('id' => 6006, 'total' => 1500.90), 'customer' => array('id' => 7006, 'name' => 'Task 06 customer', 'document' => '12345678000190')), array('aggregate_type' => 'order', 'aggregate_id' => 6006));
    $delivery = task06FetchOne($connection, 'SELECT d.WHK004_Id, d.WHK004_WHK007_Id, d.WHK004_Delivery_Id, e.WHK003_Event_Id, e.WHK003_Type, e.WHK003_Schema_Version, e.WHK003_Payload FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id = d.WHK004_WHK003_Id WHERE e.WHK003_Event_Id = ?', 's', array((string) $publication->getEventId()));
    task06Assert((int) $delivery['WHK004_WHK007_Id'] === $second->getId(), 'Publisher delivery references current encrypted WHK007');
    $timestamp = 1786924800; $rawBody = (string) $delivery['WHK003_Payload']; $loadedSecret = $service->loadActiveSecretForDelivery($connection, (int) $delivery['WHK004_Id']);
    $signature = HmacSigner::sign($loadedSecret, $timestamp, $rawBody);
    task06Assert($signature === HmacSigner::sign($loadedSecret, $timestamp, $rawBody) && $signature !== HmacSigner::sign($loadedSecret, $timestamp + 1, $rawBody) && $signature !== HmacSigner::sign($first->getPlaintextSecret(), $timestamp, $rawBody) && $signature !== HmacSigner::sign($loadedSecret, $timestamp, $rawBody . ' '), 'HMAC input changes alter signature');
    task06Assert(HmacVerifier::verify($loadedSecret, $timestamp, $rawBody, $signature, $timestamp + 300) && !HmacVerifier::verify($loadedSecret, $timestamp, $rawBody, 'v1=' . str_repeat('0', 64), $timestamp) && !HmacVerifier::verify($loadedSecret, $timestamp, $rawBody, $signature, $timestamp + 301), 'HMAC verifier uses valid signature and 5-minute replay window');
    $headers = HmacSigner::headers((string) $delivery['WHK003_Event_Id'], (string) $delivery['WHK004_Delivery_Id'], (string) $delivery['WHK003_Type'], (string) $delivery['WHK003_Schema_Version'], $timestamp, $signature);
    task06Assert(count($headers) === 8 && $headers['Hardness-Signature'] === $signature && $headers['Content-Type'] === 'application/json', 'approved HMAC headers are complete');
    $service->revokeVersion($connection, $second->getId());
    try { $service->loadActiveSecret($connection, $second->getId()); task06Assert(false, 'revoked secret must fail closed'); } catch (SecretUnavailableException $exception) { }

    echo "Task 06 synthetic tests passed.\n";
} finally {
    task06Cleanup($connection, $companyId);
    $connection->close();
}
