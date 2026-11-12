<?php

declare(strict_types=1);

namespace RestApi\Core;

class Request
{
    private string $method;
    private string $path;
    private string $host;

    /** @var array<string, mixed> */
    private array $queryParams;

    /** @var array<string, string> */
    private array $headers;

    /** @var array<string, mixed> */
    private array $attributes;

    private ?string $rawBody;
    private string $queryString;

    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, string> $headers
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        string $method,
        string $path,
        string $host,
        array $queryParams,
        array $headers,
        array $attributes = [],
        ?string $rawBody = null,
        string $queryString = ''
    )
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->host = $host;
        $this->queryParams = $queryParams;
        $this->headers = $headers;
        $this->attributes = $attributes;
        $this->rawBody = $rawBody;
        $this->queryString = $queryString;
    }

    public static function capture(): self
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = (string) parse_url($requestUri, PHP_URL_PATH);
        $path = $path === '' ? '/' : $path;
        $queryString = (string) parse_url($requestUri, PHP_URL_QUERY);
        $host = (string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');

        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $path,
            $host,
            $_GET,
            self::extractHeaders(),
            [],
            file_get_contents('php://input') ?: null,
            $queryString
        );
    }

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

    /**
     * @return array<string, mixed>
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        $normalized = strtolower($name);
        return $this->headers[$normalized] ?? null;
    }

    public function getIp(): string
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        return $ip === '' ? '0.0.0.0' : $ip;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getRawBody(): ?string
    {
        return $this->rawBody;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getJsonBody(): ?array
    {
        if ($this->rawBody === null || trim($this->rawBody) === '') {
            return null;
        }

        $decoded = json_decode($this->rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('INVALID_JSON', 'JSON inválido.', 400);
        }

        if (!is_array($decoded)) {
            throw new ApiException('INVALID_JSON', 'JSON inválido.', 400);
        }

        return $decoded;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute(string $key, $default = null)
    {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : $default;
    }

    /**
     * @param mixed $value
     */
    public function setAttribute(string $key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getRouteParam(string $name, ?string $default = null): ?string
    {
        $routeParams = $this->getAttribute('route_params', []);
        if (!is_array($routeParams)) {
            return $default;
        }

        return isset($routeParams[$name]) ? (string) $routeParams[$name] : $default;
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

            $headerName = str_replace('_', '-', substr($key, 5));
            $headers[strtolower($headerName)] = $value;
        }

        $authorizationFallbacks = [
            $_SERVER['HTTP_AUTHORIZATION'] ?? null,
            $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null,
        ];

        foreach ($authorizationFallbacks as $authorizationValue) {
            if (is_string($authorizationValue) && trim($authorizationValue) !== '') {
                $headers['authorization'] = $authorizationValue;
                break;
            }
        }

        if (function_exists('getallheaders')) {
            $rawHeaders = getallheaders();
            if (is_array($rawHeaders)) {
                foreach ($rawHeaders as $name => $value) {
                    if (!is_string($name) || !is_string($value)) {
                        continue;
                    }

                    $headers[strtolower($name)] = $value;
                }
            }
        }

        if (function_exists('apache_request_headers')) {
            $rawHeaders = apache_request_headers();
            if (is_array($rawHeaders)) {
                foreach ($rawHeaders as $name => $value) {
                    if (!is_string($name) || !is_string($value)) {
                        continue;
                    }

                    $headers[strtolower($name)] = $value;
                }
            }
        }

        return $headers;
    }
}
