<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Services\AuthService;

class AuthController
{
    private AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function register(Request $request): Response
    {
        if (!$request->isPost()) {
            return Response::error('Method not allowed', 405);
        }

        $errors = $request->validate([
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $result = $this->auth->register(
            $request->input('username'),
            $request->input('email'),
            $request->input('password'),
            $request->input('first_name', ''),
            $request->input('last_name', '')
        );

        if (!$result['success']) {
            return Response::error($result['error'], 400);
        }

        return Response::created($result['user'], $result['message']);
    }

    public function login(Request $request): Response
    {
        if (!$request->isPost()) {
            return Response::error('Method not allowed', 405);
        }

        $errors = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $result = $this->auth->login(
            $request->input('email'),
            $request->input('password')
        );

        if (!$result['success']) {
            return Response::error($result['error'], 401);
        }

        return Response::success([
            'token' => $result['token'],
            'user' => $result['user'],
        ], 200, $result['message']);
    }

    public function changePassword(Request $request): Response
    {
        if (!$request->isPost()) {
            return Response::error('Method not allowed', 405);
        }

        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $errors = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8',
        ]);

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $result = $this->auth->changePassword(
            $user['id'],
            $request->input('old_password'),
            $request->input('new_password')
        );

        if (!$result['success']) {
            return Response::error($result['error'], 400);
        }

        return Response::success(null, 200, $result['message']);
    }

    public function logout(Request $request): Response
    {
        if (!$request->isPost()) {
            return Response::error('Method not allowed', 405);
        }

        $user = $request->getAttribute('user');
        $token = $request->getAttribute('token');

        if (!$user) {
            return Response::unauthorized();
        }

        $result = $this->auth->logout($user['id'], $token);
        return Response::success(null, 200, $result['message']);
    }
}
