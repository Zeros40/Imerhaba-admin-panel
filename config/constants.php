<?php

declare(strict_types=1);

// Application Constants
define('APP_ROOT', dirname(__DIR__));
define('APP_NAME', getenv('APP_NAME') ?: 'AI Agent Platform');
define('APP_ENV', getenv('APP_ENV') ?: 'local');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');

// Paths
define('STORAGE_PATH', APP_ROOT . '/storage');
define('LOGS_PATH', APP_ROOT . '/logs');
define('GENERATED_APPS_PATH', STORAGE_PATH . '/generated-apps');
define('UPLOADS_PATH', STORAGE_PATH . '/uploads');

// AI Models
define('AVAILABLE_AI_MODELS', [
    'gpt-4' => 'GPT-4 (OpenAI)',
    'gpt-4-turbo' => 'GPT-4 Turbo (OpenAI)',
    'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Anthropic)',
    'o1' => 'OpenAI o1',
    'o1-mini' => 'OpenAI o1-mini',
]);

// JWT
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'change-me-in-production');
define('JWT_ALGORITHM', getenv('JWT_ALGORITHM') ?: 'HS256');
define('JWT_EXPIRY', (int)(getenv('JWT_EXPIRY') ?: 3600));

// HTTP Status Codes
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_CONFLICT', 409);
define('HTTP_INTERNAL_ERROR', 500);

// API Response Types
define('RESPONSE_TYPE_JSON', 'json');
define('RESPONSE_TYPE_HTML', 'html');
