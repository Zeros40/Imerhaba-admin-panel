<?php

declare(strict_types=1);

namespace App\Services;

use App\Database;

class AuthService
{
    private Database $db;
    private JWTService $jwt;

    public function __construct(Database $db, JWTService $jwt)
    {
        $this->db = $db;
        $this->jwt = $jwt;
    }

    public function register(string $username, string $email, string $password, string $firstName = '', string $lastName = ''): array
    {
        // Validate inputs
        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Username, email, and password are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }

        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters'];
        }

        // Check if user already exists
        $existing = $this->db->findOne('SELECT id FROM users WHERE email = ? OR username = ?', [$email, $username]);
        if ($existing) {
            return ['success' => false, 'error' => 'Email or username already exists'];
        }

        try {
            $userId = $this->db->insert('users', [
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'is_active' => true,
            ]);

            $user = $this->db->findOne('SELECT id, username, email FROM users WHERE id = ?', [$userId]);

            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user' => $user,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    public function login(string $email, string $password): array
    {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Email and password are required'];
        }

        $user = $this->db->findOne('SELECT * FROM users WHERE email = ?', [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'error' => 'Invalid credentials'];
        }

        if (!$user['is_active']) {
            return ['success' => false, 'error' => 'Account is inactive'];
        }

        // Generate JWT token
        $token = $this->jwt->generateToken([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'username' => $user['username'],
        ]);

        // Record session
        try {
            $this->db->insert('user_sessions', [
                'user_id' => $user['id'],
                'token_hash' => hash('sha256', $token),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'expires_at' => date('Y-m-d H:i:s', time() + 3600),
            ]);
        } catch (\Exception) {
            // Silently fail session recording
        }

        return [
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
            ],
        ];
    }

    public function authenticate(?string $token): ?array
    {
        if (empty($token)) {
            return null;
        }

        $decoded = $this->jwt->verifyToken($token);
        if (!$decoded || !isset($decoded['user_id'])) {
            return null;
        }

        // Verify session exists
        $session = $this->db->findOne(
            'SELECT id FROM user_sessions WHERE user_id = ? AND token_hash = ? AND expires_at > NOW()',
            [$decoded['user_id'], hash('sha256', $token)]
        );

        if (!$session) {
            return null;
        }

        // Get user
        $user = $this->db->findOne('SELECT * FROM users WHERE id = ?', [$decoded['user_id']]);
        if (!$user || !$user['is_active']) {
            return null;
        }

        return [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'plan' => $user['plan'],
        ];
    }

    public function changePassword(int $userId, string $oldPassword, string $newPassword): array
    {
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'error' => 'New password must be at least 8 characters'];
        }

        $user = $this->db->findOne('SELECT password FROM users WHERE id = ?', [$userId]);
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'error' => 'Current password is incorrect'];
        }

        $this->db->update('users', [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ], 'id = ?', [$userId]);

        return ['success' => true, 'message' => 'Password changed successfully'];
    }

    public function logout(int $userId, ?string $token): array
    {
        if ($token) {
            $this->db->delete('user_sessions', 'user_id = ? AND token_hash = ?', [
                $userId,
                hash('sha256', $token),
            ]);
        }

        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    public function logoutAll(int $userId): array
    {
        $this->db->delete('user_sessions', 'user_id = ?', [$userId]);
        return ['success' => true, 'message' => 'Logged out from all devices'];
    }
}
