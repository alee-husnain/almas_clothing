<?php
// view_orders.php - Admin orders management page

include('../includes/db.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query
$sql = "SELECT o.*, u.name as customer_name, u.email as customer_email,
        COUNT(oi.order_item_id) as total_items
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        WHERE 1=1";

// Add status filter
if ($status_filter !== 'all') {
    $sql .= " AND o.status = :status";
}

// Add search filter
if (!empty($search)) {
    $sql .= " AND (u.name LIKE :search OR u.email LIKE :search OR o.order_id LIKE :search)";
}

$sql .= " GROUP BY o.order_id ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);

if ($status_filter !== 'all') {
    $stmt->bindValue(':status', $status_filter);
}
if (!empty($search)) {
    $stmt->bindValue(':search', '%' . $search . '%');
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get order statistics
$stats_sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
    SUM(total_amount) as total_revenue
    FROM orders";
$stats_stmt = $conn->query($stats_sql);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

include('../templates/header.php');
?>
<link rel="stylesheet" href="../public/css/admin.css?v=2.0">

<main class="admin-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1>Order Management</h1>
            <h2>View and manage customer orders</h2>
        </div>
        <div class="dashboard-actions">
            <a href="dashboard.php" class="admin-btn secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Back to Dashboard
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

    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
            </div>
            <div class="stat-content">
                <h4>Total Orders</h4>
                <div class="stat-value"><?php echo $stats['total_orders'] ?? 0; ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="stat-content">
                <h4>Pending Orders</h4>
                <div class="stat-value"><?php echo $stats['pending_orders'] ?? 0; ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div class="stat-content">
                <h4>Completed Orders</h4>
                <div class="stat-value"><?php echo $stats['completed_orders'] ?? 0; ?></div>
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
                <h4>Total Revenue</h4>
                <div class="stat-value">PKR <?php echo number_format($stats['total_revenue'] ?? 0); ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="search-filter-bar">
        <form method="GET" action="" class="filter-form">
            <div class="search-box">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" placeholder="Search by order ID, customer name or email..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <select name="status" class="status-filter" onchange="this.form.submit()">
                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Orders</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            
            <button type="submit" class="admin-btn primary">Filter</button>
            <?php if (!empty($search) || $status_filter !== 'all'): ?>
                <a href="view_orders.php" class="admin-btn secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="products-section">
        <div class="section-header">
            <h3>Orders List (<?php echo count($orders); ?>)</h3>
        </div>
        
        <?php if (count($orders) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                        <td>
                            <div class="customer-info">
                                <strong><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></strong>
                                <small><?php echo htmlspecialchars($order['customer_email'] ?? 'N/A'); ?></small>
                            </div>
                        </td>
                        <td><?php echo $order['total_items']; ?> item(s)</td>
                        <td class="product-price"><strong>PKR <?php echo number_format($order['total_amount']); ?></strong></td>
                        <td><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="admin-btn secondary">View</a>
                                <a href="update_order_status.php?id=<?php echo $order['order_id']; ?>" class="admin-btn primary">Update</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <h3>No Orders Found</h3>
                <p>There are no orders matching your criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.customer-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.customer-info strong {
    color: #1a1a1a;
}

.customer-info small {
    color: #666;
    font-size: 0.85em;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
    text-transform: capitalize;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-processing {
    background: #cfe2ff;
    color: #084298;
}

.status-completed {
    background: #d1e7dd;
    color: #0f5132;
}

.status-cancelled {
    background: #f8d7da;
    color: #842029;
}

.filter-form {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.status-filter {
    padding: 10px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: white;
    font-size: 14px;
    cursor: pointer;
    min-width: 150px;
}

.status-filter:focus {
    outline: none;
    border-color: #000;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state svg {
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-state h3 {
    margin-bottom: 8px;
    color: #333;
}
</style>

<?php include('../templates/footer.php'); ?>
