<?php

declare(strict_types=1);

use HardnessWebhooks\Core\Bootstrap;
use HardnessWebhooks\Delivery\DeliveryRepository;
use HardnessWebhooks\Delivery\DeliveryWorker;
use HardnessWebhooks\Delivery\DestinationValidator;
use HardnessWebhooks\Delivery\CurlHttpClient;
use HardnessWebhooks\Delivery\RetryPolicy;
use HardnessWebhooks\Delivery\WebhookHttpDispatcher;
use HardnessWebhooks\Delivery\WorkerId;
use HardnessWebhooks\Secrets\SecretVersionService;
use HardnessWebhooks\Security\MasterKeyProvider;
use HardnessWebhooks\Security\SecretCipher;
use HardnessWebhooks\Tenant\TenantResolver;

try {
    // This is deliberately the first operational check.  In particular, do
    // not resolve tenant files or open a database connection without the
    // explicitly enabled production transport.
    if ((string) getenv('WEBHOOKS_HTTP_ENABLED') !== '1') {
        throw new RuntimeException('Webhook delivery requires WEBHOOKS_HTTP_ENABLED=1.');
    }

    $startedAt = microtime(true);
    $bootstrap = require dirname(__DIR__) . '/bootstrap.php';

    if (!$bootstrap instanceof Bootstrap) {
        throw new RuntimeException('Webhooks bootstrap did not initialize the module.');
    }

    $leaseSeconds = (int) (getenv('WEBHOOKS_LEASE_SECONDS') ?: 90);
    // The worker remains parametrizable for controlled tests.  The operational
    // default is one delivery per tenant per short cycle.
    $batchSize = (int) (getenv('WEBHOOKS_BATCH_SIZE') ?: 1);
    $sitesDirectory = (string) (getenv('WEBHOOKS_SITES_DIRECTORY') ?: '/var/www/sites');
    if ($leaseSeconds < 1 || $batchSize < 1) {
        throw new RuntimeException('Webhook worker configuration is invalid.');
    }
    $workerId = WorkerId::generate();
    $resolver = new TenantResolver($sitesDirectory);
    $tenants = $resolver->resolveAll();
    foreach ($resolver->getErrors() as $label) {
        fwrite(STDERR, "Webhook worker skipped invalid tenant configuration: {$label}\n");
    }
    $repository = new DeliveryRepository($leaseSeconds);
    $connectTimeoutMs = (int) (getenv('WEBHOOKS_HTTP_CONNECT_TIMEOUT_MS') ?: 5000);
    $timeoutMs = (int) (getenv('WEBHOOKS_HTTP_TIMEOUT_MS') ?: 15000);
    $maxResponseBytes = (int) (getenv('WEBHOOKS_HTTP_MAX_RESPONSE_BYTES') ?: 65536);
    if ($timeoutMs >= ($leaseSeconds * 1000)) {
        throw new RuntimeException('Webhook HTTP total timeout must remain below the lease duration.');
    }
    $secrets = new SecretVersionService(new SecretCipher(MasterKeyProvider::fromEnvironment()));
    $jitter = (float) (getenv('WEBHOOKS_RETRY_JITTER_FRACTION') !== false ? getenv('WEBHOOKS_RETRY_JITTER_FRACTION') : '0.10');
    $retryAfterCap = (int) (getenv('WEBHOOKS_RETRY_AFTER_CAP_SECONDS') ?: 21600);
    $dispatcher = new WebhookHttpDispatcher($repository, $secrets, new DestinationValidator(), new CurlHttpClient($connectTimeoutMs, $timeoutMs, $maxResponseBytes), new RetryPolicy(array(60, 300, 900, 3600, 21600), $jitter, $retryAfterCap));
    $worker = new DeliveryWorker($repository, $dispatcher, $batchSize);
    $failures = 0;
    $totals = array('candidates' => 0, 'claimed' => 0, 'dispatched' => 0, 'recovered' => 0);
    foreach ($tenants as $tenant) {
        $connection = @new mysqli($tenant['host'], $tenant['user'], $tenant['password'], $tenant['database']);
        if ($connection->connect_errno !== 0) {
            ++$failures;
            fwrite(STDERR, "Webhook worker could not connect tenant database: {$tenant['label']}\n");
            continue;
        }
        try {
            $connection->set_charset('utf8mb4');
            $schema = $connection->query("SHOW TABLES LIKE 'WHK00_'");
            if ($schema === false || $schema->num_rows !== 7) {
                if ($schema instanceof mysqli_result) { $schema->free(); }
                continue;
            }
            $schema->free();
            $summary = $worker->runTenant($connection, $workerId);
            foreach ($totals as $metric => $value) { $totals[$metric] += $summary[$metric]; }
            if ($summary['candidates'] > 0 || $summary['claimed'] > 0 || $summary['dispatched'] > 0 || $summary['recovered'] > 0) {
                fwrite(STDOUT, "Webhook worker tenant={$tenant['label']} candidates={$summary['candidates']} claimed={$summary['claimed']} dispatched={$summary['dispatched']} recovered_leases={$summary['recovered']}\n");
            }
        } catch (Throwable $tenantException) {
            ++$failures;
            fwrite(STDERR, "Webhook worker failed tenant database: {$tenant['label']}\n");
        } finally { $connection->close(); }
    }
    $exitCode = $failures > 0 ? 2 : 0;
    $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
    if ($failures > 0 || $totals['candidates'] > 0 || $totals['claimed'] > 0 || $totals['dispatched'] > 0 || $totals['recovered'] > 0) {
        fwrite(STDOUT, "Webhooks worker cycle duration_ms={$durationMs} exit_code={$exitCode} tenant_failures={$failures} candidates={$totals['candidates']} claims={$totals['claimed']} dispatched={$totals['dispatched']} recovered_leases={$totals['recovered']}\n");
    }
    exit($exitCode);
} catch (Throwable $exception) {
    fwrite(STDERR, "Webhooks module initialization failed: " . $exception->getMessage() . "\n");
    exit(1);
}
