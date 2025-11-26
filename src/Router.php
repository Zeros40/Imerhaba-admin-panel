<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(string $path, callable|string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, callable|string $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, callable|string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|string $handler): void
    {
        $pattern = $this->convertPathToPattern($path);
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    private function convertPathToPattern(string $path): string
    {
        $pattern = preg_replace_callback('/\{([^}]+)\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $path);
        return '/^' . str_replace('/', '\/', $pattern) . '$/';
    }

    public function dispatch(): ?Response
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                return $this->executeHandler($route['handler'], $matches);
            }
        }

        return Response::notFound('Route not found');
    }

    private function executeHandler(callable|string $handler, array $params): ?Response
    {
        if (is_string($handler)) {
            [$class, $method] = explode('@', $handler);
            $class = 'App\\Controllers\\' . $class;
            if (!class_exists($class)) {
                return Response::internalError("Controller $class not found");
            }
            $handler = [new $class(), $method];
        }

        // Filter params to only include named groups
        $namedParams = array_filter($params, function ($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);

        try {
            $response = call_user_func($handler, $this->request, ...$namedParams);
            return $response instanceof Response ? $response : Response::success($response);
        } catch (\Exception $e) {
            return Response::internalError($e->getMessage());
        }
    }

    public function match(string $path, string $method = 'GET'): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                return $route;
            }
        }
        return null;
    }
}
