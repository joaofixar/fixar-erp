<?php

declare(strict_types=1);

namespace HardnessMcp\Auth;

use HardnessMcp\Context\ExecutionContext;
use PDO;
use RuntimeException;
use Throwable;

final class Authenticator
{
    private ApiKeyParser $parser;
    private ApiKeyVerifier $verifier;
    private string $currentPepperVersion;

    /** @param array<string, mixed> $config */
    public function __construct(array $config)
    {
        $this->parser = new ApiKeyParser();
        $this->verifier = new ApiKeyVerifier((array) (($config['auth']['credential_peppers'] ?? [])));
        $this->currentPepperVersion = (string) ($config['auth']['credential_pepper_current_version'] ?? '');
        $this->verifier->assertAvailable($this->currentPepperVersion);
    }

    public function authenticate(PDO $pdo, ?string $authorization, string $ip): ?ExecutionContext
    {
        try {
            $apiKey = $this->parser->parseAuthorization($authorization);
            if ($apiKey === null) { return null; }
            $repository = new McpConnectionRepository($pdo);
            $row = $repository->findActiveByCredentialPublicId($apiKey->getPublicId());
            if ($row === null) {
                // Keep a fixed HMAC operation even when the public id is unknown.
                $this->verifier->hash($apiKey->getSecret(), $this->currentPepperVersion);
                return null;
            }
            if (!$this->verifier->verify($apiKey->getSecret(), (string) $row['MCP002_Secret_Hash'], (string) $row['MCP002_Pepper_Version'])) { return null; }
            if ($row['MCP001_Status'] !== 'active' || $row['MCP002_Status'] !== 'active') { return null; }
            if ($this->isExpired($row['MCP001_Data_Expiracao']) || $this->isExpired($row['MCP002_Data_Expiracao'])) { return null; }
            $scopes = json_decode((string) $row['MCP001_Scopes'], true);
            if (!is_array($scopes) || array_filter($scopes, 'is_string') !== $scopes) { return null; }
            foreach ($scopes as $scope) { if (preg_match('/\\A[a-z][a-z0-9_.:-]{0,99}\\z/', $scope) !== 1) { return null; } }
            $repository->registerCredentialUse((int) $row['MCP002_Id'], $ip);
            return new ExecutionContext((int) $row['MCP001_Id'], (int) $row['MCP002_Id'], (int) $row['MCP001_C004_Id'], $row['MCP001_C007_Id'] === null ? null : (int) $row['MCP001_C007_Id'], $scopes, 'api_key', null, null, null, null, ((int) ($row['MCP001_Api_Key_Actor_Valid'] ?? 0)) === 1);
        } catch (RuntimeException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new RuntimeException('Authentication service is unavailable.');
        }
    }

    private function isExpired($value): bool
    {
        if ($value === null) { return false; }
        $timezone = new \DateTimeZone('UTC');
        $expiration = \DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', (string) $value, $timezone);
        $errors = \DateTimeImmutable::getLastErrors();
        if ($expiration === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) { return true; }
        return $expiration <= new \DateTimeImmutable('now', $timezone);
    }
}
