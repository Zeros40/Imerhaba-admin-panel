<?php

declare(strict_types=1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/env.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

spl_autoload_register(function ($class) {
    $prefix = 'Database\\';
    $baseDir = __DIR__ . '/database/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Request;
use App\Router;
use App\Database;
use App\Response;
use App\Services\AIManager;
use App\Services\CodeGenerationService;
use App\Services\JWTService;
use App\Services\AuthService;

// Load configuration
$config = require __DIR__ . '/config/env.php';

// Initialize dependencies
$db = Database::getInstance($config['db']);
$jwt = new JWTService($config['jwt']['secret'], $config['jwt']['algorithm'], $config['jwt']['expiry']);
$auth = new AuthService($db, $jwt);
$aiManager = new AIManager($config);
$codeGen = new CodeGenerationService($aiManager, $db);

// Create request and router
$request = new Request();
$router = new Router($request);

// Helper function to check authentication
$requireAuth = function (Request $request) use ($jwt, $auth) {
    $token = $jwt->getTokenFromRequest();
    if (!$token) {
        return false;
    }

    $user = $auth->authenticate($token);
    if (!$user) {
        return false;
    }

    $request->setAttribute('user', $user);
    $request->setAttribute('token', $token);
    return true;
};

// Setup CORS headers
header('Access-Control-Allow-Origin: ' . getenv('CORS_ORIGIN') ?: '*');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($request->isMethod('OPTIONS')) {
    http_response_code(200);
    exit;
}

// Public API Routes
// Authentication
$router->post('/api/v1/auth/register', function ($request) use ($auth) {
    $controller = new \App\Controllers\AuthController($auth);
    return $controller->register($request);
});

$router->post('/api/v1/auth/login', function ($request) use ($auth) {
    $controller = new \App\Controllers\AuthController($auth);
    return $controller->login($request);
});

// Protected API Routes
// User Management
$router->get('/api/v1/user/profile', function ($request) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\UserController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->getProfile($request);
});

$router->put('/api/v1/user/profile', function ($request) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\UserController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->updateProfile($request);
});

$router->get('/api/v1/user/stats', function ($request) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\UserController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->getStats($request);
});

$router->get('/api/v1/user/usage', function ($request) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\UserController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->getUsageStats($request);
});

$router->post('/api/v1/auth/change-password', function ($request) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\AuthController($auth);
    return $controller->changePassword($request);
});

$router->post('/api/v1/auth/logout', function ($request) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\AuthController($auth);
    return $controller->logout($request);
});

// Code Generation
$router->post('/api/v1/generation/generate', function ($request) use ($requireAuth, $codeGen) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\GenerationController($codeGen);
    return $controller->generateApp($request);
});

$router->post('/api/v1/generation/{projectId}/regenerate', function ($request, $projectId) use ($requireAuth, $codeGen) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\GenerationController($codeGen);
    return $controller->regenerate($request, $projectId);
});

$router->post('/api/v1/generation/{projectId}/refine', function ($request, $projectId) use ($requireAuth, $codeGen) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\GenerationController($codeGen);
    return $controller->refine($request, $projectId);
});

// Project Management
$router->get('/api/v1/projects', function ($request) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\ProjectController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->listProjects($request);
});

$router->get('/api/v1/projects/{projectId}', function ($request, $projectId) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\ProjectController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->getProject($request, $projectId);
});

$router->put('/api/v1/projects/{projectId}', function ($request, $projectId) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\ProjectController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->updateProject($request, $projectId);
});

$router->delete('/api/v1/projects/{projectId}', function ($request, $projectId) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\ProjectController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->deleteProject($request, $projectId);
});

$router->get('/api/v1/projects/{projectId}/export', function ($request, $projectId) use ($requireAuth) {
    if (!$requireAuth($request)) {
        return Response::unauthorized();
    }
    $controller = new \App\Controllers\ProjectController(Database::getInstance(require __DIR__ . '/config/env.php')['db']);
    return $controller->exportProject($request, $projectId);
});

// Health check
$router->get('/api/v1/health', function ($request) {
    return Response::success(['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')]);
});

// Dispatch request
$response = $router->dispatch();
if ($response === null) {
    $response = Response::notFound('Route not found');
}

$response->send();
