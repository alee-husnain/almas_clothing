<?php
// login.php - User login page

include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and authenticate user
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // User authenticated, start session
        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        
        // Merge any items from session cart into database cart
        merge_carts($conn, $user['user_id']);
        
        // Redirect to checkout if that's where user came from
        if (isset($_SESSION['return_to'])) {
            $return_to = $_SESSION['return_to'];
            unset($_SESSION['return_to']);
            header("Location: $return_to");
        } else {
            header("Location: /almas_clothing/index.php");
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<main>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Welcome back</h1>
            <p class="lead">Log in to access your account and manage orders.</p>

            <?php if (isset($error)): ?>
                <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <input type="email" id="email" name="email" placeholder="Email address" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

            <p class="muted">Don't have an account? <a href="register.php" style=" text-decoration: none">Register</a></p>
        </div>
    </div>
</main>
<?php include('../templates/footer.php'); ?>
