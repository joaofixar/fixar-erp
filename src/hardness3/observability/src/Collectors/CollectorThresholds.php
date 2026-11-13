<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors;

/** Limites operacionais opcionais; valores inválidos não alteram os padrões seguros. */
final class CollectorThresholds
{
    private const PATH = '/etc/hardness/observability/collectors.json';

    public static function load(): array
    {
        $defaults = array(
            'cpu_attention_percent' => 70, 'cpu_critical_percent' => 90,
            'memory_attention_percent' => 70, 'memory_critical_percent' => 85,
            'disk_attention_percent' => 80, 'disk_critical_percent' => 90,
            'attention_seconds' => 5, 'critical_seconds' => 15,
            'lock_attention_seconds' => 3, 'lock_critical_seconds' => 10,
            'mysql_connections_attention_percent' => 60, 'mysql_connections_critical_percent' => 85,
            'disk_path' => '/',
        );
        $path = self::PATH;
        if (!is_file($path)) { return $defaults; }
        $data = json_decode((string) @file_get_contents($path), true);
        if (!is_array($data) || array_diff(array_keys($data), array_keys($defaults)) !== array()) { return $defaults; }
        foreach ($defaults as $key => $default) {
            if ($key === 'disk_path') { if (isset($data[$key]) && is_string($data[$key]) && $data[$key] !== '' && $data[$key][0] === '/' && strpos($data[$key], '..') === false) { $defaults[$key] = $data[$key]; } continue; }
            if (isset($data[$key]) && is_int($data[$key]) && $data[$key] > 0) { $defaults[$key] = $data[$key]; }
        }
        return $defaults;
    }
}
