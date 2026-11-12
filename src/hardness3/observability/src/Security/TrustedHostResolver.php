<?php
declare(strict_types=1);
namespace Hardness\Observability\Security;
use Hardness\Observability\Config\SiteTenantProvider;

final class TrustedHostResolver
{
    private $tenants;
    public function __construct(SiteTenantProvider $tenants) { $this->tenants = $tenants; }
    public function resolve(array $server): array
    {
        $tenant = $this->tenants->resolve((string) ($server['HTTP_HOST'] ?? ''));
        return array('host' => $tenant['tenant_key'], 'tenant_key' => $tenant['tenant_key'], 'tenant' => $tenant);
    }
}
