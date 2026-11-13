<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\LegacyReports;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tenant\ResolvedTenantContext;
use PDO;

final class LegacyReportGateway
{
    private PDO $pdo;
    private ?ResolvedTenantContext $tenant;
    /** @var (callable(array<string,mixed>,array<string,mixed>,ExecutionContext):resource)|null */
    private $streamFactory;
    /** @param (callable(array<string,mixed>,array<string,mixed>,ExecutionContext):resource)|null $streamFactory */
    public function __construct(PDO $pdo, ?ResolvedTenantContext $tenant, $streamFactory=null) { $this->pdo = $pdo; $this->tenant = $tenant; $this->streamFactory = is_callable($streamFactory) ? $streamFactory : null; }

    /** @param array<string,mixed> $report @param array<string,mixed> $arguments @return mixed */
    public function fetch(array $report, array $arguments, ExecutionContext $context)
    {
        $stream = $this->open($report, $arguments, $context);
        $body = stream_get_contents($stream); fclose($stream);
        if (!is_string($body) || $body === '') throw new \RuntimeException('legacy_report_unavailable');
        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) throw new \RuntimeException('legacy_report_invalid_json');
        return $decoded;
    }

    /** @param array<string,mixed> $report @param array<string,mixed> $arguments @return array{file:string,bytes:int} */
    public function export(array $report, array $arguments, ExecutionContext $context, string $directory): array
    {
        $this->cleanupExpired($directory);
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) throw new \RuntimeException('legacy_report_export_unavailable');
        @chmod($directory, 0700);
        $fileName = bin2hex(random_bytes(16)) . '.json';
        $path = rtrim($directory, '/') . '/' . $fileName;
        $temporary = $path . '.part'; $source = null; $target = null;
        try {
            $source = $this->open($report, $arguments, $context);
            $target = @fopen($temporary, 'x+b');
            if ($target === false) throw new \RuntimeException('legacy_report_export_unavailable');
            $bytes = stream_copy_to_stream($source, $target);
            if (!is_int($bytes) || $bytes < 1) throw new \RuntimeException('legacy_report_unavailable');
            fclose($target); $target = null; fclose($source); $source = null;
            if (!rename($temporary, $path)) throw new \RuntimeException('legacy_report_export_unavailable');
            @chmod($path, 0600);
            return ['file' => $fileName, 'bytes' => $bytes];
        } catch (\Throwable $exception) {
            if (is_resource($source)) fclose($source);
            if (is_resource($target)) fclose($target);
            if (is_file($temporary) && !is_link($temporary)) @unlink($temporary);
            throw $exception;
        }
    }

    public function cleanupExpired(string $directory): void
    {
        if (!is_dir($directory)) return;
        $cutoff = time() - LegacyReportDownloadLinkService::DOWNLOAD_TTL_SECONDS;
        foreach (glob(rtrim($directory, '/') . '/*.json') ?: [] as $path) if (preg_match('/\/[a-f0-9]{32}\.json\z/', $path) === 1 && !is_link($path) && is_file($path) && (($mtime = filemtime($path)) !== false) && $mtime < $cutoff) @unlink($path);
        foreach (glob(rtrim($directory, '/') . '/*.part') ?: [] as $path) if (preg_match('/\/[a-f0-9]{32}\.json\.part\z/', $path) === 1 && !is_link($path) && is_file($path)) @unlink($path);
    }

    /** @param array<string,mixed> $report @param array<string,mixed> $arguments @return resource */
    private function open(array $report, array $arguments, ExecutionContext $context)
    {
        if ($this->streamFactory !== null) return ($this->streamFactory)($report, $arguments, $context);
        if ($this->tenant === null) throw new \RuntimeException('legacy_report_unavailable');
        $user = $context->getResponsibleUserId();
        if ($user === null || $user <= 0) throw new \RuntimeException('legacy_report_actor_required');
        $statement = $this->pdo->prepare("SELECT C007_Login,C007_Senha FROM C007 WHERE C007_Id=:id AND C007_Ativo='S' LIMIT 1");
        $statement->execute([':id' => $user]); $actor = $statement->fetch();
        if (!is_array($actor) || !is_string($actor['C007_Login'] ?? null) || !is_string($actor['C007_Senha'] ?? null)) throw new \RuntimeException('legacy_report_actor_unavailable');
        $query = ['API_AUTH' => md5($actor['C007_Login'] . ':' . $actor['C007_Senha'])];
        foreach (['DATAINICIAL', 'DATAFINAL', 'MCP_PAGE_SIZE', 'MCP_PAGE_OFFSET'] as $key) if (isset($arguments[$key])) $query[$key] = $arguments[$key];
        foreach (($arguments['parameters'] ?? []) as $key => $value) if (is_string($key) && is_string($value) && in_array($key, $report['parameters'] ?? [], true)) $query[$key] = $value;
        if (isset($report['app_code']) && is_string($report['app_code'])) $query['codigoApp'] = $report['app_code'];
        $base = rtrim($this->tenant->getRootUrl(), '/');
        if ($base === '') throw new \RuntimeException('legacy_report_url_unavailable');
        $url = $base . '/api/rel/' . rawurlencode((string) $report['route']) . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        $stream = @fopen($url, 'rb', false, stream_context_create(['http' => ['method' => 'GET', 'timeout' => 120, 'ignore_errors' => false, 'header' => "Accept: application/json\r\n"]]));
        if ($stream === false) throw new \RuntimeException('legacy_report_unavailable');
        return $stream;
    }
}
