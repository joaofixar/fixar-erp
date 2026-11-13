<?php

declare(strict_types=1);

use HardnessWebhooks\Core\Bootstrap;
use HardnessWebhooks\Tenant\TenantResolver;

try {
    $bootstrap = require dirname(__DIR__) . '/bootstrap.php';
    if (!$bootstrap instanceof Bootstrap) {
        throw new RuntimeException('Webhooks bootstrap did not initialize the module.');
    }
    $resolver = new TenantResolver((string) (getenv('WEBHOOKS_SITES_DIRECTORY') ?: '/var/www/sites'));
    $tenants = $resolver->resolveAll();
    $failures = count($resolver->getErrors());
    foreach ($resolver->getErrors() as $label) {
        fwrite(STDERR, "Webhook diagnostics skipped invalid tenant configuration: {$label}\n");
    }
    foreach ($tenants as $tenant) {
        $connection = @new mysqli($tenant['host'], $tenant['user'], $tenant['password'], $tenant['database']);
        if ($connection->connect_errno !== 0) {
            ++$failures;
            fwrite(STDERR, "Webhook diagnostics could not connect tenant database: {$tenant['label']}\n");
            continue;
        }
        try {
            $connection->set_charset('utf8mb4');
            $result = $connection->query("SELECT SUM(WHK004_Status IN ('P','R')) AS backlog_pr, COALESCE(MAX(CASE WHEN WHK004_Status IN ('P','R') AND WHK004_Next_Attempt_At <= UTC_TIMESTAMP() THEN TIMESTAMPDIFF(SECOND, WHK004_Data_Criacao, UTC_TIMESTAMP()) ELSE 0 END), 0) AS oldest_eligible_age_seconds, SUM(WHK004_Status = 'L' AND WHK004_Lease_Until < UTC_TIMESTAMP()) AS expired_leases, SUM(WHK004_Status = 'R') AS retry, SUM(WHK004_Status = 'X') AS dead, SUM(WHK004_Status = 'C') AS cancelled FROM WHK004");
            if ($result === false || !($row = $result->fetch_assoc())) { throw new RuntimeException('Could not query webhook diagnostics.'); }
            fwrite(STDOUT, "Webhook diagnostics tenant={$tenant['label']} backlog_pr=" . (int) $row['backlog_pr'] . " oldest_eligible_age_seconds=" . (int) $row['oldest_eligible_age_seconds'] . " expired_leases=" . (int) $row['expired_leases'] . " retry=" . (int) $row['retry'] . " dead=" . (int) $row['dead'] . " cancelled=" . (int) $row['cancelled'] . "\n");
        } catch (Throwable $exception) {
            ++$failures;
            fwrite(STDERR, "Webhook diagnostics failed tenant database: {$tenant['label']}\n");
        } finally { $connection->close(); }
    }
    exit($failures > 0 ? 2 : 0);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Webhooks diagnostics initialization failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
