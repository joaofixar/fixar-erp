<?php
declare(strict_types=1);

namespace Hardness\Observability\Http;

final class Request
{
    private $method;
    private $path;
    private $post;
    private $query;

    private function __construct(string $method, string $path, array $post = array(), array $query = array())
    {
        $this->method = $method;
        $this->path = $path;
        $this->post = $post;
        $this->query = $query;
    }

    public static function fromGlobals(): self
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
        $path = parse_url($uri, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = '/';
        }

        return new self(isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : 'GET', rtrim($path, '/') ?: '/', $_POST, $_GET);
    }

    public static function fromValues(string $method, string $path, array $query = array()): self
    {
        return new self(strtoupper($method), rtrim($path, '/') ?: '/', array(), $query);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }
    public function post(string $key): string { return isset($this->post[$key]) && is_string($this->post[$key]) ? $this->post[$key] : ''; }
    public function query(string $key): string { return isset($this->query[$key]) && is_string($this->query[$key]) ? $this->query[$key] : ''; }
}
