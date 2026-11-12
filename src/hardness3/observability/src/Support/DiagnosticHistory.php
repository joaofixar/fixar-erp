<?php
declare(strict_types=1);

namespace Hardness\Observability\Support;

/** Histórico curto, local ao servidor, dos alertas gerados pelo painel. */
final class DiagnosticHistory
{
    private const RESOLVE_AFTER_MISSES = 3;
    private const RETENTION_SECONDS = 86400;
    private $path;

    public function __construct(?string $path = null)
    {
        $test = getenv('OBSERVABILITY_TEST_DIAGNOSTIC_HISTORY_PATH');
        $this->path = $path ?: (is_string($test) && $test !== '' ? $test : dirname(HARDNESS_OBSERVABILITY_ROOT) . '/dados_usuarios/observability/diagnostics/diagnostic-history.json');
    }

    public function update(array $diagnostics, string $scope): array
    {
        $now = time();
        $data = $this->read();
        $scopeKey = hash('sha256', $scope);
        $alerts = isset($data['scopes'][$scopeKey]['alerts']) && is_array($data['scopes'][$scopeKey]['alerts']) ? $data['scopes'][$scopeKey]['alerts'] : array();
        $seen = array(); $active = array();
        foreach ($diagnostics as $diagnostic) {
            $key = hash('sha256', (string) ($diagnostic['title'] ?? '') . '|' . (string) ($diagnostic['link'] ?? ''));
            $seen[$key] = true;
            $record = isset($alerts[$key]) && is_array($alerts[$key]) ? $alerts[$key] : array();
            $record = array_merge($record, $diagnostic, array('firstSeen' => $record['firstSeen'] ?? $now, 'lastSeen' => $now, 'status' => 'active', 'normalSamples' => 0));
            $alerts[$key] = $record;
            $active[] = array_merge($diagnostic, array('lifecycle' => array('status' => 'active', 'firstSeen' => $record['firstSeen'], 'lastSeen' => $now)));
        }
        foreach ($alerts as $key => &$record) {
            if (($record['status'] ?? '') !== 'active' || isset($seen[$key])) { continue; }
            $record['normalSamples'] = (int) ($record['normalSamples'] ?? 0) + 1;
            if ($record['normalSamples'] >= self::RESOLVE_AFTER_MISSES) { $record['status'] = 'resolved'; $record['resolvedAt'] = $now; }
        }
        unset($record);
        foreach ($alerts as $key => $record) {
            if (($record['status'] ?? '') === 'resolved' && $now - (int) ($record['resolvedAt'] ?? 0) > self::RETENTION_SECONDS) { unset($alerts[$key]); }
        }
        $data['scopes'][$scopeKey] = array('alerts' => $alerts);
        $persistenceAvailable = $this->write($data);
        $resolved = array(); $recovering = array();
        foreach ($alerts as $record) {
            if (($record['status'] ?? '') === 'resolved') { $record['lifecycle'] = array('status' => 'resolved', 'firstSeen' => (int) $record['firstSeen'], 'lastSeen' => (int) $record['lastSeen'], 'resolvedAt' => (int) $record['resolvedAt']); $resolved[] = $record; }
            if (($record['status'] ?? '') === 'active' && (int) ($record['normalSamples'] ?? 0) > 0) { $record['lifecycle'] = array('status' => 'recovering', 'firstSeen' => (int) $record['firstSeen'], 'lastSeen' => (int) $record['lastSeen'], 'normalSamples' => (int) $record['normalSamples'], 'requiredSamples' => self::RESOLVE_AFTER_MISSES); $recovering[] = $record; }
        }
        usort($resolved, function (array $left, array $right): int { return ($right['lifecycle']['resolvedAt'] ?? 0) <=> ($left['lifecycle']['resolvedAt'] ?? 0); });
        usort($recovering, function (array $left, array $right): int { return ($right['lifecycle']['lastSeen'] ?? 0) <=> ($left['lifecycle']['lastSeen'] ?? 0); });
        return array('active' => $active, 'recovering' => $recovering, 'resolved' => $resolved, 'persistenceAvailable' => $persistenceAvailable);
    }

    private function read(): array { $json = @file_get_contents($this->path); $data = is_string($json) ? json_decode($json, true) : null; return is_array($data) ? $data : array('scopes' => array()); }
    private function writable(): bool
    {
        $directory=dirname($this->path);
        if (!is_dir($directory) && !@mkdir($directory,0750,true)) { return false; }
        @chmod($directory,0750);
        return is_writable($directory);
    }
    private function write(array $data): bool
    {
        if (!$this->writable()) { return false; }
        return @file_put_contents($this->path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX) !== false;
    }
}
