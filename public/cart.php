<?php
include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

// handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $pid = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 1);
        $size = $_POST['size'] ?? null;

        // Validate that size is selected
        if (!$size) {
            $_SESSION['error'] = 'Please select a size before adding to cart.';
            header('Location: product_page.php?id=' . $pid);
            exit();
        }

        // Validate size is valid
        $valid_sizes = ['XS', 'S', 'M', 'L', 'XL'];
        if (!in_array($size, $valid_sizes)) {
            $_SESSION['error'] = 'Invalid size selected.';
            header('Location: product_page.php?id=' . $pid);
            exit();
        }

        add_to_cart($conn, $pid, $qty, $size);

    } elseif ($action === 'update') {
        $cart_item_id = $_POST['cart_item_id'] ?? '';
        $qty = (int)($_POST['quantity'] ?? 1);
        update_cart_item($conn, $cart_item_id, $qty);

    } elseif ($action === 'remove') {
        $cart_item_id = $_POST['cart_item_id'] ?? '';
        remove_from_cart($conn, $cart_item_id);

    } elseif ($action === 'clear') {
        clear_cart($conn);
    }

    header('Location: cart.php');
    exit;
}

$items = get_cart_items($conn);

// Calculate line_total for each item (needed for checkout.php)
foreach ($items as &$item) {
    $item['line_total'] = $item['price'] * $item['quantity'];
}
unset($item); // Break reference

$cart_total = get_cart_total($conn);
?>

<main class="cart-page">
    <div class="container">
        <header class="cart-header">
            <h1>Shopping Cart</h1>
        </header>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

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
                                <div class="item-size">
                                    Size: <strong><?php echo htmlspecialchars($item['size']); ?></strong>
                                </div>
                                <div class="item-price">
                                    PKR <?php echo number_format($item['price'], 0); ?>
                                </div>
                            </div>

                            <div class="item-controls">
                                <form method="POST" class="quantity-form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['cart_item_id']); ?>">

                                    <div class="quantity-wrapper">
                                        <button type="button" class="quantity-btn minus" 
                                            data-input="quantity-<?php echo $item['cart_item_id']; ?>">−</button>

                                        <input type="number" name="quantity"
                                            id="quantity-<?php echo $item['cart_item_id']; ?>"
                                            value="<?php echo $item['quantity']; ?>" min="1"
                                            class="quantity-input">

                                        <button type="button" class="quantity-btn plus"
                                            data-input="quantity-<?php echo $item['cart_item_id']; ?>">+</button>
                                    </div>

                                    <button type="submit" class="update-btn">Update</button>
                                </form>
                            </div>

                            <div class="item-total">
                                <div class="line-total">
                                    PKR <?php echo number_format($item['line_total'], 0); ?>
                                </div>

                                <form method="POST" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['cart_item_id']); ?>">
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-card">
                        <h2>Order Summary</h2>

                        <?php
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

                    <form method="POST" class="clear-cart-form">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="clear-cart-btn">Clear Cart</button>
                    </form>

                </div>
            </div>

        <?php else: ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <a href="shop.php" class="btn cta">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.input);
            const currentValue = parseInt(input.value) || 0;

            if (this.classList.contains('minus')) {
                if (currentValue > 1) input.value = currentValue - 1;
            } else {
                input.value = currentValue + 1;
            }

            input.dispatchEvent(new Event('change'));
        });
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>

<?php include('../templates/footer.php'); ?>