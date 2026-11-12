<?php

declare(strict_types=1);

namespace HardnessMcp\Context;

final class ExecutionContext
{
    private int $connectionId;
    private int $credentialId;
    private int $companyId;
    private ?int $responsibleUserId;
    private string $authMethod;
    private ?int $oauthClientId;
    private ?int $oauthGrantId;
    private ?int $oauthAccessTokenId;
    private ?string $trustedRemoteIp;
    private bool $apiKeyErpActorValid;
    /** @var string[] */
    private array $scopes;

    /** @param string[] $scopes */
    public function __construct(int $connectionId, int $credentialId, int $companyId, ?int $responsibleUserId, array $scopes, string $authMethod = 'api_key', ?int $oauthClientId = null, ?int $oauthGrantId = null, ?int $oauthAccessTokenId = null, ?string $trustedRemoteIp = null, bool $apiKeyErpActorValid = false)
    {
        $this->connectionId = $connectionId;
        $this->credentialId = $credentialId;
        $this->companyId = $companyId;
        $this->responsibleUserId = $responsibleUserId;
        $this->scopes = array_values($scopes);
        $this->authMethod = $authMethod;
        $this->oauthClientId = $oauthClientId;
        $this->oauthGrantId = $oauthGrantId;
        $this->oauthAccessTokenId = $oauthAccessTokenId;
        $this->trustedRemoteIp = $trustedRemoteIp;
        $this->apiKeyErpActorValid = $apiKeyErpActorValid;
    }

    public function getConnectionId(): int { return $this->connectionId; }
    public function getCredentialId(): int { return $this->credentialId; }
    public function getCompanyId(): int { return $this->companyId; }
    public function getResponsibleUserId(): ?int { return $this->responsibleUserId; }
    /** @return string[] */
    public function getScopes(): array { return $this->scopes; }
    public function hasScope(string $scope): bool { return in_array($scope, $this->scopes, true); }
    public function getAuthMethod(): string { return $this->authMethod; }
    public function getOAuthClientId(): ?int { return $this->oauthClientId; }
    public function getOAuthGrantId(): ?int { return $this->oauthGrantId; }
    public function getOAuthAccessTokenId(): ?int { return $this->oauthAccessTokenId; }
    public function getTrustedRemoteIp(): ?string { return $this->trustedRemoteIp; }
    public function hasErpActor(): bool { return $this->authMethod === 'oauth' ? $this->responsibleUserId !== null : $this->apiKeyErpActorValid; }

    public function withTrustedRemoteIp(string $remoteIp): self
    {
        return new self($this->connectionId, $this->credentialId, $this->companyId, $this->responsibleUserId, $this->scopes, $this->authMethod, $this->oauthClientId, $this->oauthGrantId, $this->oauthAccessTokenId, $remoteIp, $this->apiKeyErpActorValid);
    }
}
