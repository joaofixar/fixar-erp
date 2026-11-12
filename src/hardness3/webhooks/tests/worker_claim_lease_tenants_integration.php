<?php

declare(strict_types=1);

use HardnessWebhooks\Delivery\DeliveryDispatcherInterface;
use HardnessWebhooks\Delivery\DeliveryRepository;
use HardnessWebhooks\Delivery\DeliveryWorker;
use HardnessWebhooks\Tenant\TenantResolver;

require_once dirname(__DIR__) . '/autoload.php';

function task07Assert(bool $condition, string $message): void { if (!$condition) { throw new RuntimeException('Assertion failed: ' . $message); } }
function task07Connect(string $configPath): mysqli { $confUsuario = array(); require $configPath; $connection = new mysqli((string) $confUsuario['dbHost'], (string) $confUsuario['dbUser'], (string) $confUsuario['dbPass'], (string) $confUsuario['dbDatabase']); if ($connection->connect_errno !== 0) { throw new RuntimeException('Could not connect to explicit DEV database.'); } $connection->set_charset('utf8mb4'); return $connection; }
function task07Exec(mysqli $connection, string $sql, string $types = '', array $values = array()): mysqli_stmt { $statement = $connection->prepare($sql); if ($statement === false) { throw new RuntimeException('Database prepare failed.'); } if ($types !== '') { $params = array($types); foreach ($values as $key => $value) { $params[] = &$values[$key]; } call_user_func_array(array($statement, 'bind_param'), $params); } if (!$statement->execute()) { $statement->close(); throw new RuntimeException('Database execution failed.'); } return $statement; }
function task07Cleanup(mysqli $connection, int $companyId): void { task07Exec($connection, 'DELETE d FROM WHK004 d INNER JOIN WHK003 e ON e.WHK003_Id = d.WHK004_WHK003_Id WHERE e.WHK003_C004_Id = ?', 'i', array($companyId))->close(); task07Exec($connection, 'DELETE FROM WHK003 WHERE WHK003_C004_Id = ?', 'i', array($companyId))->close(); }
function task07Delivery(mysqli $connection, int $companyId, string $status = 'P', ?string $nextAttempt = null, ?string $leaseUntil = null): int { $eventId = 'task07-' . bin2hex(random_bytes(12)); task07Exec($connection, "INSERT INTO WHK003 (WHK003_Event_Id, WHK003_C004_Id, WHK003_Type, WHK003_Schema_Version, WHK003_Aggregate_Type, WHK003_Aggregate_Id, WHK003_Payload, WHK003_Occurred_At) VALUES (?, ?, 'financial_analysis.requested', '1', 'order', ?, '{}', UTC_TIMESTAMP())", 'sis', array($eventId, $companyId, $eventId))->close(); $eventRecordId = (int) $connection->insert_id; $deliveryId = '00000000-0000-4000-8000-' . substr(bin2hex(random_bytes(8)), 0, 12); $nextAttempt = $nextAttempt ?: gmdate('Y-m-d H:i:s', time() - 1); task07Exec($connection, 'INSERT INTO WHK004 (WHK004_Delivery_Id, WHK004_C004_Id, WHK004_WHK003_Id, WHK004_WHK001_Id, WHK004_WHK002_Id, WHK004_WHK007_Id, WHK004_Destination_URL, WHK004_Status, WHK004_Next_Attempt_At, WHK004_Lease_Until) VALUES (?, ?, ?, 1, 1, 1, ?, ?, ?, ?)', 'siissss', array($deliveryId, $companyId, $eventRecordId, 'https://task07.example.test/hook', $status, $nextAttempt, $leaseUntil))->close(); return (int) $connection->insert_id; }
function task07ClaimChild(string $configPath, int $deliveryId, string $workerId): void { $connection = task07Connect($configPath); $claimed = (new DeliveryRepository(90))->tryClaim($connection, $deliveryId, $workerId); $connection->close(); echo $claimed ? '1' : '0'; }

final class Task07Dispatcher implements DeliveryDispatcherInterface { /** @var int */ public $count = 0; /** @param array<string,mixed> $delivery */ public function dispatch(mysqli $connection, string $workerId, array $delivery): void { ++$this->count; } }

if (($argv[1] ?? '') === '--claim') { task07ClaimChild((string) $argv[2], (int) $argv[3], (string) $argv[4]); exit(0); }

