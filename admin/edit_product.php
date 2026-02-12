<?php
// edit_product.php - Admin edit product
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

// fetch product
$stmt = $conn->prepare('SELECT * FROM products WHERE product_id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
	header('Location: dashboard.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = $_POST['name'];
	$price = $_POST['price'];
	$category_id = $_POST['category_id'];
	$description = $_POST['description'];

	// handle optional image
	if (!empty($_FILES['image']['name'])) {
		$allowed = ['png','jpg','jpeg','svg','webp'];
		$orig = basename($_FILES['image']['name']);
		$ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
		if (!in_array($ext, $allowed)) {
			$error = 'Invalid image type.';
		} else {
			$newname = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $orig);
			if (move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../images/' . $newname)) {
				// delete old image if exists
				if (!empty($product['image_url']) && file_exists(__DIR__ . '/../images/' . $product['image_url'])) {
					@unlink(__DIR__ . '/../images/' . $product['image_url']);
				}
				$image_url = $newname;
			} else {
				$error = 'Failed to upload image.';
			}
		}
	} else {
		$image_url = $product['image_url'];
	}

	if (!isset($error)) {
		$u = $conn->prepare('UPDATE products SET name=?, price=?, category_id=?, description=?, image_url=? WHERE product_id=?');
		$u->execute([$name, $price, $category_id, $description, $image_url, $id]);
		header('Location: dashboard.php');
		exit;
	}
}

// include('../templates/header.php');
?>
<main>
	<div class="auth-wrapper">
		<div class="auth-card">
			<h1>Edit Product</h1>
			<?php if (isset($error)): ?>
				<div class="notice error"><?php echo htmlspecialchars($error); ?></div>
			<?php endif; ?>
			<form method="POST" enctype="multipart/form-data">
				<input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
				<input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
				<input type="number" name="category_id" value="<?php echo htmlspecialchars($product['category_id']); ?>" required>
				<textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
				<p>Current image:</p>
				<?php if (!empty($product['image_url'])): ?>
					<img src="../images/<?php echo htmlspecialchars($product['image_url']); ?>" alt="" style="max-width:140px; display:block; margin-bottom:8px;">
				<?php endif; ?>
				<input type="file" name="image" accept="image/*">
				<button type="submit">Save Changes</button>
			</form>
		</div>
	</div>
</main>

<?php include('../templates/footer.php'); ?>

