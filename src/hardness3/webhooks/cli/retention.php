<?php

declare(strict_types=1);

use HardnessWebhooks\Core\Bootstrap;
use HardnessWebhooks\Delivery\DeliveryRetentionService;
use HardnessWebhooks\Tenant\TenantResolver;

try {
    $bootstrap = require dirname(__DIR__) . '/bootstrap.php';
    if (!$bootstrap instanceof Bootstrap) {
        throw new RuntimeException('Webhooks bootstrap did not initialize the module.');
    }

    $limit = (int) (getenv('WEBHOOKS_RETENTION_LIMIT') ?: 100);
    if ($limit < 1 || $limit > 1000) {
        throw new RuntimeException('Webhook retention limit must be between 1 and 1000.');
    }
    $startedAt = microtime(true);
    $resolver = new TenantResolver((string) (getenv('WEBHOOKS_SITES_DIRECTORY') ?: '/var/www/sites'));
    $tenants = $resolver->resolveAll();
    $failures = count($resolver->getErrors());
    foreach ($resolver->getErrors() as $label) {
        fwrite(STDERR, "Webhook retention skipped invalid tenant configuration: {$label}\n");
    }
    $service = new DeliveryRetentionService();
    $totals = array('attempts' => 0, 'deliveries' => 0, 'events' => 0);
    foreach ($tenants as $tenant) {
        $connection = @new mysqli($tenant['host'], $tenant['user'], $tenant['password'], $tenant['database']);
        if ($connection->connect_errno !== 0) {
            ++$failures;
            fwrite(STDERR, "Webhook retention could not connect tenant database: {$tenant['label']}\n");
            continue;
        }
        try {
            $connection->set_charset('utf8mb4');
            $summary = $service->purge($connection, $limit);
            foreach ($totals as $metric => $value) { $totals[$metric] += $summary[$metric]; }
            fwrite(STDOUT, "Webhook retention tenant={$tenant['label']} attempts={$summary['attempts']} deliveries={$summary['deliveries']} events={$summary['events']}\n");
        } catch (Throwable $exception) {
            ++$failures;
            fwrite(STDERR, "Webhook retention failed tenant database: {$tenant['label']}\n");
        } finally { $connection->close(); }
    }
    $exitCode = $failures > 0 ? 2 : 0;
    $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
    fwrite(STDOUT, "Webhooks retention duration_ms={$durationMs} exit_code={$exitCode} tenant_failures={$failures} attempts={$totals['attempts']} deliveries={$totals['deliveries']} events={$totals['events']}\n");
    exit($exitCode);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Webhooks retention initialization failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
