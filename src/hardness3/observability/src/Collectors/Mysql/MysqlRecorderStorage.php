<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Persistência privada do gravador: JSONL diário e estado substituído atomicamente. */
final class MysqlRecorderStorage
{
    public const DEFAULT_MAX_FILE_BYTES = 16777216;
    public const MAX_RECORD_BYTES = 262144;
    public const MAX_STATE_BYTES = 8388608;
    private const CATEGORIES = array('events', 'digests', 'status', 'state');
    private $baseDirectory;
    private $maxFileBytes;

    public function __construct(int $maxFileBytes = self::DEFAULT_MAX_FILE_BYTES)
    {
        $testPath = getenv('OBSERVABILITY_TEST_MODE') === '1' ? getenv('OBSERVABILITY_TEST_MYSQL_RECORDER_PATH') : false;
        $baseDirectory = is_string($testPath) && $testPath !== '' ? $testPath : dirname(HARDNESS_OBSERVABILITY_ROOT) . '/dados_usuarios/observability/mysql';
        if ($baseDirectory === '' || in_array(rtrim($baseDirectory, '/'), array('', '/tmp', HARDNESS_OBSERVABILITY_ROOT, dirname(HARDNESS_OBSERVABILITY_ROOT)), true) || $baseDirectory[0] !== '/' || strpos($baseDirectory, "\0") !== false || preg_match('#(?:^|/)\.\.(?:/|$)#', $baseDirectory)) {
            throw new \InvalidArgumentException('mysql_recorder_unsafe_base_path');
        }
        $this->baseDirectory = self::resolveBaseDirectory($baseDirectory);
        $this->maxFileBytes = max(256, $maxFileBytes);
        $this->prepare();
    }

