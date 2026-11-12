<?php

declare(strict_types=1);

namespace HardnessMcp\Core;

class Response
{
    private int $status;

    /** @var array<string, string> */
    private array $headers;

    /** @var array<string, mixed>|null */
    private ?array $payload;

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed>|null $payload
     */
    private function __construct(int $status, array $headers, ?array $payload)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->payload = $payload;
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, string> $headers
     */
    public static function json(array $payload, int $status = 200, array $headers = []): self
    {
        return new self($status, array_merge([
            'Content-Type' => 'application/json; charset=utf-8',
            'Cache-Control' => 'no-store',
        ], $headers), $payload);
    }

    /**
     * @param array<string, string> $headers
     */
    public static function empty(int $status, array $headers = []): self
    {
        // PHP/Apache otherwise supplies its default text/html media type, even for
        // a zero-length response. The MCP endpoint never emits HTML.
        return new self($status, array_merge([
            'Content-Type' => 'application/json; charset=utf-8',
            'Cache-Control' => 'no-store',
        ], $headers), null);
    }
    public static function redirect(string $location): self { return self::empty(302,['Location'=>$location]); }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        if ($this->payload !== null) {
            echo json_encode($this->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }
}
