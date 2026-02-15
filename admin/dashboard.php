<?php
// dashboard.php - Admin dashboard page

include('../includes/db.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch all products
$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.category_id");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total products
$totalProducts = count($products);

// Calculate total value
$totalValue = array_reduce($products, function($carry, $item) {
    return $carry + ($item['price'] * ($item['stock'] ?? 1));
}, 0);

// Get category counts
$categories = [];
foreach ($products as $product) {
    $catName = $product['category_name'] ?? 'Uncategorized';
    $categories[$catName] = ($categories[$catName] ?? 0) + 1;
}

include('../templates/header.php');
?>
<link rel="stylesheet" href="../public/css/admin.css?v=2.0">

<main class="admin-dashboard">
    <!-- Stats Section -->
    <!-- <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
            </div>
            <div class="stat-content">
                <h4>Total Products</h4>
                <div class="stat-value"><?php echo $totalProducts; ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="7"/>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                </svg>
            </div>
            <div class="stat-content">
                <h4>Total Categories</h4>
                <div class="stat-value"><?php echo count($categories); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div class="stat-content">
                <h4>Total Value</h4>
                <div class="stat-value">PKR <?php echo number_format($totalValue); ?></div>
            </div>
        </div>
    </div> -->

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="quick-actions-grid">
            <a href="add_product.php" class="quick-action-btn">
                <svg class="quick-action-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                <span class="quick-action-label">Add Product</span>
            </a>
            <a href="manage_categories.php" class="quick-action-btn">
                <svg class="quick-action-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10H3"/>
                    <path d="M21 6H3"/>
                    <path d="M21 14H3"/>
                    <path d="M21 18H3"/>
                </svg>
                <span class="quick-action-label">Manage Categories</span>
            </a>
            <a href="view_orders.php" class="quick-action-btn">
                <svg class="quick-action-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="quick-action-label">View Orders</span>
            </a>
        </div>
    </div>

    <!-- Products Section -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1>Product Management</h1>
            <h2>Manage and organize your product inventory</h2>
        </div>
        <div class="dashboard-actions">
            <a href="add_product.php" class="admin-btn primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add New Product
            </a>
            <a href="logout.php" class="admin-btn danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Logout
            </a>
        </div>
    </div>

    <div class="search-filter-bar">
        <div class="search-box">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" placeholder="Search products..." id="productSearch">
        </div>
    </div>

    <div class="products-section">
        <div class="section-header">
            <h3>Product List</h3>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if ($product['image_url']): ?>
                                <img class="product-image" src="../images/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php endif; ?>
                        </td>
                        <td class="product-name"><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="product-price">PKR <?php echo number_format($product['price']); ?></td>
                        <td><span class="category-tag"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span></td>
                        <td>
                            <div class="table-actions">
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="admin-btn secondary">Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this product?')" 
                                   class="admin-btn danger">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
// Simple search functionality
document.getElementById('productSearch').addEventListener('input', function(e) {
    const searchText = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.admin-table tbody tr');
    
    rows.forEach(row => {
        const productName = row.querySelector('.product-name').textContent.toLowerCase();
        const category = row.querySelector('.category-tag').textContent.toLowerCase();
        
        if (productName.includes(searchText) || category.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<!-- <?php include('../templates/footer.php'); ?> -->
