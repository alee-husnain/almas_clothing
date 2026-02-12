<?php
// index.php - Homepage with product listing

include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

// Fetch all products
$products = get_all_products($conn);
?>
<main>
    <!-- Hero -->
    <section class="hero">
        <div class="hero-inner">
            <h1>Welcome to Almas Clothing Brand</h1>
            <p class="lead">Quality clothing crafted with care — explore our curated collection and find your new favorite piece.</p>
            <a href="shop.php" class="btn cta">Shop Now</a>
        </div>
    </section>

    <!-- Feature highlights -->
    <section class="features">
        <div class="features-inner"> 
            <div class="feature">
                <img style=" height: 60px; width: 60px" src="../images/icon-fabrics.jpg" alt="fabric icon" class="feature-icon">
                <h3>Premium Fabrics</h3>
                <p>Handpicked materials for comfort and durability.</p>
            </div>
            <div class="feature">
                <img style=" height: 60px; width: 60px" src="../images/icon-shipping.jpg" alt="shipping icon" class="feature-icon">
                <h3>Free Shipping</h3>
                <p>On orders above PKR 5000 across Pakistan.</p>
            </div>
            <div class="feature">
                <img style=" height: 60px; width: 60px" src="../images/icon-returns.jpg" alt="returns icon" class="feature-icon">
                <h3>Easy Returns</h3>
                <p>30-day hassle-free returns for eligible items.</p>
            </div>
        </div>
    </section>

    <!-- Categories preview -->
    <section class="categories">
        <div class="container">
            <h2>Shop by Category</h2>
            <p class="lead muted">Browse our curated collection by category</p>
            
            <div class="category-grid">
                <a href="shop.php?category=1" class="category-card">
                    <div class="category-image" style="background-image: url('/almas_clothing/images/category-shirts.jpg')">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <h3>Shirts & Tops</h3>
                        <span class="category-meta">Premium Collection</span>
                    </div>
                </a>

                <a href="shop.php?category=2" class="category-card">
                    <div class="category-image" style="background-image: url('/almas_clothing/images/category-jackets.jpg')">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <h3>Jackets & Coats</h3>
                        <span class="category-meta">Winter Essentials</span>
                    </div>
                </a>

                <a href="shop.php?category=3" class="category-card">
                    <div class="category-image" style="background-image: url('/almas_clothing/images/category-dresses.jpg')">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <h3>Dresses</h3>
                        <span class="category-meta">Party & Formal</span>
                    </div>
                </a>

                <a href="shop.php?category=4" class="category-card">
                    <div class="category-image" style="background-image: url('/almas_clothing/images/category-traditional.jpg')">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <h3>Traditional</h3>
                        <span class="category-meta">Ethnic Collection</span>
                    </div>
                </a>
            </div>

            <div class="categories-footer">
                <a href="shop.php" class="btn cta">View All Categories</a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="container">
            <header class="section-header">
                <h2>Featured Products</h2>
                <p class="lead">Handpicked selections you'll love</p>
            </header>

            <div class="products featured-grid">
                <?php 
                // Get 4 random featured products
                $featured_products = get_featured_products($conn, 4);
                foreach ($featured_products as $product): 
                ?>
                    <div class="product-item featured-item">
                        <div class="product-image">
                            <img src="../images/<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php if (isset($product['category_name'])): ?>
                                <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="product-footer">
                                <p class="price">PKR <?php echo number_format($product['price'], 0); ?></p>
                                <a href="product_page.php?id=<?php echo $product['product_id']; ?>" 
                                   class="btn featured-btn">Shop Now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="featured-footer">
                <a href="shop.php" class="btn cta">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Newsletter / CTA -->
    <section class="newsletter">
        <div class="newsletter-inner">
            <div class="newsletter-text">
                <h2>Join our newsletter</h2>
                <p>Get 10% off your first order and be the first to see new arrivals.</p>
            </div>
            <form id="newsletter-form" class="newsletter-form">
                <input type="email" name="email" id="newsletter-email" placeholder="Your email address" required>
                <button type="submit" class="btn cta" id="subscribe-btn">
                    <span class="btn-text">Subscribe</span>
                    <span class="btn-loading" style="display: none;">
                        <svg class="spinner" viewBox="0 0 50 50">
                            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                        </svg>
                    </span>
                </button>
            </form>
            <div id="newsletter-message" class="newsletter-message"></div>
        </div>
    </section>

    <script>
    document.getElementById('newsletter-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const email = form.email.value;
        const button = document.getElementById('subscribe-btn');
        const message = document.getElementById('newsletter-message');
        const buttonText = button.querySelector('.btn-text');
        const buttonLoading = button.querySelector('.btn-loading');

        // Show loading state
        buttonText.style.display = 'none';
        buttonLoading.style.display = 'inline-block';
        button.disabled = true;
        message.innerHTML = '';

        try {
            if (!email) {
                throw new Error('Please enter a valid email address');
            }

            const response = await fetch('subscribe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}`
            });

            let data;
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            } else {
                throw new Error('Invalid server response format');
            }
            
            if (!response.ok) {
                throw new Error(data.message || 'Server error');
            }
            
            message.innerHTML = data.message;
            message.className = 'newsletter-message ' + (data.success ? 'success' : 'error');

            if (data.success) {
                form.reset();
            }
            
            // Log debug info if available
            if (data.debug) {
                console.error('Debug info:', data.debug);
            }
        } catch (error) {
            message.innerHTML = 'An error occurred. Please try again later.';
            message.className = 'newsletter-message error';
        } finally {
            // Restore button state
            buttonText.style.display = 'inline-block';
            buttonLoading.style.display = 'none';
            button.disabled = false;
        }
    });
    </script>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="testimonials-inner">
            <blockquote>
                "Absolutely loved the fabric and fit — will buy again!" <cite>— Aisha</cite>
            </blockquote>
            <blockquote>
                "Fast delivery and great packaging. Highly recommend." <cite>— Omar</cite>
            </blockquote>
        </div>
    </section>
</main>
<?php include('../templates/footer.php'); ?>
