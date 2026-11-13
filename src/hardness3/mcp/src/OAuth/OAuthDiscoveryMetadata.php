<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use HardnessMcp\Scopes\ScopeCatalog;
use RuntimeException;

/** Public, configuration-only OAuth metadata. No OAuth grant or token is issued here. */
final class OAuthDiscoveryMetadata
{
    private ScopeCatalog $scopeCatalog;

    public function __construct(?ScopeCatalog $scopeCatalog = null)
    {
        $this->scopeCatalog = $scopeCatalog ?? new ScopeCatalog();
    }

    /** @param array<string,string> $tenant @return array<string,mixed> */
    public function protectedResource(array $tenant, string $endpointPath = '/mcp'): array
    {
        $baseUrl = $this->canonicalBaseUrl($tenant);
        return [
            'resource' => $baseUrl . $endpointPath,
            'authorization_servers' => [$baseUrl],
            'scopes_supported' => $this->scopes(),
            'bearer_methods_supported' => ['header'],
        ];
    }

    /** @param array<string,string> $tenant @return array<string,mixed> */
    public function authorizationServer(array $tenant): array
    {
        $issuer = $this->canonicalBaseUrl($tenant);
        return [
            'issuer' => $issuer,
            'authorization_endpoint' => $issuer . '/oauth/authorize',
            'token_endpoint' => $issuer . '/oauth/token',
            'registration_endpoint' => $issuer . '/oauth/register',
            'response_types_supported' => ['code'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'token_endpoint_auth_methods_supported' => ['none'],
            'code_challenge_methods_supported' => ['S256'],
            'scopes_supported' => $this->scopes(),
        ];
    }

    /** @param array<string,string> $tenant */
    public function protectedResourceMetadataUrl(array $tenant, string $endpointPath = '/mcp'): string
    {
        return $this->canonicalBaseUrl($tenant) . '/.well-known/oauth-protected-resource' . $endpointPath;
    }

    /** @param array<string,string> $tenant */
    public function canonicalBaseUrl(array $tenant): string
    {
        $parts = parse_url(trim((string) ($tenant['urlRaiz'] ?? '')));
        if (!is_array($parts) || ($parts['scheme'] ?? null) !== 'https' || empty($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) {
            throw new RuntimeException('Canonical public URL is unavailable.');
        }

        $host = strtolower((string) $parts['host']);
        if ($host === '' || filter_var($host, FILTER_VALIDATE_IP) !== false) {
            throw new RuntimeException('Canonical public URL is unavailable.');
        }
        $port = isset($parts['port']) && (int) $parts['port'] !== 443 ? ':' . (int) $parts['port'] : '';
        return 'https://' . $host . $port;
    }

    /** @return string[] */
    private function scopes(): array
    {
        $scopes = [];
        foreach ($this->scopeCatalog->all() as $scope) {
            if ($scope['active']) { $scopes[] = $scope['id']; }
        }
        sort($scopes, SORT_STRING);
        return $scopes;
    }
}
