<?php
include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

// Get sorting parameter
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$valid_sorts = ['price_asc', 'price_desc', 'newest'];
if (!in_array($sort, $valid_sorts)) {
    $sort = '';
}

// Get category ID from URL if present
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$category = null; // Initialize $category as null

// Get products based on category or all products
if ($category_id) {
    $products = get_products_by_category($conn, $category_id, $sort);
    $category = get_category_by_id($conn, $category_id);
    $title = $category ? htmlspecialchars($category['name']) : 'Products';
} else {
    $products = get_all_products($conn, $sort);
    $title = 'All Products';
}

// Helper function to generate sort URL
function getSortUrl($sort_value) {
    global $category_id;
    $params = ['sort' => $sort_value];
    if ($category_id) {
        $params['category'] = $category_id;
    }
    return 'shop.php?' . http_build_query($params);
}
?>

<main>
    <section class="product-list">
        <div class="container">
            <header class="section-header">
                <h1><?php echo $title; ?></h1>
                <?php if ($category && !empty($category['description'])): ?>
                    <p class="lead"><?php echo htmlspecialchars($category['description']); ?></p>
                <?php endif; ?>

                <!-- Category Navigation -->
                <div class="category-nav">
                    <a href="shop.php" class="category-link <?php echo !$category_id ? 'active' : ''; ?>">
                        <span class="category-name">All</span>
                        <span class="category-count"><?php echo count(get_all_products($conn)); ?></span>
                    </a>
                    <?php
                    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
                    $imagesDir = dirname(__FILE__) . '/../images/';
                    while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)):
                        $count = get_category_product_count($conn, $cat['category_id']);
                        // Determine category image with fallbacks
                        $catImg = '';
                        if (!empty($cat['image_url']) && file_exists($imagesDir . $cat['image_url'])) {
                            $catImg = $cat['image_url'];
                        } else {
                            $altName = 'category-' . strtolower(str_replace(' ', '-', $cat['name'])) . '.jpg';
                            if (file_exists($imagesDir . $altName)) {
                                $catImg = $altName;
                            } elseif (file_exists($imagesDir . 'product.jpg')) {
                                $catImg = 'product.jpg';
                            }
                        }
                    ?>
                        <a href="shop.php?category=<?php echo $cat['category_id']; ?><?php echo $sort ? '&sort='.$sort : ''; ?>" 
                           class="category-link <?php echo $category_id == $cat['category_id'] ? 'active' : ''; ?>">
                            <?php if (!empty($catImg)): ?>
                                <img src="../images/<?php echo htmlspecialchars($catImg); ?>" 
                                     alt="<?php echo htmlspecialchars($cat['name']); ?>" class="category-icon">
                            <?php endif; ?>
                            <span class="category-name"><?php echo htmlspecialchars($cat['name']); ?></span>
                            <span class="category-count"><?php echo $count; ?></span>
                        </a>
                    <?php endwhile; ?>
                </div>

                <!-- Sort Options -->
                <div class="sort-options">
                    <span class="sort-label">Sort by:</span>
                    <a href="<?php echo getSortUrl('newest'); ?>" 
                       class="sort-link <?php echo $sort === 'newest' ? 'active' : ''; ?>">Newest</a>
                    <a href="<?php echo getSortUrl('price_asc'); ?>" 
                       class="sort-link <?php echo $sort === 'price_asc' ? 'active' : ''; ?>">Price: Low to High</a>
                    <a href="<?php echo getSortUrl('price_desc'); ?>" 
                       class="sort-link <?php echo $sort === 'price_desc' ? 'active' : ''; ?>">Price: High to Low</a>
                </div>
            </header>

            <?php if (empty($products)): ?>
                <div class="notice info">No products found in this category.</div>
            <?php else: ?>
                <div class="products">
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <?php
                                // Determine product image with fallback
                                $prodImg = '';
                                if (!empty($product['image_url']) && file_exists(dirname(__FILE__) . '/../images/' . $product['image_url'])) {
                                    $prodImg = $product['image_url'];
                                } elseif (file_exists(dirname(__FILE__) . '/../images/product.jpg')) {
                                    $prodImg = 'product.jpg';
                                }
                            ?>
                            <?php if (!empty($prodImg)): ?>
                                <img src="../images/<?php echo htmlspecialchars($prodImg); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php endif; ?>
                            <?php if (isset($product['category_name'])): ?>
                                <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <?php endif; ?>
                            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="price"><?php echo number_format($product['price'], 0); ?> PKR</p>
                            <a href="product_page.php?id=<?php echo $product['product_id']; ?>" class="btn">View Details</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include('../templates/footer.php'); ?>
