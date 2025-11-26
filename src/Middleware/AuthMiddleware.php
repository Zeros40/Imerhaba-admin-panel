<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Request;
use App\Response;
use App\Services\JWTService;
use App\Services\AuthService;

class AuthMiddleware
{
    private JWTService $jwt;
    private AuthService $auth;

    public function __construct(JWTService $jwt, AuthService $auth)
    {
        $this->jwt = $jwt;
        $this->auth = $auth;
    }

    public function handle(Request $request): ?Response
    {
        $token = $this->jwt->getTokenFromRequest();

        if (!$token) {
            return Response::unauthorized('Authentication token required');
        }

        $user = $this->auth->authenticate($token);

        if (!$user) {
            return Response::unauthorized('Invalid or expired token');
        }

        // Store user in request for later use
        $request->setAttribute('user', $user);
        $request->setAttribute('token', $token);

        return null; // Continue
    }
}
