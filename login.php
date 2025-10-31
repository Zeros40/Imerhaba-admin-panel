<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

Auth::init();

// If already logged in, redirect to dashboard
if (Auth::check()) {
    redirect('/dashboard');
}

$error = null;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($identifier) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $result = Auth::login($identifier, $password);
        if ($result['success']) {
            redirect('/dashboard', 'Welcome back!', 'success');
        } else {
            $error = $result['error'];
        }
    }
}

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= e(getSetting('site_name', 'Imerhaba Admin Panel')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
            --error: #ef4444;
            --success: #10b981;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 440px;
            padding: 48px;
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo h1 {
            color: var(--primary);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .logo p {
            color: var(--text-light);
            font-size: 14px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            color: var(--error);
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: var(--success);
            border: 1px solid #cfc;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: 400;
            font-size: 14px;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: var(--text-light);
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0;
            color: var(--text-light);
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border);
        }

        .divider span {
            padding: 0 12px;
        }

        .default-credentials {
            background: #f1f5f9;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 13px;
            color: var(--text-dark);
        }

        .default-credentials strong {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
        }

        .default-credentials code {
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Imerhaba</h1>
            <p>Admin Panel</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="default-credentials">
            <strong>Default Login Credentials:</strong>
            Username: <code>admin</code><br>
            Password: <code>admin123</code>
        </div>

        <form method="POST" action="/login">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username or email"
                    required
                    autofocus
                    value="<?= e($_POST['username'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn">Sign In</button>
        </form>

        <div class="footer">
            <p>Powered by Imerhaba Admin Panel</p>
        </div>
    </div>
</body>
</html>
