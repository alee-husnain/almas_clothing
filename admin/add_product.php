<?php
// add_product.php - Page for admin to add new products

include('../includes/db.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Load existing categories for the form datalist
$catStmt = $conn->prepare("SELECT name FROM categories ORDER BY name ASC");
$catStmt->execute();
$all_categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];
    $price = $_POST['price'];
    // accept category name from admin (create if not exists)
    $category_name = trim($_POST['category_name'] ?? '');
    $category_id = null;
    $description = $_POST['description'];
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['png','jpg','jpeg','svg','webp'];
        $tmp = $_FILES['image']['tmp_name'];
        $orig = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $upload_error = "Invalid image type. Allowed: " . implode(', ', $allowed);
        } else {
            // ensure unique filename
            $image = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $orig);
            if (!move_uploaded_file($tmp, __DIR__ . '/../images/' . $image)) {
                $upload_error = "Failed to move uploaded file.";
            }
        }
    }

    if (!isset($upload_error)) {
        // Resolve or create category by name
        if ($category_name !== '') {
            $s = $conn->prepare('SELECT category_id FROM categories WHERE name = ?');
            $s->execute([$category_name]);
            $row = $s->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $category_id = $row['category_id'];
            } else {
                $ins = $conn->prepare('INSERT INTO categories (name) VALUES (?)');
                $ins->execute([$category_name]);
                $category_id = $conn->lastInsertId();
            }
        }

        $stmt = $conn->prepare("INSERT INTO products (name, price, category_id, description, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $category_id, $description, $image]);
        header("Location: dashboard.php");
        exit;
    } else {
        $error = $upload_error;
    }
}
?>

<?php include('../templates/header.php'); ?>
<main>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Add New Product</h1>
            <?php if (isset($error)): ?>
                <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" id="name" name="name" placeholder="Product name" required>
                <input type="number" id="price" name="price" placeholder="Price" required step="0.01">
                <label for="category_name" style="font-size:0.9rem;color:#64748b;margin-bottom:6px;display:block">Category</label>
                <input list="categories" id="category_name" name="category_name" placeholder="Enter category name (existing or new)" required style="padding:10px;border-radius:6px;border:1px solid #e6e9ee;margin-bottom:8px;">
                <datalist id="categories">
                    <?php foreach ($all_categories as $cname): ?>
                        <option value="<?php echo htmlspecialchars($cname); ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <textarea id="description" name="description" placeholder="Description" required></textarea>
                <input type="file" id="image" name="image" accept="image/*">
                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>
</main>

<?php include('../templates/footer.php'); ?>