    public function append(string $category, array $record, ?string $day = null): array
    {
        if (!in_array($category, array('events', 'digests', 'status'), true)) { throw new \InvalidArgumentException('mysql_recorder_invalid_category'); }
        $day = $day ?? date('Y-m-d');
        if (!$this->validDay($day)) { throw new \InvalidArgumentException('mysql_recorder_invalid_day'); }
        $path = $this->baseDirectory . '/' . $category . '/' . $day . '.jsonl';
        $this->assertSafeFile($path);
        $json = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($json)) { throw new \RuntimeException('mysql_recorder_json_encode_failed'); }
        $line = $json . "\n";
        if (strlen($line) > self::MAX_RECORD_BYTES) {
            if ($category === 'digests') { return $this->appendDigestChunks($record, $day); }
            throw new \RuntimeException('mysql_recorder_record_too_large');
        }
        return $this->appendLine($category, $day, $line);
    }

    private function appendLine(string $category, string $day, string $line): array
    {
        $path = $this->baseDirectory . '/' . $category . '/' . $day . '.jsonl';
        $this->assertSafeFile($path);
        $oldMask = umask(0077); $handle = @fopen($path, 'c+b'); umask($oldMask);
        if ($handle === false) { throw new \RuntimeException('mysql_recorder_history_open_failed'); }
        try {
            $this->assertOpenFile($path, $handle);
            @chmod($path, 0600);
            if (!flock($handle, LOCK_EX)) { throw new \RuntimeException('mysql_recorder_history_lock_failed'); }
            $size = fstat($handle); $before = is_array($size) ? (int) $size['size'] : 0;
            if ($before + strlen($line) > $this->maxFileBytes) {
                return array('written' => false, 'limit_reached' => true, 'bytes' => $before);
            }
            if (fseek($handle, 0, SEEK_END) !== 0) { throw new \RuntimeException('mysql_recorder_history_seek_failed'); }
            $written = fwrite($handle, $line);
            if ($written !== strlen($line) || !fflush($handle)) {
                @ftruncate($handle, $before); @fflush($handle);
                throw new \RuntimeException('mysql_recorder_history_write_failed');
            }
            return array('written' => true, 'limit_reached' => false, 'bytes' => $before + $written);
        } finally {
            @flock($handle, LOCK_UN); fclose($handle);
        }
    }

    private function appendDigestChunks(array $record, string $day): array
    {
        $items = $record['counters']['items'] ?? null;
        if (!is_array($items) || count($items) < 2) { throw new \RuntimeException('mysql_recorder_record_too_large'); }
        $chunks = array(); $current = array(); $target = self::MAX_RECORD_BYTES - 1024;
        foreach ($items as $item) {
            $candidate = $current; $candidate[] = $item;
            $probe = $record; $probe['counters']['items'] = $candidate;
            $encoded = json_encode($probe, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (!is_string($encoded)) { throw new \RuntimeException('mysql_recorder_json_encode_failed'); }
            if ($current !== array() && strlen($encoded) + 1 > $target) { $chunks[] = $current; $current = array($item); }
            else { $current = $candidate; }
        }
        if ($current !== array()) { $chunks[] = $current; }
        if (count($chunks) < 2) { throw new \RuntimeException('mysql_recorder_record_too_large'); }

        $written = 0; $bytes = 0; $limitReached = false; $total = count($chunks);
        foreach ($chunks as $index => $chunk) {
            $part = $record; $part['counters']['items'] = $chunk;
            if ($index > 0) { $part['counters']['disappeared_digests'] = array(); }
            $part['collection']['digest_chunk'] = array('index'=>$index + 1, 'total'=>$total);
            $json = json_encode($part, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (!is_string($json) || strlen($json) + 1 > self::MAX_RECORD_BYTES) { throw new \RuntimeException('mysql_recorder_record_too_large'); }
            $result = $this->appendLine('digests', $day, $json . "\n"); $bytes = (int) ($result['bytes'] ?? $bytes);
            if (!empty($result['written'])) { $written++; }
            if (!empty($result['limit_reached'])) { $limitReached = true; break; }
        }
        return array('written'=>$written === $total, 'limit_reached'=>$limitReached, 'bytes'=>$bytes, 'records_written'=>$written, 'records_total'=>$total, 'chunked'=>true);
    }

    public function loadState(string $database): ?array
    {
        $path = $this->statePath($database);
        $this->assertSafeFile($path);
        if (!is_file($path)) { return null; }
        $handle = @fopen($path, 'rb');
        if ($handle === false) { return null; }
        try {
            $this->assertOpenFile($path, $handle);
            $stat = fstat($handle);
            if (!is_array($stat) || $stat['size'] > self::MAX_STATE_BYTES) { return null; }
            $contents = stream_get_contents($handle);
        } catch (\Throwable $exception) { return null; }
        finally { fclose($handle); }
        if (!is_string($contents)) { return null; }
        $decoded = json_decode($contents, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function saveState(string $database, array $state): void
    {
        $target = $this->statePath($database);
        $this->assertSafeFile($target);
        $json = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($json)) { throw new \RuntimeException('mysql_recorder_state_encode_failed'); }
        if (strlen($json) + 1 > self::MAX_STATE_BYTES) { throw new \RuntimeException('mysql_recorder_state_too_large'); }
        $temporary = $target . '.tmp.' . bin2hex(random_bytes(8));
        $oldMask = umask(0077); $handle = @fopen($temporary, 'x+b'); umask($oldMask);
        if ($handle === false) { throw new \RuntimeException('mysql_recorder_state_temporary_failed'); }
        @chmod($temporary, 0600);
        try {
            $written = fwrite($handle, $json . "\n");
            if ($written !== strlen($json) + 1 || !fflush($handle)) { throw new \RuntimeException('mysql_recorder_state_write_failed'); }
        } catch (\Throwable $exception) {
            fclose($handle); @unlink($temporary); throw $exception;
        }
        fclose($handle);
        if (is_link($target) || !@rename($temporary, $target)) { @unlink($temporary); throw new \RuntimeException('mysql_recorder_state_replace_failed'); }
        @chmod($target, 0600);
    }

    public function lockPath(string $database): string
    {
        $this->validateDatabase($database);
        $path = $this->baseDirectory . '/state/' . $database . '.lock';
        $this->assertSafeFile($path);
        return $path;
    }

    public function retain(int $days = 7, ?int $now = null): array
    {
        $days = max(1, min(90, $days)); $now = $now ?? time(); $removed = array(); $failures = array();
        $cutoffDay = date('Y-m-d', $now - ($days - 1) * 86400);
        foreach (array('events', 'digests', 'status') as $category) {
            $directory = $this->baseDirectory . '/' . $category;
            if (is_link($directory) || realpath($directory) !== $directory) { $failures[] = $category . '/unsafe_directory'; continue; }
            foreach (scandir($directory) ?: array() as $name) {
                if (!preg_match('/^(\d{4}-\d{2}-\d{2})\.jsonl$/', $name, $matches) || !$this->validDay($matches[1]) || $matches[1] >= $cutoffDay) { continue; }
                $path = $directory . '/' . $name;
                if (is_link($path) || !is_file($path)) { continue; }
                if (@unlink($path)) { $removed[] = $category . '/' . $name; } else { $failures[] = $category . '/' . $name; }
            }
        }
        return array('removed' => $removed, 'failures' => $failures, 'retention_days' => $days);
    }

    public function baseDirectory(): string { return $this->baseDirectory; }

    /**
     * Resolve somente os diretórios pais definidos pela aplicação. Isso permite
     * instalações em que dados_usuarios aponta para um volume dedicado, sem aceitar
     * symlinks no diretório mysql nem nos arquivos controlados pelo gravador.
     */
    public static function resolveBaseDirectory(string $path): string
    {
        $path = rtrim($path, '/');
        $parent = dirname($path);
        $suffix = array(basename($path));
        while (!file_exists($parent) && !is_link($parent)) {
            if ($parent === '/' || $parent === '.' || $parent === '') { throw new \RuntimeException('mysql_recorder_unsafe_directory'); }
            array_unshift($suffix, basename($parent));
            $next = dirname($parent);
            if ($next === $parent) { throw new \RuntimeException('mysql_recorder_unsafe_directory'); }
            $parent = $next;
        }
        $resolved = realpath($parent);
        if ($resolved === false || !is_dir($resolved)) { throw new \RuntimeException('mysql_recorder_unsafe_directory'); }
        foreach ($suffix as $component) {
            if ($component === '' || $component === '.' || $component === '..' || strpos($component, "\0") !== false) { throw new \RuntimeException('mysql_recorder_unsafe_directory'); }
        }
        return ($resolved === '/' ? '' : rtrim($resolved, '/')) . '/' . implode('/', $suffix);
    }

    private function prepare(): void
    {
        $this->assertNoSymlinkComponents($this->baseDirectory);
        if (!is_dir($this->baseDirectory) && !@mkdir($this->baseDirectory, 0700, true)) { throw new \RuntimeException('mysql_recorder_directory_create_failed'); }
        if (is_link($this->baseDirectory) || !is_dir($this->baseDirectory) || realpath($this->baseDirectory) !== $this->baseDirectory) { throw new \RuntimeException('mysql_recorder_unsafe_directory'); }
        @chmod($this->baseDirectory, 0700);
        foreach (self::CATEGORIES as $category) {
            $directory = $this->baseDirectory . '/' . $category;
            if (is_link($directory) || (!is_dir($directory) && !@mkdir($directory, 0700))) { throw new \RuntimeException('mysql_recorder_category_create_failed'); }
            @chmod($directory, 0700);
        }
    }

    private function statePath(string $database): string
    {
        $this->validateDatabase($database);
        return $this->baseDirectory . '/state/' . $database . '.json';
    }

    private function validateDatabase(string $database): void
    {
        if (preg_match('/^[A-Za-z0-9_]+$/', $database) !== 1) { throw new \InvalidArgumentException('invalid_database_name'); }
    }

    private function assertSafeFile(string $path): void
    {
        if (is_link($path)) { throw new \RuntimeException('mysql_recorder_symlink_refused'); }
        $directory = dirname($path);
        if (is_link($directory) || realpath($directory) !== $directory || strpos($directory . '/', $this->baseDirectory . '/') !== 0) { throw new \RuntimeException('mysql_recorder_unsafe_path'); }
    }

    private function assertOpenFile(string $path, $handle): void
    {
        $pathStat = @lstat($path); $handleStat = @fstat($handle);
        if (!is_array($pathStat) || !is_array($handleStat) || $pathStat['dev'] !== $handleStat['dev'] || $pathStat['ino'] !== $handleStat['ino'] || (($handleStat['mode'] & 0170000) !== 0100000)) {
            throw new \RuntimeException('mysql_recorder_file_changed');
        }
    }

    private function assertNoSymlinkComponents(string $path): void
    {
        $current = '/';
        foreach (explode('/', trim($path, '/')) as $component) {
            $current = rtrim($current, '/') . '/' . $component;
            if (file_exists($current) && is_link($current)) { throw new \RuntimeException('mysql_recorder_symlink_refused'); }
        }
    }

    private function validDay(string $day): bool
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $day);
        return $date !== false && $date->format('Y-m-d') === $day;
    }
}
