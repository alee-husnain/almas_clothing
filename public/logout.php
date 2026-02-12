<?php
// public/logout.php - user logout handler

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear cart and user session
$_SESSION = [];

// Delete the session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to login page (absolute path)
header('Location: /almas_clothing/public/index.php');
exit;
?>