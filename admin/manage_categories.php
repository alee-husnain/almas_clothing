<?php
// manage_categories.php - Admin categories management

session_start();
include('../includes/db.php');

// Require admin
if (!isset($_SESSION['admin_id'])) {
	header('Location: admin_login.php');
	exit();
}

$message = '';
$error = '';

// Check if coming back from successful update
if (isset($_GET['success'])) {
	$message = 'Category updated successfully.';
}

// Handle actions: add, update, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'] ?? '';

	if ($action === 'add') {
		$name = trim($_POST['name'] ?? '');
		if ($name === '') {
			$error = 'Category name cannot be empty.';
		} else {
			$stmt = $conn->prepare('INSERT INTO categories (name) VALUES (?)');
			try {
				$stmt->execute([$name]);
				$message = 'Category added successfully.';
			} catch (Exception $e) {
				$error = 'Error adding category.';
			}
		}
	} elseif ($action === 'update') {
		$id = (int)($_POST['category_id'] ?? 0);
		$name = trim($_POST['name'] ?? '');
		if ($id <= 0 || $name === '') {
			$error = 'Invalid input for update.';
		} else {
			$stmt = $conn->prepare('UPDATE categories SET name = ? WHERE category_id = ?');
			try {
				$stmt->execute([$name, $id]);
				// Redirect to prevent form resubmission and refresh the data
				header('Location: manage_categories.php?success=1');
				exit();
			} catch (Exception $e) {
				$error = 'Error updating category: ' . $e->getMessage();
			}
		}
	} elseif ($action === 'delete') {
		$id = (int)($_POST['category_id'] ?? 0);
		if ($id <= 0) {
			$error = 'Invalid category.';
		} else {
			// Optional: check for products in this category before delete
			$stmt = $conn->prepare('SELECT COUNT(*) as cnt FROM products WHERE category_id = ?');
			$stmt->execute([$id]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row && $row['cnt'] > 0) {
				$error = 'Cannot delete category because products are assigned to it.';
			} else {
				$stmt = $conn->prepare('DELETE FROM categories WHERE category_id = ?');
				try {
					$stmt->execute([$id]);
					$message = 'Category deleted.';
				} catch (Exception $e) {
					$error = 'Error deleting category.';
				}
			}
		}
	}

	// reload categories after action
}

// Load categories
$stmt = $conn->prepare('SELECT * FROM categories ORDER BY name ASC');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If editing, load the category
$editCategory = null;
if (isset($_GET['edit'])) {
	$eid = (int)$_GET['edit'];
	if ($eid > 0) {
		$s2 = $conn->prepare('SELECT * FROM categories WHERE category_id = ?');
		$s2->execute([$eid]);
		$editCategory = $s2->fetch(PDO::FETCH_ASSOC);
	}
}

include('../templates/header.php');
?>
<link rel="stylesheet" href="../public/css/admin.css?v=2.0">

<main class="admin-dashboard">
	<div class="dashboard-header">
		<div class="dashboard-title">
			<h1>Manage Categories</h1>
			<h2>Organize categories used across the store</h2>
		</div>
		<div class="dashboard-actions">
			<a href="add_product.php" class="admin-btn secondary">Add Product</a>
			<a href="../admin/dashboard.php" class="admin-btn">Back to Dashboard</a>
		</div>
	</div>

	<?php if ($message): ?>
		<div class="notice success" style="margin:16px 0"><?php echo htmlspecialchars($message); ?></div>
	<?php endif; ?>
	<?php if ($error): ?>
		<div class="notice error" style="margin:16px 0"><?php echo htmlspecialchars($error); ?></div>
	<?php endif; ?>

	<div class="stats-grid">
		<div class="stat-card">
			<div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg></div>
			<div class="stat-content"><h4>Total Categories</h4><div class="stat-value"><?php echo count($categories); ?></div></div>
		</div>
	</div>

	<div class="quick-actions" style="margin-bottom:24px">
		<div class="quick-actions-grid">
			<div style="padding:16px">
				<form method="POST" style="display:flex;gap:8px;align-items:center">
					<input type="hidden" name="action" value="add">
					<input name="name" placeholder="New category name" style="padding:10px;border-radius:8px;border:1px solid #e2e8f0">
					<button class="admin-btn primary" type="submit">Add</button>
				</form>
			</div>
			<div style="padding:16px">
				<small class="muted">Tip: edit a category using the edit button in the list.</small>
			</div>
		</div>
	</div>

	<div class="products-section">
		<div class="section-header">
			<h3>Categories</h3>
		</div>
		<table class="admin-table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($categories as $cat): ?>
					<tr>
						<td class="product-name"><?php echo htmlspecialchars($cat['name']); ?></td>
						<td>
							<div class="table-actions">
								<a href="?edit=<?php echo $cat['category_id']; ?>#edit-form" class="admin-btn secondary">Edit</a>
								<form method="POST" style="display:inline" onsubmit="return confirm('Delete category?');">
									<input type="hidden" name="action" value="delete">
									<input type="hidden" name="category_id" value="<?php echo $cat['category_id']; ?>">
									<button type="submit" class="admin-btn danger">Delete</button>
								</form>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php if ($editCategory): ?>
		<div style="margin-top:24px" id="edit-form">
			<div class="section-header"><h3>Edit Category</h3></div>
			<div style="padding:18px;background:#fff;border-radius:12px;margin-top:12px;box-shadow:0 4px 6px rgba(0,0,0,0.04)">
				<form method="POST">
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="category_id" value="<?php echo $editCategory['category_id']; ?>">
					<div style="display:flex;gap:12px;align-items:center">
						<input name="name" value="<?php echo htmlspecialchars($editCategory['name']); ?>" style="flex:1;padding:10px;border-radius:8px;border:1px solid #e2e8f0" autofocus>
						<button class="admin-btn primary" type="submit">Save</button>
						<a href="manage_categories.php" class="admin-btn secondary">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	<?php endif; ?>
</main>

<!-- <?php include('../templates/footer.php'); ?> -->

