<?php
require_once __DIR__ . '/includes/auth.php';

Auth::logout();
redirect('/login', 'You have been logged out successfully', 'success');
