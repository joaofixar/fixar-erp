<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Linux;

/** Coleta local Linux sem executar comandos nem alterar o sistema. */
final class LinuxServerCollector
{
    public function collect(string $diskPath = '/'): array
    {
        $memory = $this->memory();
        $diskTotal = @disk_total_space($diskPath);
        $diskFree = @disk_free_space($diskPath);
        $disk = ($diskTotal !== false && $diskTotal > 0 && $diskFree !== false)
            ? (100.0 * (1.0 - ((float) $diskFree / (float) $diskTotal))) : null;

        return array(
            'hostname' => (string) (gethostname() ?: 'Indisponível'),
            'time' => date('d/m/Y H:i:s T'),
            'uptime_seconds' => $this->uptime(),
            'load' => $this->load(),
            'cpu_percent' => $this->cpuPercent(),
            'memory_percent' => $memory['percent'],
            'memory_total' => $memory['total'],
            'memory_available' => $memory['available'],
            'swap_percent' => $memory['swap_percent'],
            'disk_percent' => $disk,
            'disk_path' => $diskPath,
            'disk_mount' => $diskPath,
            'disk_total' => $diskTotal === false ? null : (float) $diskTotal,
            'disk_free' => $diskFree === false ? null : (float) $diskFree,
        );
    }

    private function uptime(): ?int
    {
        $raw = @file_get_contents('/proc/uptime');
        if (!is_string($raw) || !preg_match('/^([0-9.]+)/', $raw, $match)) {
            return null;
        }
        return (int) floor((float) $match[1]);
    }

    private function load(): ?array
    {
        $load = function_exists('sys_getloadavg') ? @sys_getloadavg() : false;
        return is_array($load) && count($load) >= 3 ? array((float) $load[0], (float) $load[1], (float) $load[2]) : null;
    }

    private function memory(): array
    {
        $raw = @file('/proc/meminfo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $values = array();
        if (is_array($raw)) {
            foreach ($raw as $line) {
                if (preg_match('/^([A-Za-z_]+):\s+([0-9]+)/', $line, $match)) {
                    $values[$match[1]] = (int) $match[2] * 1024;
                }
            }
        }
        $total = isset($values['MemTotal']) ? $values['MemTotal'] : null;
        $available = isset($values['MemAvailable']) ? $values['MemAvailable'] : (isset($values['MemFree']) ? $values['MemFree'] : null);
        $swapTotal = isset($values['SwapTotal']) ? $values['SwapTotal'] : null;
        $swapFree = isset($values['SwapFree']) ? $values['SwapFree'] : null;
        return array(
            'total' => $total,
            'available' => $available,
            'percent' => ($total && $available !== null) ? 100.0 * (1.0 - ((float) $available / (float) $total)) : null,
            'swap_percent' => ($swapTotal && $swapFree !== null) ? 100.0 * (1.0 - ((float) $swapFree / (float) $swapTotal)) : 0.0,
        );
    }

    private function cpuPercent(): ?float
    {
        $first = $this->cpuCounters();
        if ($first === null) { return null; }
        usleep(100000);
        $second = $this->cpuCounters();
        if ($second === null) { return null; }
        $total = $second['total'] - $first['total'];
        $idle = $second['idle'] - $first['idle'];
        return $total > 0 ? max(0.0, min(100.0, 100.0 * (1.0 - ((float) $idle / (float) $total)))) : null;
    }

    private function cpuCounters(): ?array
    {
        $raw = @file('/proc/stat', FILE_IGNORE_NEW_LINES);
        if (!is_array($raw) || !isset($raw[0]) || !preg_match('/^cpu\s+(.+)$/', $raw[0], $match)) { return null; }
        $parts = preg_split('/\s+/', trim($match[1]));
        $values = array_map('intval', $parts);
        if (count($values) < 4) { return null; }
        return array('total' => array_sum($values), 'idle' => $values[3] + (isset($values[4]) ? $values[4] : 0));
    }
}
