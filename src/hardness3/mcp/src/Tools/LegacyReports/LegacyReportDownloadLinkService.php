<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\LegacyReports;

use HardnessMcp\Context\ExecutionContext;

final class LegacyReportDownloadLinkService
{
    public const DOWNLOAD_TTL_SECONDS = 900;
    private string $rootUrl;
    private string $secret;

    public function __construct(string $rootUrl, string $secret) { $this->rootUrl = rtrim($rootUrl, '/'); $this->secret = $secret; }

    public function issue(string $fileName, string $tenantDatabase, ExecutionContext $context): ?string
    {
        if ($this->rootUrl === '' || $this->secret === '' || $context->getOAuthGrantId() === null || $context->getConnectionId() < 1 || $context->getCompanyId() < 1 || preg_match('/\A[a-f0-9]{32}\.json\z/', $fileName) !== 1) return null;
        $data = ['f' => $fileName, 't' => hash('sha256', $tenantDatabase), 'c' => $context->getCompanyId(), 'n' => $context->getConnectionId(), 'g' => $context->getOAuthGrantId(), 'e' => time() + self::DOWNLOAD_TTL_SECONDS];
        $body = rtrim(strtr(base64_encode(json_encode($data, JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
        $mac = rtrim(strtr(base64_encode(hash_hmac('sha256', 'legacy-report-download:v1:' . $body, $this->secret, true)), '+/', '-_'), '=');
        return $this->rootUrl . '/mcp?legacy_report_download=1&token=' . $body . '.' . $mac;
    }

    /** @return array{f:string,t:string,c:int,n:int,g:int,e:int}|null */
    public function verify(string $token): ?array
    {
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2 || preg_match('/\A[A-Za-z0-9_-]{20,800}\z/', $parts[0]) !== 1 || preg_match('/\A[A-Za-z0-9_-]{20,100}\z/', $parts[1]) !== 1) return null;
        $expected = rtrim(strtr(base64_encode(hash_hmac('sha256', 'legacy-report-download:v1:' . $parts[0], $this->secret, true)), '+/', '-_'), '=');
        if (!hash_equals($expected, $parts[1])) return null;
        $raw = base64_decode(strtr($parts[0], '-_', '+/') . str_repeat('=', (4 - strlen($parts[0]) % 4) % 4), true);
        $data = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($data) || !is_string($data['f'] ?? null) || !is_string($data['t'] ?? null) || !is_int($data['c'] ?? null) || !is_int($data['n'] ?? null) || !is_int($data['g'] ?? null) || !is_int($data['e'] ?? null)) return null;
        if (preg_match('/\A[a-f0-9]{32}\.json\z/', $data['f']) !== 1 || preg_match('/\A[a-f0-9]{64}\z/', $data['t']) !== 1 || $data['c'] < 1 || $data['n'] < 1 || $data['g'] < 1 || $data['e'] < time() || $data['e'] > time() + self::DOWNLOAD_TTL_SECONDS + 1) return null;
        return $data;
    }
}
