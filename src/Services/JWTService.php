<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private string $secret;
    private string $algorithm;
    private int $expiry;

    public function __construct(string $secret, string $algorithm = 'HS256', int $expiry = 3600)
    {
        $this->secret = $secret;
        $this->algorithm = $algorithm;
        $this->expiry = $expiry;
    }

    public function generateToken(array $payload): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->expiry;

        $payload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
        ]);

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function verifyToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return (array)$decoded;
        } catch (\Exception) {
            return null;
        }
    }

    public function getTokenFromRequest(): ?string
    {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            $parts = explode(' ', $headers['Authorization']);
            if (count($parts) === 2 && strtolower($parts[0]) === 'bearer') {
                return $parts[1];
            }
        }

        return $_GET['token'] ?? null;
    }

    public function refreshToken(array $payload): string
    {
        unset($payload['iat'], $payload['exp']);
        return $this->generateToken($payload);
    }
}
