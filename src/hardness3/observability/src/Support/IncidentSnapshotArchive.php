<?php
declare(strict_types=1);

namespace Hardness\Observability\Support;

/** Capturas manuais de incidentes, isoladas por escopo e mantidas localmente. */
final class IncidentSnapshotArchive
{
    private const MAX_ITEMS = 50;
    private const RETENTION_SECONDS = 2592000;
    private $root;
    private $legacyRoot;

    public function __construct(?string $root = null)
    {
        $test = getenv('OBSERVABILITY_TEST_SNAPSHOT_ARCHIVE_PATH');
        $this->root = $root ?: (is_string($test) && $test !== '' ? $test : dirname(HARDNESS_OBSERVABILITY_ROOT) . '/dados_usuarios/observability/captures');
        $this->legacyRoot = dirname($this->root);
    }

    public function save(array $snapshot, array $context, string $note = ''): array
    {
        $scope = $this->scope($context);
        $directory = $this->directory($scope);
        if (!$this->ensureDirectory($directory)) {
            throw new \RuntimeException('snapshot_archive_unavailable');
        }
        $now = time();
        $id = date('Ymd-His', $now) . '-' . bin2hex(random_bytes(4));
        $record = array(
            'id' => $id,
            'capturedAt' => date('c', $now),
            'capturedBy' => array('role' => (string) ($context['role'] ?? ''), 'scope' => (string) ($context['scopeLabel'] ?? '')),
            'note' => $this->note($note),
            'snapshot' => $snapshot,
        );
        $target = $directory . '/' . $id . '.json';
        $temporary = $target . '.tmp-' . bin2hex(random_bytes(4));
        $written = @file_put_contents($temporary, json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
        if ($written === false || !@rename($temporary, $target)) {
            @unlink($temporary);
            throw new \RuntimeException('snapshot_archive_unavailable');
        }
        @chmod($target, 0640);
        $this->prune($directory);
        return $this->summary($record);
    }

    public function list(array $context): array
    {
        $directories = array($this->directory($this->scope($context)), $this->legacyDirectory($this->scope($context)));
        $items = array();
        foreach ($directories as $directory) {
            foreach (glob($directory . '/*.json') ?: array() as $file) {
                $record = $this->read($file);
                if ($record !== null) { $items[] = $this->summary($record); }
            }
        }
        usort($items, function (array $a, array $b): int { return strcmp($b['capturedAt'], $a['capturedAt']); });
        return array('items' => $items, 'persistenceAvailable' => is_writable($directory));
    }

    public function get(array $context, string $id): ?array
    {
        if (!preg_match('/^\d{8}-\d{6}-[a-f0-9]{8}$/', $id)) { return null; }
        $record = $this->read($this->directory($this->scope($context)) . '/' . $id . '.json');
        if ($record === null) { $record=$this->read($this->legacyDirectory($this->scope($context)) . '/' . $id . '.json'); }
        return $record;
    }

    /** Exclui uma captura somente dentro do escopo atual. */
    public function delete(array $context, string $id): bool
    {
        if (!preg_match('/^\d{8}-\d{6}-[a-f0-9]{8}$/', $id)) { return false; }
        foreach (array($this->directory($this->scope($context)), $this->legacyDirectory($this->scope($context))) as $directory) {
            $file = $directory . '/' . $id . '.json';
            if (!is_file($file)) { continue; }
            $record = $this->read($file);
            if ($record === null || !hash_equals((string) $record['id'], $id)) { return false; }
            return @unlink($file);
        }
        return false;
    }

    private function scope(array $context): string
    {
        return hash('sha256', (string) ($context['role'] ?? '') . '|' . (string) ($context['scopeLabel'] ?? ''));
    }
    private function directory(string $scope): string { return $this->root . '/' . $scope; }
    private function legacyDirectory(string $scope): string { return $this->legacyRoot . '/' . $scope; }
    private function note(string $note): string
    {
        $note = trim($note);
        return function_exists('mb_substr') ? mb_substr($note, 0, 500, 'UTF-8') : substr($note, 0, 500);
    }
    private function parentWritable(): bool { return is_dir($this->root) ? is_writable($this->root) : is_writable(dirname($this->root)); }
    private function ensureDirectory(string $directory): bool
    {
        if (!is_dir($directory) && !@mkdir($directory, 0750, true)) { return false; }
        @chmod($this->root, 0750); @chmod($directory, 0750);
        return is_writable($directory);
    }
    private function read(string $file): ?array
    {
        $json = @file_get_contents($file); $record = is_string($json) ? json_decode($json, true) : null;
        return is_array($record) && isset($record['id'], $record['capturedAt'], $record['snapshot']) && is_array($record['snapshot']) ? $record : null;
    }
    private function summary(array $record): array
    {
        $snapshot = $record['snapshot']; $summary = $snapshot['overview']['processSummary'] ?? array();
        return array('id' => $record['id'], 'capturedAt' => $record['capturedAt'], 'note' => (string) ($record['note'] ?? ''), 'capturedBy' => $record['capturedBy'] ?? array(), 'overall' => $snapshot['overall'] ?? array(), 'processSummary' => $summary, 'diagnostics' => array_map(function (array $item): array { return array('title' => (string) ($item['title'] ?? ''), 'severity' => (string) ($item['severity'] ?? 'unknown')); }, array_slice($snapshot['diagnostics'] ?? array(), 0, 10)));
    }
    private function prune(string $directory): void
    {
        $files = glob($directory . '/*.json') ?: array(); usort($files, function (string $a, string $b): int { return filemtime($b) <=> filemtime($a); });
        $now = time();
        foreach ($files as $index => $file) { if ($index >= self::MAX_ITEMS || $now - (int) @filemtime($file) > self::RETENTION_SECONDS) { @unlink($file); } }
    }
}
