<?php

declare(strict_types=1);

namespace App;

class Request
{
    private array $query = [];
    private array $post = [];
    private array $files = [];
    private array $headers = [];
    private array $json = [];
    private array $attributes = [];
    private string $method;
    private string $path;
    private string $body = '';

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = $this->parsePath($_SERVER['REQUEST_URI'] ?? '/');
        $this->query = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->headers = $this->parseHeaders();
        $this->body = file_get_contents('php://input');

        if ($this->isJson()) {
            $this->json = json_decode($this->body, true) ?? [];
        }
    }

    private function parsePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = str_replace('/index.php', '', $path);
        return $path ?: '/';
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('HTTP_', '', $key);
                $headerName = str_replace('_', '-', strtolower($headerName));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isMethod(string $method): bool
    {
        return strtoupper($this->method) === strtoupper($method);
    }

    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function isPut(): bool
    {
        return $this->isMethod('PUT');
    }

    public function isDelete(): bool
    {
        return $this->isMethod('DELETE');
    }

    public function isPatch(): bool
    {
        return $this->isMethod('PATCH');
    }

    public function isJson(): bool
    {
        return strpos($this->getHeader('content-type', '') ?? '', 'application/json') !== false;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->json[$key] ?? $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->post, $this->json);
    }

    public function json(string $key = '', mixed $default = null): mixed
    {
        if (empty($key)) {
            return $this->json;
        }
        return $this->json[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function getHeader(string $key, mixed $default = null): mixed
    {
        $key = strtolower(str_replace('_', '-', $key));
        return $this->headers[$key] ?? $default;
    }

    public function hasHeader(string $key): bool
    {
        $key = strtolower(str_replace('_', '-', $key));
        return isset($this->headers[$key]);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function validate(array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $value = $this->input($field);

            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "$field is required";
            }

            if (strpos($rule, 'email') !== false && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "$field must be a valid email";
            }

            if (strpos($rule, 'min:') !== false) {
                preg_match('/min:(\d+)/', $rule, $matches);
                if (!empty($value) && strlen($value) < (int)$matches[1]) {
                    $errors[$field] = "$field must be at least {$matches[1]} characters";
                }
            }

            if (strpos($rule, 'max:') !== false) {
                preg_match('/max:(\d+)/', $rule, $matches);
                if (!empty($value) && strlen($value) > (int)$matches[1]) {
                    $errors[$field] = "$field must not exceed {$matches[1]} characters";
                }
            }
        }
        return $errors;
    }

    public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
