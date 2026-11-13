<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Leitura incremental e estritamente read-only dos JSONL produzidos pelo gravador. */
final class MysqlRecorderHistoryRepository
{
    public const MAX_PERIOD_SECONDS = 604800;
    public const DEFAULT_MAX_BYTES = 8388608;
    public const DEFAULT_MAX_LINES = 20000;
    public const DEFAULT_MAX_RESULTS = 5000;
    private const CATEGORIES = array('events', 'digests', 'status');
    private const MAX_LINE_BYTES = MysqlRecorderStorage::MAX_RECORD_BYTES;
    private $baseDirectory;

    public function __construct(?string $baseDirectory = null)
    {
        $testPath = getenv('OBSERVABILITY_TEST_MODE') === '1' ? getenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH') : false;
        $path = $baseDirectory ?? (is_string($testPath) && $testPath !== '' ? $testPath : dirname(HARDNESS_OBSERVABILITY_ROOT) . '/dados_usuarios/observability/mysql');
        if ($path === '' || $path[0] !== '/' || strpos($path, "\0") !== false || preg_match('#(?:^|/)\.\.(?:/|$)#', $path)) {
            throw new \InvalidArgumentException('mysql_history_unsafe_base_path');
        }
        $this->baseDirectory = MysqlRecorderStorage::resolveBaseDirectory($path);
    }

    public function read(string $category, array $authorizedSchemas, int $from, int $to, int $maxBytes = self::DEFAULT_MAX_BYTES, int $maxLines = self::DEFAULT_MAX_LINES, int $maxResults = self::DEFAULT_MAX_RESULTS): array
    {
        if (!in_array($category, self::CATEGORIES, true)) { throw new \InvalidArgumentException('mysql_history_invalid_category'); }
        if ($from < 0 || $to < $from || $to - $from > self::MAX_PERIOD_SECONDS) { throw new \InvalidArgumentException('mysql_history_invalid_period'); }
        $schemas = $this->schemas($authorizedSchemas);
        $maxBytes = max(1024, min(self::DEFAULT_MAX_BYTES, $maxBytes));
        $maxLines = max(1, min(self::DEFAULT_MAX_LINES, $maxLines));
        $maxResults = max(1, min(self::DEFAULT_MAX_RESULTS, $maxResults));
        $result = array('category'=>$category, 'records'=>array(), 'files_seen'=>0, 'bytes_read'=>0, 'lines_read'=>0, 'invalid_lines'=>0, 'truncated_last_lines'=>0, 'available'=>true, 'unavailable_reasons'=>array(), 'limits_reached'=>array());
        if ($schemas === array()) { return $result; }

        $directory = $this->baseDirectory . '/' . $category;
        if (!$this->safeDirectory($directory)) {
            $result['available'] = false; $result['unavailable_reasons'][] = 'source_unavailable'; return $result;
        }
        foreach ($this->days($from, $to) as $day) {
            $path = $directory . '/' . $day . '.jsonl';
            if (!file_exists($path) && !is_link($path)) { continue; }
            if (!$this->safeFile($path, $directory)) {
                $result['available'] = false; $result['unavailable_reasons'][] = 'unsafe_file_refused'; continue;
            }
            $handle = @fopen($path, 'rb');
            if ($handle === false) { $result['available'] = false; $result['unavailable_reasons'][] = 'file_unreadable'; continue; }
            try {
                if (!$this->sameRegularFile($path, $handle) || !flock($handle, LOCK_SH)) {
                    $result['available'] = false; $result['unavailable_reasons'][] = 'file_changed'; continue;
                }
                $result['files_seen']++;
                while (!feof($handle)) {
                    if ($result['bytes_read'] >= $maxBytes) { $result['limits_reached'][] = 'bytes'; break 2; }
                    if ($result['lines_read'] >= $maxLines) { $result['limits_reached'][] = 'lines'; break 2; }
                    $remaining = $maxBytes - $result['bytes_read'];
                    $line = fgets($handle, min(self::MAX_LINE_BYTES + 1, $remaining + 1));
                    if ($line === false) { break; }
                    $length = strlen($line); $result['bytes_read'] += $length; $result['lines_read']++;
                    if ($result['bytes_read'] > $maxBytes) { $result['limits_reached'][] = 'bytes'; break 2; }
                    if ($length > self::MAX_LINE_BYTES || ($line !== '' && substr($line, -1) !== "\n" && !feof($handle))) {
                        $this->discardLongLine($handle, $result, $maxBytes);
                        $result['invalid_lines']++; $result['available'] = false; $result['unavailable_reasons'][] = 'corrupt_record'; continue;
                    }
                    $decoded = json_decode(rtrim($line, "\r\n"), true);
                    if (!is_array($decoded)) {
                        if (feof($handle)) { $result['truncated_last_lines']++; }
                        else { $result['invalid_lines']++; $result['available'] = false; $result['unavailable_reasons'][] = 'corrupt_record'; }
                        continue;
                    }
                    $schema = isset($decoded['database']) && is_string($decoded['database']) ? $decoded['database'] : '';
                    if (!isset($schemas[$schema])) { continue; }
                    $timestamp = $this->timestamp($decoded['collected_at'] ?? null);
                    if ($timestamp === null || $timestamp < $from || $timestamp > $to) { continue; }
                    $decoded['_history_timestamp'] = $timestamp;
                    $result['records'][] = $decoded;
                    if (count($result['records']) >= $maxResults) { $result['limits_reached'][] = 'results'; break 2; }
                }
            } catch (\Throwable $exception) {
                $result['available'] = false; $result['unavailable_reasons'][] = 'read_failed';
            } finally {
                @flock($handle, LOCK_UN); fclose($handle);
            }
        }
        usort($result['records'], function (array $left, array $right): int { return $left['_history_timestamp'] <=> $right['_history_timestamp']; });
        $result['unavailable_reasons'] = array_values(array_unique($result['unavailable_reasons']));
        $result['limits_reached'] = array_values(array_unique($result['limits_reached']));
        return $result;
    }

