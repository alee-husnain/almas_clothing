<?php
include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

// handle cart actions: add (from product page), update quantities, remove item, clear
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $pid = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 1);
        add_to_cart($conn, $pid, $qty);
    } elseif ($action === 'update') {
        $pid = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 1);
        update_cart_item($conn, $pid, $qty);
    } elseif ($action === 'remove') {
        $pid = (int)($_POST['product_id'] ?? 0);
        remove_from_cart($conn, $pid);
    } elseif ($action === 'clear') {
        clear_cart($conn);
    }
    // redirect to avoid form resubmission
    header('Location: cart.php');
    exit;
}

$items = get_cart_items($conn);
$cart_total = get_cart_total($conn);
?>

<main class="cart-page">
    <div class="container">
        <header class="cart-header">
            <h1>Shopping Cart</h1>
            <p class="item-count"><?php echo count($items); ?> items in your cart</p>
        </header>

        <?php if (!empty($items)): ?>
            <div class="cart-grid">
                <div class="cart-items">
                    <?php foreach ($items as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="../images/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <?php if (isset($item['category_name'])): ?>
                                    <span class="item-category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                                <?php endif; ?>
                                <div class="item-price">PKR <?php echo number_format($item['price'], 0); ?></div>
                            </div>
                            <div class="item-controls">
                                <form method="POST" class="quantity-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <div class="quantity-wrapper">
                                        <button type="button" class="quantity-btn minus" data-input="quantity-<?php echo $item['product_id']; ?>">−</button>
                                        <input type="number" name="quantity" id="quantity-<?php echo $item['product_id']; ?>" 
                                               value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                        <button type="button" class="quantity-btn plus" data-input="quantity-<?php echo $item['product_id']; ?>">+</button>
                                    </div>
                                    <button type="submit" class="update-btn">Update</button>
                                </form>
                            </div>
                            <div class="item-total">
                                <div class="line-total">PKR <?php echo number_format($item['line_total'], 0); ?></div>
                                <form method="POST" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" class="remove-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" 
                                                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-card">
                        <h2>Order Summary</h2>
                        <?php
                            // Calculate shipping: 10% per item line_total
                            $cart_shipping = 0;
                            foreach ($items as $it) {
                                $cart_shipping += ($it['line_total'] * 0.10);
                            }
                            $cart_shipping = round($cart_shipping, 2);
                            $cart_grand = round($cart_total + $cart_shipping, 2);
                        ?>
                        <div class="summary-items">
                            <div class="summary-item">
                                <span>Subtotal</span>
                                <span>PKR <?php echo number_format($cart_total, 0); ?></span>
                            </div>
                            <div class="summary-item">
                                <span>Shipping</span>
                                <span>PKR <?php echo number_format($cart_shipping, 0); ?></span>
                            </div>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span>PKR <?php echo number_format($cart_grand, 0); ?></span>
                        </div>
                        <div class="summary-actions">
                            <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
                            <a href="shop.php" class="btn continue-btn">Continue Shopping</a>
                        </div>
                    </div>
                    <?php if (!empty($items)): ?>
                        <form method="POST" class="clear-cart-form">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="clear-cart-btn">Clear Cart</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <div class="empty-cart-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="64" height="64">
                        <path d="M4.873 3h14.254a1 1 0 01.809.412l3.823 5.256a.5.5 0 01-.037.633L12.367 21.602a.5.5 0 01-.734 0L.278 9.302a.5.5 0 01-.037-.634l3.823-5.256A1 1 0 014.873 3z" 
                              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added anything to your cart yet.</p>
                    <a href="shop.php" class="btn cta">Start Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity increment/decrement handlers
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.input);
            const currentValue = parseInt(input.value) || 0;
            
            if (this.classList.contains('minus')) {
                if (currentValue > 1) input.value = currentValue - 1;
            } else {
                input.value = currentValue + 1;
            }
            
            // Trigger change event
            input.dispatchEvent(new Event('change'));
        });
    });

    // Auto-submit quantity updates
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>

<?php include('../templates/footer.php'); ?>
