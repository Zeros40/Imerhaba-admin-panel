<?php
// Authentication and session management

require_once __DIR__ . '/../db/config.php';

class Auth {
    private static $sessionLifetime = 3600; // 1 hour default

    /**
     * Start secure session
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
            ini_set('session.use_strict_mode', 1);
            session_start();
        }

        // Check if session is expired
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > self::$sessionLifetime)) {
            self::logout();
            return false;
        }
        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * Login user with username/email and password
     */
    public static function login($identifier, $password) {
        try {
            $pdo = db();

            // Find user by username or email
            $stmt = $pdo->prepare("
                SELECT * FROM users
                WHERE (username = ? OR email = ?)
                AND status = 'active'
                LIMIT 1
            ");
            $stmt->execute([$identifier, $identifier]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                self::logActivity(null, 'login_failed', 'user', null, "Failed login attempt for: $identifier");
                return ['success' => false, 'error' => 'Invalid credentials'];
            }

            // Create session
            self::createSession($user);

            // Update last login
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);

            // Log activity
            self::logActivity($user['id'], 'login', 'user', $user['id'], 'User logged in');

            return ['success' => true, 'user' => self::sanitizeUser($user)];

        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error'];
        }
    }

    /**
     * Create user session
     */
    private static function createSession($user) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();

        // Store session in database
        try {
            $pdo = db();
            $sessionId = session_id();
            $expiresAt = date('Y-m-d H:i:s', time() + self::$sessionLifetime);

            $stmt = $pdo->prepare("
                INSERT INTO sessions (id, user_id, ip_address, user_agent, expires_at)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    last_activity = CURRENT_TIMESTAMP,
                    expires_at = ?
            ");
            $stmt->execute([
                $sessionId,
                $user['id'],
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $expiresAt,
                $expiresAt
            ]);
        } catch (PDOException $e) {
            error_log("Session storage error: " . $e->getMessage());
        }
    }

    /**
     * Logout user
     */
    public static function logout() {
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];

            // Remove session from database
            try {
                $pdo = db();
                $stmt = $pdo->prepare("DELETE FROM sessions WHERE id = ?");
                $stmt->execute([session_id()]);

                // Log activity
                self::logActivity($userId, 'logout', 'user', $userId, 'User logged out');
            } catch (PDOException $e) {
                error_log("Logout error: " . $e->getMessage());
            }
        }

        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }

    /**
     * Check if user is logged in
     */
    public static function check() {
        self::init();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? null,
            'first_name' => $_SESSION['first_name'] ?? null,
            'last_name' => $_SESSION['last_name'] ?? null,
            'avatar' => $_SESSION['avatar'] ?? null,
        ];
    }

    /**
     * Require authentication (redirect if not logged in)
     */
    public static function require($redirect = '/login') {
        if (!self::check()) {
            header("Location: $redirect");
            exit;
        }
    }

    /**
     * Require specific role
     */
    public static function requireRole($role, $redirect = '/dashboard') {
        self::require();
        if ($_SESSION['role'] !== $role && $_SESSION['role'] !== 'admin') {
            header("Location: $redirect");
            exit;
        }
    }

    /**
     * Check if user has role
     */
    public static function hasRole($role) {
        if (!self::check()) {
            return false;
        }
        return $_SESSION['role'] === $role || $_SESSION['role'] === 'admin';
    }

    /**
     * Clean up expired sessions
     */
    public static function cleanupSessions() {
        try {
            $pdo = db();
            $stmt = $pdo->prepare("DELETE FROM sessions WHERE expires_at < NOW()");
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Session cleanup error: " . $e->getMessage());
        }
    }

    /**
     * Log user activity
     */
    public static function logActivity($userId, $action, $entityType = null, $entityId = null, $description = null) {
        try {
            $pdo = db();
            $stmt = $pdo->prepare("
                INSERT INTO activity_log (user_id, action, entity_type, entity_id, description, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                $entityType,
                $entityId,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Remove sensitive data from user object
     */
    private static function sanitizeUser($user) {
        unset($user['password']);
        return $user;
    }

    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
