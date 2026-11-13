<?php

declare(strict_types=1);

namespace HardnessMcp\Core;

class Request
{
    private string $method;
    private string $path;
    private string $host;
    private string $body;
    /** @var array<string,string> */ private array $query;
    private bool $ambiguousQuery;

    /** @var array<string, string> */
    private array $headers;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(string $method, string $path, string $host, string $body, array $headers = [], array $query = [], bool $ambiguousQuery = false)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->host = $host;
        $this->body = $body;
        $this->headers = [];
        foreach ($headers as $name => $value) {
            $this->headers[strtolower($name)] = $value;
        }
        $this->query=$query; $this->ambiguousQuery=$ambiguousQuery;
    }

    public static function capture(): self
    {
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = (string) parse_url($requestUri, PHP_URL_PATH);

        $rawQuery=(string) parse_url($requestUri, PHP_URL_QUERY); $query=[]; $ambiguous=false;
        foreach ($rawQuery===''?[]:explode('&',$rawQuery) as $part) { $pair=explode('=',$part,2); $key=urldecode($pair[0]); if($key===''||isset($query[$key])){$ambiguous=true;continue;} $query[$key]=urldecode($pair[1]??''); }
        return new self(
            (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path === '' ? '/' : $path,
            (string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''),
            (string) (file_get_contents('php://input') ?: ''),
            array_merge(self::extractHeaders(), ['X-Client-IP' => (string) ($_SERVER['REMOTE_ADDR'] ?? '')]), $query, $ambiguous
        );
    }
    public function getQuery(string $name): ?string { return $this->query[$name] ?? null; }
    public function hasAmbiguousQuery(): bool { return $this->ambiguousQuery; }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getClientIp(): string
    {
        return (string) ($this->headers['x-client-ip'] ?? '');
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    /**
     * @return array<string, string>
     */
    private static function extractHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') !== 0 || !is_string($value)) {
                continue;
            }

            $headers[strtolower(str_replace('_', '-', substr($key, 5)))] = $value;
        }

        // Apache can expose Authorization only through getallheaders(), rather
        // than HTTP_AUTHORIZATION. Use it when the SAPI provides it.
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                if (is_string($name) && is_string($value)) {
                    $headers[strtolower($name)] = $value;
                }
            }
        }

        // PHP exposes entity headers outside the HTTP_* family on Apache/FastCGI.
        if (isset($_SERVER['CONTENT_TYPE']) && is_string($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }

        return $headers;
    }
}
