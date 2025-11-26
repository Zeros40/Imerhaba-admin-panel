<?php

declare(strict_types=1);

namespace App;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private mixed $data = null;
    private ?string $message = null;
    private bool $success = true;

    public function __construct(mixed $data = null, int $statusCode = 200, ?string $message = null)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->success = $statusCode >= 200 && $statusCode < 300;
        $this->headers['Content-Type'] = 'application/json';
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        $this->success = $code >= 200 && $code < 300;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public static function success(mixed $data = null, int $statusCode = 200, ?string $message = null): self
    {
        return new self($data, $statusCode, $message ?? 'Success');
    }

    public static function created(mixed $data = null, ?string $message = null): self
    {
        return new self($data, 201, $message ?? 'Resource created successfully');
    }

    public static function error(string $message, int $statusCode = 400, mixed $data = null): self
    {
        $response = new self($data, $statusCode, $message);
        $response->success = false;
        return $response;
    }

    public static function notFound(string $message = 'Resource not found'): self
    {
        return self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }

    public static function badRequest(string $message = 'Bad request'): self
    {
        return self::error($message, 400);
    }

    public static function internalError(string $message = 'Internal server error'): self
    {
        return self::error($message, 500);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
