<?php
if (!defined('REQUIRE_AUTH')) {
    define('REQUIRE_AUTH', true);
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

if (REQUIRE_AUTH) {
    Auth::require();
}

$user = Auth::user();
$currentPage = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= e(getSetting('site_name', 'Imerhaba Admin Panel')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
            --sidebar-width: 260px;
            --header-height: 70px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-white);
            border-right: 1px solid var(--border);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-header h2 {
            color: var(--primary);
            font-size: 24px;
            font-weight: 700;
        }

        .sidebar-header p {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 4px;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-section-title {
            padding: 0 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-light);
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .nav-item {
            display: block;
            padding: 12px 20px;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .nav-item:hover {
            background: var(--bg-light);
            color: var(--primary);
        }

        .nav-item.active {
            background: linear-gradient(90deg, var(--primary) 0%, transparent 100%);
            background-size: 4px 100%;
            background-repeat: no-repeat;
            color: var(--primary);
            font-weight: 500;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            height: var(--header-height);
            background: var(--bg-white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-left h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.2s;
            position: relative;
        }

        .user-menu:hover {
            background: var(--bg-light);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .user-role {
            font-size: 12px;
            color: var(--text-light);
        }

        /* Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            display: none;
            z-index: 1001;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 12px 16px;
            color: var(--text-dark);
            text-decoration: none;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item:hover {
            background: var(--bg-light);
        }

        .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        /* Content Area */
        .content {
            padding: 32px;
            flex: 1;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert i {
            font-size: 18px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        .card-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--text-light);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Imerhaba</h2>
                <p>Admin Panel</p>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="/dashboard" class="nav-item <?= strpos($currentPage, '/dashboard') !== false ? 'active' : '' ?>">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="/users" class="nav-item <?= strpos($currentPage, '/users') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                    <a href="/contact-messages" class="nav-item <?= strpos($currentPage, '/contact-messages') !== false ? 'active' : '' ?>">
                        <i class="fas fa-envelope"></i>
                        <span>Contact Messages</span>
                    </a>
                    <a href="/activity-log" class="nav-item <?= strpos($currentPage, '/activity-log') !== false ? 'active' : '' ?>">
                        <i class="fas fa-history"></i>
                        <span>Activity Log</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <a href="/settings" class="nav-item <?= strpos($currentPage, '/settings') !== false ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="/profile" class="nav-item <?= strpos($currentPage, '/profile') !== false ? 'active' : '' ?>">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <h1><?= e($pageTitle ?? 'Dashboard') ?></h1>
                </div>

                <div class="header-right">
                    <div class="dropdown user-menu">
                        <div class="user-avatar">
                            <?php if ($user['avatar']): ?>
                                <img src="<?= e(getAvatarUrl($user['avatar'], $user['email'])) ?>" alt="<?= e($user['username']) ?>">
                            <?php else: ?>
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?= e($user['first_name'] ?? $user['username']) ?></div>
                            <div class="user-role"><?= e(ucfirst($user['role'])) ?></div>
                        </div>
                        <i class="fas fa-chevron-down"></i>

                        <div class="dropdown-menu">
                            <a href="/profile" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>My Profile</span>
                            </a>
                            <a href="/settings" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                            <a href="/logout" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content">
                <?php
                $flash = getFlashMessage();
                if ($flash):
                    $iconMap = [
                        'success' => 'check-circle',
                        'error' => 'exclamation-circle',
                        'warning' => 'exclamation-triangle',
                        'info' => 'info-circle',
                    ];
                    $icon = $iconMap[$flash['type']] ?? 'info-circle';
                ?>
                    <div class="alert alert-<?= e($flash['type']) ?>">
                        <i class="fas fa-<?= e($icon) ?>"></i>
                        <span><?= e($flash['message']) ?></span>
                    </div>
                <?php endif; ?>
