<?php
// product_page.php - Single product details page

include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

if (isset($_GET['id'])) {
    $product = get_product_by_id($conn, $_GET['id']);
}
?>
<main>
    <section class="product-detail">
        <div class="detail-grid">
            <div class="detail-media">
                <img src="../images/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="detail-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="detail-price">PKR <?php echo number_format($product['price'], 2); ?></p>
                <p class="detail-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                <form method="POST" action="cart.php" class="add-to-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1">
                    <button type="submit" class="btn cta">Add to Cart</button>
                </form>
            </div>
        </div>
    </section>
</main>
<?php include('../templates/footer.php'); ?>
