<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

final class HttpResult
{
    /** @var int|null */ private $status; /** @var string|null */ private $error; /** @var int */ private $durationMs; /** @var int */ private $responseBytes; /** @var array<string,string> */ private $headers;
    public function __construct(?int $status, ?string $error, int $durationMs, int $responseBytes, array $headers = array())
    { $this->status = $status; $this->error = $error; $this->durationMs = $durationMs; $this->responseBytes = $responseBytes; $this->headers = $headers; }
    public function getStatus(): ?int { return $this->status; } public function getError(): ?string { return $this->error; }
    public function getDurationMs(): int { return $this->durationMs; } public function getResponseBytes(): int { return $this->responseBytes; }
    public function getHeader(string $name): ?string { foreach ($this->headers as $key => $value) { if (strcasecmp($key, $name) === 0) { return $value; } } return null; }
}
