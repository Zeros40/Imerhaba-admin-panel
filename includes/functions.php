<?php
// Helper functions

/**
 * Escape HTML output
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Get setting value
 */
function getSetting($key, $default = null) {
    try {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT setting_value, setting_type FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $setting = $stmt->fetch();

        if (!$setting) {
            return $default;
        }

        $value = $setting['setting_value'];

        // Cast to appropriate type
        switch ($setting['setting_type']) {
            case 'number':
                return is_numeric($value) ? (float)$value : $default;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true) ?? $default;
            default:
                return $value;
        }
    } catch (PDOException $e) {
        error_log("Get setting error: " . $e->getMessage());
        return $default;
    }
}

/**
 * Update setting value
 */
function updateSetting($key, $value) {
    try {
        $pdo = db();
        $stmt = $pdo->prepare("
            UPDATE settings
            SET setting_value = ?, updated_at = CURRENT_TIMESTAMP
            WHERE setting_key = ?
        ");
        return $stmt->execute([$value, $key]);
    } catch (PDOException $e) {
        error_log("Update setting error: " . $e->getMessage());
        return false;
    }
}

/**
 * Format date
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    if (!$date) return '-';
    try {
        return (new DateTime($date))->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Get user avatar URL
 */
function getAvatarUrl($avatar = null, $email = null) {
    if ($avatar) {
        return $avatar;
    }
    if ($email) {
        // Gravatar fallback
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }
    return '/assets/images/default-avatar.png';
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect with message
 */
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit;
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

/**
 * Get pagination data
 */
function paginate($totalItems, $currentPage = 1, $perPage = 10) {
    $totalPages = ceil($totalItems / $perPage);
    $currentPage = max(1, min($totalPages, $currentPage));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'total_items' => $totalItems,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
    ];
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Generate random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Truncate string
 */
function truncate($string, $length = 100, $suffix = '...') {
    if (mb_strlen($string) <= $length) {
        return $string;
    }
    return mb_substr($string, 0, $length) . $suffix;
}

/**
 * Get time ago
 */
function timeAgo($datetime) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    if ($diff < 31536000) return floor($diff / 2592000) . ' months ago';
    return floor($diff / 31536000) . ' years ago';
}
