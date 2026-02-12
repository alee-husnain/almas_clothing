<?php
// header.php - Header of the page
require_once(__DIR__ . '/../includes/db.php');

// Start session if not already started to avoid "session_start() because a session is already active" notices
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine current page filename for active link highlighting
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Detect if current page is an admin page (any page inside /admin/)
$isAdminPage = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;

// Include functions for cart count
require_once(__DIR__ . '/../includes/functions.php');

// Get cart count
if (isset($_SESSION['user_id'])) {
    // For logged-in users, count from database
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $cartCount = $result['count'];
} else {
    // For guest users, count from session
    $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almas Clothing Brand</title>
    <link rel="stylesheet" href="/almas_clothing/public/css/style.css?v=2.0">
</head>
<body>
    <header>
        <?php if ($isAdminPage): ?>
            <!-- Admin Header -->
            <div style="display:flex; justify-content:center; align-items:center; height:80px; background:#f5f5f5;">
                <h1 style="margin:0;">Admin Panel</h1>
            </div>
        <?php else: ?>
            <!-- Normal Header -->
            <div class="header-inner">
                <div class="logo-container">
                    <a href="../public/index.php" class="logo">
                        <img src="../images/logo.jpg" alt="Almas Clothing Brand">
                    </a>
                </div>

                <!-- Mobile menu toggle (visible on small screens) -->
                <button class="mobile-menu-toggle" aria-controls="mobile-menu" aria-expanded="false" aria-label="Open menu">
                    <span class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <nav>
                    <ul>
                        <li><a href="../public/index.php" class="nav-link<?php echo ($currentPage === 'index.php') ? ' active' : ''; ?>">Home</a></li>
                        <li><a href="../public/shop.php" class="nav-link<?php echo ($currentPage === 'shop.php') ? ' active' : ''; ?>">Shop</a></li>
                        <li><a href="../public/about.php" class="nav-link<?php echo ($currentPage === 'about.php') ? ' active' : ''; ?>">About Us</a></li>
                        <li><a href="../public/contact.php" class="nav-link<?php echo ($currentPage === 'contact.php') ? ' active' : ''; ?>">Contact</a></li>
                        <li><a href="../public/cart.php" class="nav-link<?php echo ($currentPage === 'cart.php') ? ' active' : ''; ?>">
                            Cart
                            <?php if ($cartCount > 0) { echo '<span class="cart-badge">' . $cartCount . '</span>'; } ?>
                        </a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="../public/logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="../public/login.php" class="nav-link<?php echo ($currentPage === 'login.php') ? ' active' : ''; ?>">Login</a></li>
                            <li><a href="../public/register.php" class="nav-link<?php echo ($currentPage === 'register.php') ? ' active' : ''; ?>">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>

            <!-- Mobile popup menu / overlay -->
            <div id="mobile-menu" class="mobile-menu" aria-hidden="true">
                <div class="mobile-menu-inner">
                    <button class="mobile-menu-close" aria-label="Close menu">&times;</button>
                    <ul class="mobile-nav-list">
                        <li><a href="../public/index.php" class="nav-link<?php echo ($currentPage === 'index.php') ? ' active' : ''; ?>">Home</a></li>
                        <li><a href="../public/shop.php" class="nav-link<?php echo ($currentPage === 'shop.php') ? ' active' : ''; ?>">Shop</a></li>
                        <li><a href="../public/about.php" class="nav-link<?php echo ($currentPage === 'about.php') ? ' active' : ''; ?>">About Us</a></li>
                        <li><a href="../public/contact.php" class="nav-link<?php echo ($currentPage === 'contact.php') ? ' active' : ''; ?>">Contact</a></li>
                        <li><a href="../public/cart.php" class="cart-link nav-link<?php echo ($currentPage === 'cart.php') ? ' active' : ''; ?>">
                            <?php echo "Cart ($cartCount)"; ?>
                        </a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="../public/logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="../public/login.php" class="nav-link<?php echo ($currentPage === 'login.php') ? ' active' : ''; ?>">Login</a></li>
                            <li><a href="../public/register.php" class="nav-link<?php echo ($currentPage === 'register.php') ? ' active' : ''; ?>">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="mobile-menu-overlay" tabindex="-1" aria-hidden="true"></div>
        <?php endif; ?>
    </header>
