<?php
// Redirect to dashboard if logged in, otherwise to login page
require_once __DIR__ . '/includes/auth.php';

Auth::init();

if (Auth::check()) {
    header('Location: /dashboard');
} else {
    header('Location: /login');
}
exit;