    private function schemas(array $schemas): array
    {
        $allowed = array();
        foreach ($schemas as $schema) {
            if (is_string($schema) && preg_match('/^[A-Za-z0-9_]+$/D', $schema) === 1) { $allowed[$schema] = true; }
        }
        return $allowed;
    }

    private function days(int $from, int $to): array
    {
        $zone = new \DateTimeZone(date_default_timezone_get());
        $day = (new \DateTimeImmutable('@' . $from))->setTimezone($zone)->setTime(0, 0);
        $last = (new \DateTimeImmutable('@' . $to))->setTimezone($zone)->setTime(0, 0);
        $days = array();
        while ($day <= $last) { $days[] = $day->format('Y-m-d'); $day = $day->modify('+1 day'); }
        return $days;
    }

    private function safeDirectory(string $directory): bool
    {
        if (!is_dir($directory) || is_link($directory)) { return false; }
        $base = realpath($this->baseDirectory); $real = realpath($directory);
        return $base !== false && $real === $directory && strpos($real . '/', $base . '/') === 0;
    }

    private function safeFile(string $path, string $directory): bool
    {
        if (is_link($path) || !is_file($path) || dirname($path) !== $directory) { return false; }
        $real = realpath($path);
        return $real !== false && dirname($real) === $directory && preg_match('/^\d{4}-\d{2}-\d{2}\.jsonl$/D', basename($real)) === 1;
    }

    private function sameRegularFile(string $path, $handle): bool
    {
        $pathStat = @lstat($path); $handleStat = @fstat($handle);
        return is_array($pathStat) && is_array($handleStat) && $pathStat['dev'] === $handleStat['dev'] && $pathStat['ino'] === $handleStat['ino'] && (($handleStat['mode'] & 0170000) === 0100000);
    }

    private function discardLongLine($handle, array &$result, int $maxBytes): void
    {
        while (!feof($handle) && $result['bytes_read'] < $maxBytes) {
            $chunk = fgets($handle, min(8192, $maxBytes - $result['bytes_read'] + 1));
            if ($chunk === false) { break; }
            $result['bytes_read'] += strlen($chunk);
            if (substr($chunk, -1) === "\n") { break; }
        }
    }

    private function timestamp($value): ?int
    {
        if (!is_string($value) || $value === '' || strlen($value) > 64) { return null; }
        try { return (new \DateTimeImmutable($value))->getTimestamp(); }
        catch (\Throwable $exception) { return null; }
    }
}
