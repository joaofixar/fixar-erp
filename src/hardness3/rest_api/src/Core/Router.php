<?php

declare(strict_types=1);

namespace RestApi\Core;

use RuntimeException;

class Router
{
    /**
     * @var array<string, array<int, array{path:string, handler:callable, regex:string, paramNames:array<int, string>}>>
     */
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $normalizedMethod = strtoupper($method);
        $normalizedPath = $this->normalizePath($path);
        $paramNames = [];
        $regex = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $matches) use (&$paramNames): string {
                $paramNames[] = $matches[1];
                return '([^/]+)';
            },
            $normalizedPath
        );

        if (!is_string($regex)) {
            throw new RuntimeException('Invalid route pattern.', 500);
        }

        $this->routes[$normalizedMethod][] = [
            'path' => $normalizedPath,
            'handler' => $handler,
            'regex' => '#^' . $regex . '$#',
            'paramNames' => $paramNames,
        ];
    }

    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function dispatch(Request $request): array
    {
        $method = $request->getMethod();
        $path = $this->normalizePath($request->getPath());

        if (!isset($this->routes[$method])) {
            throw new RuntimeException('Route not found.', 404);
        }

        foreach ($this->routes[$method] as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $routeParams = [];
            foreach ($route['paramNames'] as $index => $paramName) {
                $routeParams[$paramName] = urldecode($matches[$index + 1] ?? '');
            }

            $request->setAttribute('route_params', $routeParams);
            $handler = $route['handler'];
            return $handler($request);
        }

        if ($path === '/api/rel' || strpos($path, '/api/rel/') === 0) {
            throw new RuntimeException('Route not found.', 404);
        }

        throw new RuntimeException('Route not found.', 404);
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        $normalized = '/' . trim($path, '/');
        return $normalized === '/' ? $normalized : rtrim($normalized, '/');
    }
}
