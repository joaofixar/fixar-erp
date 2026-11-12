<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Apache;

final class ApachePhpCollector
{
    public function collect(): array
    {
        $apache = function_exists('apache_get_version') ? @apache_get_version() : null;
        if (!is_string($apache) || $apache === '') { $apache = isset($_SERVER['SERVER_SOFTWARE']) ? (string) $_SERVER['SERVER_SOFTWARE'] : 'Indisponível'; }
        $opcache = function_exists('opcache_get_status') ? @opcache_get_status(false) : false;
        $apcu = function_exists('apcu_cache_info') ? @apcu_cache_info(false) : false;
        return array(
            'severity' => 'normal',
            'mode' => $apache,
            'version' => 'PHP ' . PHP_VERSION . ' · ' . PHP_SAPI,
            'details' => array(
                array('label' => 'Versão Apache', 'value' => $apache, 'severity' => 'normal', 'available' => $apache !== 'Indisponível'),
                array('label' => 'Versão PHP', 'value' => PHP_VERSION, 'severity' => 'normal', 'available' => true),
                array('label' => 'SAPI', 'value' => PHP_SAPI, 'severity' => 'normal', 'available' => true),
                array('label' => 'OPcache', 'value' => $opcache === false ? 'Indisponível' : (!empty($opcache['opcache_enabled']) ? 'Ativo' : 'Desativado'), 'severity' => $opcache === false ? 'unavailable' : 'normal', 'available' => $opcache !== false),
                array('label' => 'APCu', 'value' => $apcu === false ? 'Indisponível' : 'Ativo', 'severity' => $apcu === false ? 'unavailable' : 'normal', 'available' => $apcu !== false),
                array('label' => 'memory_limit', 'value' => (string) ini_get('memory_limit'), 'severity' => 'normal', 'available' => true),
                array('label' => 'max_execution_time', 'value' => (string) ini_get('max_execution_time') . ' s', 'severity' => 'normal', 'available' => true),
            ),
        );
    }
}
