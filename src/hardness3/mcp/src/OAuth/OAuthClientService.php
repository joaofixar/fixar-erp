<?php

declare(strict_types=1);

namespace HardnessMcp\OAuth;

use RuntimeException;

/** Foundation service: client registration endpoints are deliberately deferred. */
final class OAuthClientService
{
    private OAuthClientRepository $clients;

    public function __construct(OAuthClientRepository $clients) { $this->clients = $clients; }

    public function registerPreRegisteredClient(string $clientId, string $name): int
    {
        $clientId = trim($clientId); $name = trim($name);
        if ($clientId === '' || strlen($clientId) > 191 || $name === '' || strlen($name) > 150) { throw new RuntimeException('Invalid OAuth client.'); }
        if ($this->clients->findByClientId($clientId) !== null) { throw new RuntimeException('OAuth client already exists.'); }
        return $this->clients->create($clientId, 'pre_registered', $name);
    }
}
