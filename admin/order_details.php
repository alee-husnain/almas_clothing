<?php
// order_details.php - View detailed information about a specific order

include('../includes/db.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id === 0) {
    header("Location: view_orders.php");
    exit();
}

// Fetch order details with customer information
$stmt = $conn->prepare("
    SELECT o.*, u.name as customer_name, u.email as customer_email 
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: view_orders.php");
    exit();
}

// Fetch order items with product details
$items_stmt = $conn->prepare("
    SELECT oi.*, p.name as product_name, p.image_url, c.name as category_name
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE oi.order_id = ?
");
$items_stmt->execute([$order_id]);
$order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

include('../templates/header.php');
?>
<link rel="stylesheet" href="../public/css/admin.css?v=2.0">

<main class="admin-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1>Order Details #<?php echo $order_id; ?></h1>
            <h2>Order placed on <?php echo date('F d, Y \a\t h:i A', strtotime($order['created_at'])); ?></h2>
        </div>
        <div class="dashboard-actions">
            <a href="view_orders.php" class="admin-btn secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Orders
            </a>
            <button onclick="window.print()" class="admin-btn primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print Order
            </button>
        </div>
    </div>

    <div class="order-details-container">
        <!-- Order Status Card -->
        <div class="order-card">
            <div class="order-card-header">
                <h3>Order Status</h3>
                <span class="status-badge status-<?php echo $order['status']; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
            <div class="order-card-body">
                <form method="POST" action="update_order_status.php" class="status-update-form">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <div class="form-group">
                        <label>Update Status:</label>
                        <select name="status" class="form-control">
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="admin-btn primary">Update Status</button>
                </form>
            </div>
        </div>

        <!-- Customer Information Card -->
        <div class="order-card">
            <div class="order-card-header">
                <h3>Customer Information</h3>
            </div>
            <div class="order-card-body">
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest Customer'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['customer_email'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">User ID:</span>
                    <span class="info-value">#<?php echo $order['user_id'] ?? 'N/A'; ?></span>
                </div>
            </div>
        </div>

        <!-- Shipping Information Card -->
        <div class="order-card">
            <div class="order-card-header">
                <h3>Shipping Information</h3>
            </div>
            <div class="order-card-body">
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Information Card -->
        <div class="order-card">
            <div class="order-card-header">
                <h3>Payment Information</h3>
            </div>
            <div class="order-card-body">
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value"><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Amount:</span>
                    <span class="info-value total-amount">PKR <?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items Section -->
    <div class="products-section">
        <div class="section-header">
            <h3>Order Items (<?php echo count($order_items); ?>)</h3>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($order_items as $item): 
                    $item_subtotal = $item['price'] * $item['quantity'];
                    $subtotal += $item_subtotal;
                ?>
                    <tr>
                        <td>
                            <?php if ($item['image_url']): ?>
                                <img class="product-image" src="../images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td class="product-name"><?php echo htmlspecialchars($item['product_name'] ?? 'Deleted Product'); ?></td>
                        <td><span class="category-tag"><?php echo htmlspecialchars($item['category_name'] ?? 'N/A'); ?></span></td>
                        <td class="product-price">PKR <?php echo number_format($item['price'], 2); ?></td>
                        <td><strong>×<?php echo $item['quantity']; ?></strong></td>
                        <td class="product-price"><strong>PKR <?php echo number_format($item_subtotal, 2); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>Total:</strong></td>
                    <td class="product-price"><strong>PKR <?php echo number_format($subtotal, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</main>

<style>
.order-details-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.order-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.order-card-header {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-card-header h3 {
    margin: 0;
    font-size: 1.1em;
    color: #333;
}

.order-card-body {
    padding: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #666;
}

.info-value {
    color: #333;
    text-align: right;
}

.total-amount {
    font-size: 1.3em;
    font-weight: 700;
    color: #28a745;
}

.status-update-form {
    display: flex;
    gap: 12px;
    align-items: flex-end;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #000;
}

.total-row {
    background: #f8f9fa;
    font-weight: 700;
}

.total-row td {
    padding: 15px !important;
    font-size: 1.1em;
}

.text-right {
    text-align: right;
}

.no-image {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    border-radius: 6px;
    font-size: 0.75em;
    color: #999;
}

@media print {
    .dashboard-header,
    .admin-btn,
    .status-update-form {
        display: none !important;
    }
    
    .order-details-container {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<?php include('../templates/footer.php'); ?>
