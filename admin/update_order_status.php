<?php
// update_order_status.php - Handle order status updates

include('../includes/db.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    
    // Validate inputs
    $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    
    if ($order_id === 0 || !in_array($status, $valid_statuses)) {
        $_SESSION['error'] = "Invalid order or status.";
        header("Location: view_orders.php");
        exit();
    }
    
    try {
        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$status, $order_id]);
        
        $_SESSION['success'] = "Order status updated successfully!";
        header("Location: order_details.php?id=" . $order_id);
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating order status: " . $e->getMessage();
        header("Location: order_details.php?id=" . $order_id);
        exit();
    }
    
} else {
    // If GET request, show update form
    $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($order_id === 0) {
        header("Location: view_orders.php");
        exit();
    }
    
    // Fetch order details
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header("Location: view_orders.php");
        exit();
    }
    
    // include('../templates/header.php');
    ?>
    <link rel="stylesheet" href="../public/css/admin.css?v=2.0">
    
    <main class="admin-dashboard">
        <div class="dashboard-header">
            <div class="dashboard-title">
                <h1>Update Order Status</h1>
                <h2>Order #<?php echo $order_id; ?></h2>
            </div>
            <div class="dashboard-actions">
                <a href="order_details.php?id=<?php echo $order_id; ?>" class="admin-btn secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back to Order Details
                </a>
            </div>
        </div>
        
        <div class="form-container">
            <div class="form-card">
                <div class="current-status">
                    <h3>Current Status:</h3>
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
                
                <form method="POST" action="update_order_status.php" class="update-form">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    
                    <div class="form-group">
                        <label for="status">Select New Status:</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">-- Select Status --</option>
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="status-descriptions">
                        <div class="status-desc" data-status="pending">
                            <strong>Pending:</strong> Order has been placed but not yet processed
                        </div>
                        <div class="status-desc" data-status="processing">
                            <strong>Processing:</strong> Order is being prepared and packaged
                        </div>
                        <div class="status-desc" data-status="completed">
                            <strong>Completed:</strong> Order has been delivered to customer
                        </div>
                        <div class="status-desc" data-status="cancelled">
                            <strong>Cancelled:</strong> Order has been cancelled
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="admin-btn primary">Update Status</button>
                        <a href="order_details.php?id=<?php echo $order_id; ?>" class="admin-btn secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <style>
    .form-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .form-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .current-status {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    
    .current-status h3 {
        margin: 0;
        color: #666;
        font-size: 1em;
    }
    
    .update-form .form-group {
        margin-bottom: 25px;
    }
    
    .update-form label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: #333;
    }
    
    .update-form .form-control {
        width: 100%;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s;
    }
    
    .update-form .form-control:focus {
        outline: none;
        border-color: #000;
    }
    
    .status-descriptions {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }
    
    .status-desc {
        padding: 10px 0;
        color: #666;
        font-size: 0.9em;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .status-desc:last-child {
        border-bottom: none;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
    }
    
    .form-actions .admin-btn {
        flex: 1;
        justify-content: center;
    }
    </style>
    
    <!-- <?php include('../templates/footer.php'); ?> -->
    <?php
}
?>
