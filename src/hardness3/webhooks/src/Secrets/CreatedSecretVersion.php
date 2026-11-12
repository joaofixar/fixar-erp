<?php

declare(strict_types=1);

namespace HardnessWebhooks\Secrets;

/** Plaintext is intentionally retained only in this short-lived creation result. */
final class CreatedSecretVersion
{
    /** @var int */
    private $id;
    /** @var int */
    private $version;
    /** @var string */
    private $plaintextSecret;

    public function __construct(int $id, int $version, string $plaintextSecret)
    {
        $this->id = $id;
        $this->version = $version;
        $this->plaintextSecret = $plaintextSecret;
    }

    public function getId(): int { return $this->id; }
    public function getVersion(): int { return $this->version; }
    public function getPlaintextSecret(): string { return $this->plaintextSecret; }
}
