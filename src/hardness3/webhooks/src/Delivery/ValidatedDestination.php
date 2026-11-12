<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

final class ValidatedDestination
{
    /** @var string */ private $url;
    /** @var string */ private $host;
    /** @var int */ private $port;
    /** @var array<int,string> */ private $addresses;

    /** @param array<int,string> $addresses */
    public function __construct(string $url, string $host, int $port, array $addresses)
    {
        $this->url = $url; $this->host = $host; $this->port = $port; $this->addresses = $addresses;
    }
    public function getUrl(): string { return $this->url; }
    public function getHost(): string { return $this->host; }
    public function getPort(): int { return $this->port; }
    /** @return array<int,string> */ public function getAddresses(): array { return $this->addresses; }
}
