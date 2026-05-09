<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->map('PUT', $path, $handler);
    }

    public function patch(string $path, callable $handler): void
    {
        $this->map('PATCH', $path, $handler);
    }

    private function map(string $method, string $path, callable $handler): void
    {
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $path);
        $this->routes[] = [
            'method' => $method,
            'pattern' => '#^' . $pattern . '$#',
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            if (!preg_match($route['pattern'], $request->path(), $matches)) {
                continue;
            }

            $params = array_filter(
                $matches,
                static fn ($key): bool => !is_int($key),
                ARRAY_FILTER_USE_KEY
            );

            if ($params === []) {
                ($route['handler'])();
            } else {
                ($route['handler'])($params);
            }
            return;
        }

        Response::error('Route introuvable.', 404);
    }
}
