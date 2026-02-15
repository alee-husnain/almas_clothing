<?php
// logout.php - admin logout
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
// Unset all session variables and destroy
$_SESSION = [];
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'], $params['domain'], $params['secure'], $params['httponly']
	);
}
session_unset();
session_destroy();

// Redirect to admin login (absolute path)
header('Location: /almas_clothing/admin/index.php');
exit;
?>