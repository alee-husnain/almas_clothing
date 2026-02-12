<?php
// checkout.php - Checkout page

include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

// Check if user is logged in, redirect to login if not
if (!isset($_SESSION['user_id'])) {
    // Store intended return URL
    $_SESSION['return_to'] = '/almas_clothing/public/checkout.php';
    header('Location: /almas_clothing/public/login.php');
    exit;
}

$orderData = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Very small checkout handling for development: validate minimal fields
    $name = trim($_POST['name'] ?? '');
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal = trim($_POST['postal'] ?? '');
    $payment = trim($_POST['payment'] ?? 'cod');

    if ($name === '' || $address === '' || !$email) {
        $error = "Please provide your name, email, and shipping address.";
    } elseif ($payment === 'card') {
        // Validate card details
        $cardName = trim($_POST['card_name'] ?? '');
        $cardNumber = trim($_POST['card_number'] ?? '');
        $cardExpiry = trim($_POST['card_expiry'] ?? '');
        $cardCvv = trim($_POST['card_cvv'] ?? '');

        if ($cardName === '' || $cardNumber === '' || $cardExpiry === '' || $cardCvv === '') {
            $error = "Please provide all card details.";
        } elseif (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            $error = "Please provide a valid card number.";
        } elseif (strlen($cardCvv) < 3 || strlen($cardCvv) > 4) {
            $error = "Please provide a valid CVV.";
        } else {
            // Process card payment and save to database
            $cartItems = get_cart_items($conn);
            $cartTotal = get_cart_total($conn);

            if (!empty($cartItems)) {
                // Calculate shipping: 10% of each product line_total (i.e., 10% of subtotal)
                $shipping = 0;
                foreach ($cartItems as $ci) {
                    $shipping += ($ci['line_total'] * 0.10);
                }
                $shipping = round($shipping, 2);
                $grandTotal = round($cartTotal + $shipping, 2);

                // Build full shipping address
                $fullAddress = $address;
                if ($city) $fullAddress .= "\n" . $city;
                if ($postal) $fullAddress .= ", " . $postal;
                if ($phone) $fullAddress .= "\nPhone: " . $phone;

                try {
                    // Start transaction
                    $conn->beginTransaction();

                    // Insert order into orders table
                    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, status) 
                                           VALUES (?, ?, ?, ?, 'pending')");
                    $stmt->execute([
                        $_SESSION['user_id'],
                        $grandTotal,
                        $fullAddress,
                        'Credit/Debit Card'
                    ]);

                    // Get the inserted order ID
                    $orderId = $conn->lastInsertId();

                    // Insert order items
                    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                                VALUES (?, ?, ?, ?)");

                    foreach ($cartItems as $item) {
                        $itemStmt->execute([
                            $orderId,
                            $item['product_id'],
                            $item['quantity'],
                            $item['price']
                        ]);
                    }

                    // Commit transaction
                    $conn->commit();

                    // Prepare order data for receipt display
                    $orderData = [
                        'orderId' => $orderId,
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                        'payment' => 'Credit/Debit Card',
                        'cardLast4' => substr($cardNumber, -4),
                        'subtotal' => $cartTotal,
                        'shipping' => $shipping,
                        'total' => $grandTotal,
                        'items' => $cartItems,
                        'date' => date('F j, Y \a\t g:i A')
                    ];

                    // Clear cart after successful order
                    if (function_exists('clear_cart')) clear_cart($conn);

                } catch (Exception $e) {
                    // Rollback on error
                    $conn->rollBack();
                    $error = "Error processing order: " . $e->getMessage();
                }
            } else {
                $error = "Your cart is empty.";
            }
        }
    } else {
        // Cash on Delivery
        $cartItems = get_cart_items($conn);
        $cartTotal = get_cart_total($conn);

        if (!empty($cartItems)) {
            // Calculate shipping: 10% of each product line_total
            $shipping = 0;
            foreach ($cartItems as $ci) {
                $shipping += ($ci['line_total'] * 0.10);
            }
            $shipping = round($shipping, 2);
            $grandTotal = round($cartTotal + $shipping, 2);

            // Build full shipping address
            $fullAddress = $address;
            if ($city) $fullAddress .= "\n" . $city;
            if ($postal) $fullAddress .= ", " . $postal;
            if ($phone) $fullAddress .= "\nPhone: " . $phone;

            try {
                // Start transaction
                $conn->beginTransaction();

                // Insert order into orders table
                $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, status) 
                                       VALUES (?, ?, ?, ?, 'pending')");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $grandTotal,
                    $fullAddress,
                    'Cash on Delivery'
                ]);

                // Get the inserted order ID
                $orderId = $conn->lastInsertId();

                // Insert order items
                $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                            VALUES (?, ?, ?, ?)");

                foreach ($cartItems as $item) {
                    $itemStmt->execute([
                        $orderId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['price']
                    ]);
                }

                // Commit transaction
                $conn->commit();

                // Prepare order data for receipt display
                $orderData = [
                    'orderId' => $orderId,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'payment' => 'Cash on Delivery',
                    'cardLast4' => null,
                    'subtotal' => $cartTotal,
                    'shipping' => $shipping,
                    'total' => $grandTotal,
                    'items' => $cartItems,
                    'date' => date('F j, Y \a\t g:i A')
                ];

                // Clear cart after successful order
                if (function_exists('clear_cart')) clear_cart($conn);

            } catch (Exception $e) {
                // Rollback on error
                $conn->rollBack();
                $error = "Error processing order: " . $e->getMessage();
            }
        } else {
            $error = "Your cart is empty.";
        }
    }
}

