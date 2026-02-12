<?php
// create_admin.php - Create initial admin or allow existing admin to add new admins

include('../includes/db.php');
session_start();

// Include header
// include('../templates/header.php');

// Count existing admins
$countStmt = $conn->prepare("SELECT COUNT(*) FROM admins");
$countStmt->execute();
$adminCount = (int)$countStmt->fetchColumn();

// If admins exist and user is not logged in as admin, redirect to login
// if ($adminCount > 0 && !isset($_SESSION['admin_id'])) {
//     header("Location: admin_login.php");
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$email) {
        $error = 'Please provide a valid email.';
    } elseif (empty($password) || strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Ensure email isn't already used
        $check = $conn->prepare("SELECT * FROM admins WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'An admin with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
                $ins->execute([$email, $hash]);

                // Redirect to admin login after successful creation
                header('Location: admin_login.php?created=1');
                exit();
        }
    }
}
?>

<main>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Create Admin</h1>
            <?php if (isset($error)): ?>
                <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Admin email" required>
                <input type="password" name="password" placeholder="Password (min 6 chars)" required>
                <input type="password" name="confirm_password" placeholder="Confirm password" required>
                <button type="submit">Create Admin</button>
            </form>
            <div style="margin-top:12px; text-align:center;">
                <a href="admin_login.php" style=" text-decoration: none">Back to Login</a>
            </div>
        </div>
    </div>
</main>

<!-- <?php include('../templates/footer.php'); ?> -->
