<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$rootPath = dirname(__DIR__);

// Load .env file
if (file_exists($rootPath . '/.env')) {
    $dotenv = Dotenv::createImmutable($rootPath);
    $dotenv->load();
}

// Ensure required environment variables are set
$required = [
    'DB_HOST', 'DB_NAME', 'DB_USER',
    'OPENAI_API_KEY', 'ANTHROPIC_API_KEY'
];

$missing = [];
foreach ($required as $var) {
    if (!getenv($var)) {
        $missing[] = $var;
    }
}

if (!empty($missing)) {
    if (getenv('APP_ENV') === 'production') {
        throw new RuntimeException('Missing required environment variables: ' . implode(', ', $missing));
    }
}

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => (int)(getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'ai_agent_platform',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'openai' => [
        'api_key' => getenv('OPENAI_API_KEY') ?: '',
        'org_id' => getenv('OPENAI_ORG_ID') ?: '',
        'gpt4_model' => getenv('GPT4_MODEL') ?: 'gpt-4',
        'o1_model' => getenv('O1_MODEL') ?: 'o1',
        'o1_mini_model' => getenv('O1_MINI_MODEL') ?: 'o1-mini',
    ],
    'anthropic' => [
        'api_key' => getenv('ANTHROPIC_API_KEY') ?: '',
        'model' => getenv('CLAUDE_MODEL') ?: 'claude-3-5-sonnet-20241022',
    ],
    'app' => [
        'name' => getenv('APP_NAME') ?: 'AI Agent Platform',
        'env' => getenv('APP_ENV') ?: 'local',
        'debug' => getenv('APP_DEBUG') === 'true',
        'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    ],
    'generation' => [
        'max_tokens' => (int)(getenv('APP_GENERATION_MAX_TOKENS') ?: 4000),
        'temperature' => (float)(getenv('APP_GENERATION_TEMPERATURE') ?: 0.7),
        'default_model' => getenv('DEFAULT_AI_MODEL') ?: 'gpt-4',
    ],
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?: 'change-me-in-production',
        'algorithm' => getenv('JWT_ALGORITHM') ?: 'HS256',
        'expiry' => (int)(getenv('JWT_EXPIRY') ?: 3600),
    ],
];