$cartItems = get_cart_items($conn);
$cartTotal = get_cart_total($conn);

?>
<main class="checkout">
    <div class="checkout-inner">
        <h1>Checkout</h1>

        <?php if (!empty($error)): ?>
            <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="notice success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="checkout-grid">
            <section class="checkout-form">
                <div class="card">
                    <h2>Billing & Shipping</h2>
                    <form method="POST" id="checkout-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full name</label>
                                <input type="text" id="name" name="name" required placeholder="Jane Doe">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" <?php echo isset($_SESSION['email']) ? 'readonly' : 'required'; ?> placeholder="you@example.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" placeholder="0300-1234567">
                        </div>

                        <div class="form-group">
                            <label for="address">Shipping address</label>
                            <textarea id="address" name="address" rows="4" required placeholder="Street address, city, postal code"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" placeholder="Lahore">
                            </div>
                            <div class="form-group">
                                <label for="postal">Postal code</label>
                                <input type="text" id="postal" name="postal" placeholder="54000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="payment">Payment method</label>
                            <select id="payment" name="payment" onchange="toggleCardFields()">
                                <option value="cod">Cash on Delivery</option>
                                <option value="card">Credit / Debit Card</option>
                            </select>
                        </div>

                        <!-- Card Payment Fields (Hidden by default) -->
                        <div id="card-fields" style="display: none; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                            <h3 style="margin: 0 0 16px; font-size: 1.1rem; color: #0f1724;">Card Details</h3>
                            
                            <div class="form-group">
                                <label for="card_name">Cardholder Name</label>
                                <input type="text" id="card_name" name="card_name" placeholder="John Doe">
                            </div>

                            <div class="form-group">
                                <label for="card_number">Card Number</label>
                                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_expiry">Expiry Date</label>
                                    <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label for="card_cvv">CVV</label>
                                    <input type="text" id="card_cvv" name="card_cvv" placeholder="123" maxlength="4">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn cta" type="submit">Place Order</button>
                        </div>
                    </form>
                </div>
            </section>

            <aside class="order-summary">
                <div class="card">
                    <h2>Order Summary</h2>
                    <?php if (empty($cartItems)): ?>
                        <p>Your cart is empty.</p>
                    <?php else: ?>
                        <ul class="order-items">
                            <?php foreach ($cartItems as $item): ?>
                                <li class="order-item">
                                    <img src="../images/<?php echo htmlspecialchars($item['image_url'] ?? 'product-placeholder.svg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div class="item-meta">
                                        <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="item-qty">Qty: <?php echo (int)$item['quantity']; ?></div>
                                    </div>
                                    <div class="item-price"><?php echo number_format($item['line_total'], 0); ?> PKR</div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php
                            // Calculate shipping for display: 10% of each line_total
                            $displayShipping = 0;
                            foreach ($cartItems as $ci) {
                                $displayShipping += ($ci['line_total'] * 0.10);
                            }
                            $displayShipping = round($displayShipping, 2);
                            $displayGrand = round($cartTotal + $displayShipping, 2);
                        ?>
                        <div class="order-totals">
                            <div class="totals-row"><span>Subtotal</span><span class="totals-value"><?php echo number_format($cartTotal, 0); ?> PKR</span></div>
                            <div class="totals-row"><span>Shipping</span><span class="totals-value"><?php echo number_format($displayShipping, 0); ?> PKR</span></div>
                            <div class="totals-row total"><strong>Total</strong><strong class="totals-value"><?php echo number_format($displayGrand, 0); ?> PKR</strong></div>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</main>

