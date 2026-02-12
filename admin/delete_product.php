<?php
// delete_product.php - Admin delete product
include('../includes/db.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = (int) $_GET['id'];

// fetch product to delete image
$stmt = $conn->prepare('SELECT image_url FROM products WHERE product_id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if ($p && !empty($p['image_url'])) {
    $path = __DIR__ . '/../images/' . $p['image_url'];
    if (file_exists($path)) {
        @unlink($path);
    }
}

$del = $conn->prepare('DELETE FROM products WHERE product_id = ?');
$del->execute([$id]);

header('Location: dashboard.php');
exit;
?>