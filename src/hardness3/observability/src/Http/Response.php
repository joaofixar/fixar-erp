<?php
declare(strict_types=1);

namespace Hardness\Observability\Http;

final class Response
{
    private $status;
    private $headers;
    private $body;

    private function __construct(int $status, array $headers, string $body)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function html(string $body, int $status = 200): self
    {
        return new self($status, array('Content-Type' => 'text/html; charset=UTF-8', 'Cache-Control' => 'no-store'), $body);
    }

    public static function json(array $data, int $status = 200): self
    {
        return new self($status, array('Content-Type' => 'application/json; charset=UTF-8', 'Cache-Control' => 'no-store'), json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    public static function downloadJson(array $data, string $filename): self
    {
        return new self(200, array('Content-Type' => 'application/json; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="' . str_replace('"', '', $filename) . '"', 'Cache-Control' => 'no-store'), json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    public static function empty(int $status = 204): self
    {
        return new self($status, array('Cache-Control' => 'no-store'), '');
    }
    public static function redirect(string $path): self { return new self(302,array('Location'=>$path,'Cache-Control'=>'no-store'),''); }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function body(): string
    {
        return $this->body;
    }
}