$configPath = (string) (getenv('WEBHOOKS_TEST_ERP_CONFIG') ?: '');
if ($configPath === '' || !is_file($configPath)) { fwrite(STDERR, "WEBHOOKS_TEST_ERP_CONFIG must identify the explicit DEV ERP configuration.\n"); exit(2); }
$companyId = 987654324;
$connection = task07Connect($configPath);
try {
    task07Cleanup($connection, $companyId);
    $repository = new DeliveryRepository(90);

    $single = task07Delivery($connection, $companyId); task07Assert($repository->tryClaim($connection, $single, 'worker-a'), 'first worker claims pending delivery'); task07Assert(!$repository->tryClaim($connection, $single, 'worker-b'), 'second worker cannot claim active lease');
    task07Exec($connection, "UPDATE WHK004 SET WHK004_Lease_Until = DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 SECOND) WHERE WHK004_Id = ?", 'i', array($single))->close(); task07Assert($repository->tryClaim($connection, $single, 'worker-b'), 'expired lease is recoverable'); task07Assert(!$repository->markDeliveredForTesting($connection, $single, 'worker-a') && $repository->markDeliveredForTesting($connection, $single, 'worker-b'), 'only current owner can complete');

    $future = task07Delivery($connection, $companyId, 'P', gmdate('Y-m-d H:i:s', time() + 3600)); task07Assert(!$repository->tryClaim($connection, $future, 'worker-a'), 'future pending delivery cannot be claimed');
    foreach (array('D', 'X', 'C') as $status) { $id = task07Delivery($connection, $companyId, $status); task07Assert(!$repository->tryClaim($connection, $id, 'worker-a'), 'final state ' . $status . ' cannot be claimed'); }
    $retry = task07Delivery($connection, $companyId, 'R'); task07Assert($repository->tryClaim($connection, $retry, 'worker-a'), 'eligible retry can be claimed');
    $leased = task07Delivery($connection, $companyId, 'L', gmdate('Y-m-d H:i:s', time() - 1), gmdate('Y-m-d H:i:s', time() + 3600)); task07Assert(!$repository->tryClaim($connection, $leased, 'worker-b'), 'unexpired lease cannot be recovered');

    $concurrent = task07Delivery($connection, $companyId); $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' --claim ' . escapeshellarg($configPath) . ' ' . (int) $concurrent . ' ' . escapeshellarg('worker-child'); $process = proc_open($command, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes); task07Assert(is_resource($process), 'concurrent child process starts'); $parentClaim = $repository->tryClaim($connection, $concurrent, 'worker-parent'); $childClaim = trim(stream_get_contents($pipes[1])); fclose($pipes[1]); fclose($pipes[2]); task07Assert(proc_close($process) === 0 && ((int) $parentClaim + (int) ($childClaim === '1')) === 1, 'two independent PHP processes produce exactly one claim');

    $batchIds = array(); for ($i = 0; $i < 4; ++$i) { $batchIds[] = task07Delivery($connection, $companyId); } $dispatcher = new Task07Dispatcher(); $summary = (new DeliveryWorker(new DeliveryRepository(90), $dispatcher, 2))->runTenant($connection, 'worker-batch'); task07Assert($summary['claimed'] === 2 && $dispatcher->count === 2, 'worker respects per-tenant batch limit');

    $directory = sys_get_temp_dir() . '/webhooks-task07-' . bin2hex(random_bytes(6)); mkdir($directory, 0700); $source = file_get_contents($configPath); task07Assert($source !== false, 'explicit DEV configuration can seed resolver fixture'); file_put_contents($directory . '/one-confUsuario.php', $source); file_put_contents($directory . '/two-confUsuario.php', $source); file_put_contents($directory . '/invalid-confUsuario.php', '<?php $confUsuario = array("dbDatabase" => "only");'); $resolver = new TenantResolver($directory); $tenants = $resolver->resolveAll(); task07Assert(count($tenants) === 1 && count($resolver->getErrors()) === 1, 'duplicate tenant is processed once and invalid configuration is isolated'); unlink($directory . '/one-confUsuario.php'); unlink($directory . '/two-confUsuario.php'); unlink($directory . '/invalid-confUsuario.php'); rmdir($directory);

    echo "Task 07 synthetic tests passed.\n";
} finally { task07Cleanup($connection, $companyId); $connection->close(); }

