<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

use Hardness\Observability\Config\SiteTenantProvider;

/** Allowlist de schemas obtida apenas do inventário local confiável. */
final class MysqlRecorderSchemaProvider
{
    private $tenants;
    public function __construct(?SiteTenantProvider $tenants = null) { $this->tenants=$tenants ?? new SiteTenantProvider(); }

    public function forServer(array $centralTenant): array
    {
        $server=$centralTenant['database']??array(); $host=strtolower((string)($server['host']??'')); $port=(int)($server['port']??3306);
        if ($host==='') { throw new \RuntimeException('mysql_recorder_server_unavailable'); }
        $schemas=array();
        foreach ($this->tenants->all() as $tenant) { $database=$tenant['database']??array(); if (strtolower((string)($database['host']??''))!==$host||(int)($database['port']??3306)!==$port) { continue; } $schema=(string)($database['database']??''); if (preg_match('/^[A-Za-z0-9_]+$/D',$schema)===1){$schemas[$schema]=true;} }
        if ($schemas===array()) { throw new \RuntimeException('mysql_recorder_no_authorized_schemas'); }
        if (count($schemas)>100) { throw new \RuntimeException('mysql_recorder_schema_limit_exceeded'); }
        $result=array_keys($schemas);sort($result);return $result;
    }
}