<!-- Payment Receipt Modal -->
<?php if ($orderData): ?>
<div id="receipt-modal" class="modal-overlay active">
    <div class="modal-content receipt-card">
        <button class="modal-close" onclick="closeReceipt()">&times;</button>
        
        <div class="receipt-header">
            <h2>Order Confirmed</h2>
            <p class="receipt-id">Order ID: <strong>#<?php echo htmlspecialchars($orderData['orderId']); ?></strong></p>
        </div>

        <div class="receipt-body">
            <div class="receipt-section">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($orderData['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($orderData['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($orderData['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($orderData['address']); ?></p>
            </div>

            <div class="receipt-section">
                <h3>Payment Method</h3>
                <p><strong><?php echo htmlspecialchars($orderData['payment']); ?></strong></p>
                <?php if ($orderData['cardLast4']): ?>
                    <p>Card ending in: ****<?php echo htmlspecialchars($orderData['cardLast4']); ?></p>
                <?php endif; ?>
            </div>

            <div class="receipt-section">
                <h3>Order Details</h3>
                <div class="receipt-items">
                    <?php foreach ($orderData['items'] as $item): ?>
                        <div class="receipt-item">
                            <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            <span class="item-qty">x<?php echo (int)$item['quantity']; ?></span>
                            <span class="item-price"><?php echo number_format($item['line_total'], 0); ?> PKR</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="receipt-total">
                    <div class="receipt-row"><span>Subtotal:</span><span><?php echo number_format($orderData['subtotal'] ?? 0, 0); ?> PKR</span></div>
                    <div class="receipt-row"><span>Shipping:</span><span><?php echo number_format($orderData['shipping'] ?? 0, 0); ?> PKR</span></div>
                    <div class="receipt-row total"><strong>Total Amount:</strong><strong><?php echo number_format($orderData['total'] ?? 0, 0); ?> PKR</strong></div>
                </div>
            </div>

            <div class="receipt-section">
                <p class="receipt-date">Ordered on: <?php echo htmlspecialchars($orderData['date']); ?></p>
                <p class="receipt-note">A confirmation email has been sent to <?php echo htmlspecialchars($orderData['email']); ?></p>
            </div>
        </div>

        <div class="receipt-footer">
            <a href="shop.php" class="btn cta">Continue Shopping</a>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function toggleCardFields() {
    const paymentMethod = document.getElementById('payment').value;
    const cardFields = document.getElementById('card-fields');
    
    if (paymentMethod === 'card') {
        cardFields.style.display = 'block';
        // Make card fields required
        document.getElementById('card_name').required = true;
        document.getElementById('card_number').required = true;
        document.getElementById('card_expiry').required = true;
        document.getElementById('card_cvv').required = true;
    } else {
        cardFields.style.display = 'none';
        // Remove required attribute
        document.getElementById('card_name').required = false;
        document.getElementById('card_number').required = false;
        document.getElementById('card_expiry').required = false;
        document.getElementById('card_cvv').required = false;
    }
}

function closeReceipt() {
    document.getElementById('receipt-modal').classList.remove('active');
    // Redirect to shop after closing
    setTimeout(() => {
        window.location.href = 'shop.php';
    }, 500);
}

// Format card number input
document.getElementById('card_number')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formatted = '';
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) formatted += ' ';
        formatted += value[i];
    }
    e.target.value = formatted;
});

// Format expiry date input
document.getElementById('card_expiry')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});
</script>

<?php include('../templates/footer.php'); ?>