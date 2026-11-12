<?php

declare(strict_types=1);

function task12Assert(bool $condition, string $message): void
{
    if (!$condition) { throw new RuntimeException('Task 12 assertion failed: ' . $message); }
}

/** @return array{0:int,1:string} */
function task12RunWorker(?string $httpEnabled): array
{
    $environment = array(
        'PATH' => (string) getenv('PATH'),
        // If the guard regresses, this makes tenant discovery fail visibly.
        'WEBHOOKS_SITES_DIRECTORY' => '/webhooks-task12-must-not-be-read',
    );
    if ($httpEnabled !== null) { $environment['WEBHOOKS_HTTP_ENABLED'] = $httpEnabled; }
    $descriptor = array(1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
    $process = proc_open(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(dirname(__DIR__) . '/cli/worker.php'), $descriptor, $pipes, dirname(__DIR__), $environment);
    task12Assert(is_resource($process), 'worker subprocess starts');
    $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    return array(proc_close($process), $output);
}

try {
    foreach (array(null, '0', 'yes') as $httpEnabled) {
        list($exitCode, $output) = task12RunWorker($httpEnabled);
        task12Assert($exitCode !== 0, 'disabled HTTP returns a non-zero exit code');
        task12Assert(strpos($output, 'WEBHOOKS_HTTP_ENABLED=1') !== false, 'disabled HTTP has a visible safe error');
        task12Assert(strpos($output, 'tenant configuration directory is unavailable') === false, 'HTTP guard runs before tenant discovery');
    }

    $worker = file_get_contents(dirname(__DIR__) . '/cli/worker.php');
    task12Assert($worker !== false && strpos($worker, "getenv('WEBHOOKS_BATCH_SIZE') ?: 1") !== false, 'delivery CLI defaults to one delivery per tenant');
    task12Assert(strpos((string) $worker, 'NoopDeliveryDispatcher') === false, 'production delivery CLI has no noop fallback');

    $deliveryService = file_get_contents(dirname(__DIR__) . '/systemd/hardness-webhooks-delivery.service');
    task12Assert($deliveryService !== false && strpos($deliveryService, 'Type=simple') !== false && strpos($deliveryService, 'Environment=WEBHOOKS_BATCH_SIZE=20') !== false && strpos($deliveryService, 'while :; do') !== false && strpos($deliveryService, 'WantedBy=multi-user.target') !== false, 'delivery service is continuous and pins the operational batch per tenant');

    $retention = file_get_contents(dirname(__DIR__) . '/cli/retention.php');
    task12Assert($retention !== false && strpos($retention, 'DeliveryWorker') === false && strpos($retention, 'WEBHOOKS_RETENTION_LIMIT') !== false, 'retention is separate and bounded');
    fwrite(STDOUT, "Task 12 operational tests passed.\n");
    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}
