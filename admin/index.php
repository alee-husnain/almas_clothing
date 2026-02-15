<?php
// index.php - Admin login page (renamed from admin_login.php)
include('../includes/db.php');
session_start();

// If no admins exist yet, redirect to create_admin.php to initialize the first admin
$countStmt = $conn->prepare("SELECT COUNT(*) FROM admins");
$countStmt->execute();
$adminCount = (int)$countStmt->fetchColumn();
if ($adminCount === 0) {
    header("Location: create_admin.php");
    exit();
}

include('../templates/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        // Admin authenticated, set session and redirect
        $_SESSION['admin_id'] = $admin['admin_id'];
        header("Location: dashboard.php");
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<main>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Admin Login</h1>
            <?php if (isset($error)): ?>
                <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="email" id="email" name="email" placeholder="Admin email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <div style="margin-top:12px; text-align:center;">
                <a href="create_admin.php" style=" text-decoration: none">Create Admin</a>
            </div>
        </div>
    </div>
</main>

<!-- <?php include('../templates/footer.php'); ?> -->
