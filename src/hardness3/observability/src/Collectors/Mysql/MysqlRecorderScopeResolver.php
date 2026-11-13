<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

use Hardness\Observability\Config\SiteTenantProvider;
use Hardness\Observability\Context\ObservationContextProvider;

/** Resolve schemas somente a partir da sessão/host e do inventário confiável do backend. */
final class MysqlRecorderScopeResolver
{
    private $contexts;
    private $tenants;

    public function __construct(ObservationContextProvider $contexts, SiteTenantProvider $tenants)
    {
        $this->contexts = $contexts; $this->tenants = $tenants;
    }

    public function resolve(): array
    {
        $context = $this->contexts->current()->toArray(); $schemas = array();
        if (($context['role'] ?? '') === 'hardness_internal') {
            $tenants = $this->tenants->all();
        } else {
            $resolved = $this->tenants->resolve((string) ($_SERVER['HTTP_HOST'] ?? ''));
            $targetSchema = (string) ($resolved['database']['database'] ?? ''); $servers = array();
            foreach ($this->tenants->all() as $knownTenant) {
                if ((string) ($knownTenant['database']['database'] ?? '') === $targetSchema) { $servers[(string) ($knownTenant['database']['host'] ?? '')] = true; }
            }
            if (count($servers) > 1) { throw new \RuntimeException('ambiguous_recorder_schema'); }
            $tenants = array($resolved);
        }
        foreach ($tenants as $tenant) {
            $schema = $tenant['database']['database'] ?? null;
            if (is_string($schema) && preg_match('/^[A-Za-z0-9_]+$/D', $schema) === 1) { $schemas[$schema] = true; }
        }
        if (($context['role'] ?? '') === 'hardness_internal') { $schemas['server'] = true; }
        return array('role'=>(string) ($context['role'] ?? ''), 'schemas'=>array_keys($schemas), 'capabilities'=>is_array($context['capabilities'] ?? null) ? $context['capabilities'] : array());
    }
}
