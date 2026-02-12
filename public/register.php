<?php
// register.php - User registration page

include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Hash password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password]);

        header("Location: login.php");
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<main>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Create your account</h1>
            <p class="lead">Sign up to track orders, save favorites and check out faster.</p>

            <?php if (isset($error)): ?>
                <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <input type="text" id="name" name="name" placeholder="Full name" required>
                <input type="email" id="email" name="email" placeholder="Email address" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>

                <button type="submit">Register</button>
            </form>

            <p class="muted">Already have an account? <a href="login.php" style=" text-decoration: none">Login</a></p>
        </div>
    </div>
</main>
<?php include('../templates/footer.php'); ?>
