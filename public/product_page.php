<?php
// product_page.php - Single product details page with Size Guide

include('../includes/db.php');
include('../includes/functions.php');
include('../templates/header.php');

if (isset($_GET['id'])) {
    $product = get_product_by_id($conn, $_GET['id']);
}

// All sizes are available for all products
$availableSizes = ['XS', 'S', 'M', 'L', 'XL'];

// Hardcoded size chart data
$sizeChartData = [
    'XS'  => ['shoulder' => 13.5, 'bust' => 18, 'armhole' => 8, 'sleeve_full' => 21.5, 'sleeve_3qtr' => 17.5],
    'S'   => ['shoulder' => 14.5, 'bust' => 19, 'armhole' => 8.5, 'sleeve_full' => 22, 'sleeve_3qtr' => 18],
    'M'   => ['shoulder' => 15.5, 'bust' => 21, 'armhole' => 9, 'sleeve_full' => 22.5, 'sleeve_3qtr' => 18.5],
    'L'   => ['shoulder' => 16, 'bust' => 23, 'armhole' => 9.5, 'sleeve_full' => 23, 'sleeve_3qtr' => 19],
    'XL'  => ['shoulder' => 16.75, 'bust' => 25, 'armhole' => 10.5, 'sleeve_full' => 23.5, 'sleeve_3qtr' => 19.5],
    'XXL' => ['shoulder' => 17.5, 'bust' => 27, 'armhole' => 11.25, 'sleeve_full' => 23.5, 'sleeve_3qtr' => 20]
];

// All possible sizes in order
$allSizes = ['XS', 'S', 'M', 'L', 'XL'];
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
                    
                    <!-- Size Selection -->
                    <div class="size-selection">
                        <div class="size-header">
                            <button type="button" class="size-guide-link" onclick="openSizeGuide()">Size Guide</button>
                        </div>
                        <div class="size-options">
                            <?php foreach ($allSizes as $size): ?>
                                <label class="size-option">
                                    <input type="radio" name="size" value="<?php echo $size; ?>" required>
                                    <span class="size-label"><?php echo $size; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="quantity-selection">
                        <div class="quantity-controls">
                            <button type="button" class="qty-btn" onclick="decreaseQty()">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M3 8h10" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" readonly>
                            <button type="button" class="qty-btn" onclick="increaseQty()">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn cta add-to-cart-btn">ADD TO CART</button>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- Size Guide Modal -->
<div id="sizeGuideModal" class="modal-overlay">
    <div class="modal-content size-guide-modal">
        <button class="modal-close" onclick="closeSizeGuide()">&times;</button>
        
        <div class="size-guide-header">
            <h2>Size Guide</h2>
        </div>

        <div class="size-guide-body">
            <div class="size-guide-tabs">
                <button class="tab-btn active" onclick="switchUnit('inch')">Inch</button>
                <button class="tab-btn" onclick="switchUnit('cm')">CM</button>
            </div>

            <div class="size-guide-content">
                <h3>Dresses</h3>
                
                <div class="size-table-wrapper">
                    <!-- Inch Table -->
                    <table class="size-table" id="sizeTableInch">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Shoulder</th>
                                <th>Bust</th>
                                <th>Armhole Straight</th>
                                <th>Sleeve Length (Full)</th>
                                <th>Sleeve Length (3 QTR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sizeChartData as $size => $measurements): ?>
                            <tr>
                                <td><strong><?php echo $size; ?></strong></td>
                                <td><?php echo $measurements['shoulder']; ?></td>
                                <td><?php echo $measurements['bust']; ?></td>
                                <td><?php echo $measurements['armhole']; ?></td>
                                <td><?php echo $measurements['sleeve_full']; ?></td>
                                <td><?php echo $measurements['sleeve_3qtr']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- CM Table -->
                    <table class="size-table" id="sizeTableCm" style="display: none;">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Shoulder</th>
                                <th>Bust</th>
                                <th>Armhole Straight</th>
                                <th>Sleeve Length (Full)</th>
                                <th>Sleeve Length (3 QTR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sizeChartData as $size => $measurements): ?>
                            <tr>
                                <td><strong><?php echo $size; ?></strong></td>
                                <td><?php echo round($measurements['shoulder'] * 2.54, 1); ?></td>
                                <td><?php echo round($measurements['bust'] * 2.54, 1); ?></td>
                                <td><?php echo round($measurements['armhole'] * 2.54, 1); ?></td>
                                <td><?php echo round($measurements['sleeve_full'] * 2.54, 1); ?></td>
                                <td><?php echo round($measurements['sleeve_3qtr'] * 2.54, 1); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="size-guide-tips">
                    <h4>How to Measure</h4>
                    <ul>
                        <li><strong>Shoulder:</strong> Measure from shoulder point to shoulder point</li>
                        <li><strong>Bust:</strong> Measure around the fullest part of the bust</li>
                        <li><strong>Armhole Straight:</strong> Measure the straight armhole opening</li>
                        <li><strong>Sleeve Length:</strong> Measure from shoulder to wrist (full) or elbow (3 QTR)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Size Selection Styles */
.detail-info{
    padding: 20px 0;
}
.add-to-cart-form{
    display: flex;
    flex-direction: column;
}
.size-selection {
    padding-bottom: 16px;
}

.size-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.size-header label {
    font-weight: 400;
    font-size: 14px;
    color: #1a1a1a;
}

.size-guide-link {
    background: none;
    border: none;
    color: #8B4513;
    text-decoration: underline;
    font-size: 13px;
    cursor: pointer;
    padding: 0;
    font-family: inherit;
}

.size-guide-link:hover {
    color: #654321;
}

.size-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.size-option {
    position: relative;
    cursor: pointer;
}

.size-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.size-label {
    display: inline-block;
    padding: 12px 20px;
    border: 1px solid #d0d0d0;
    background: white;
    font-size: 14px;
    font-weight: 400;
    transition: all 0.2s ease;
    min-width: 55px;
    text-align: center;
    color: #1a1a1a;
}

.size-option input[type="radio"]:checked + .size-label {
    border-color: #1a1a1a;
    background: #1a1a1a;
    color: white;
}

.size-option:hover .size-label {
    border-color: #666;
}

.size-note {
    font-size: 12px;
    color: #666;
    margin: 0;
}

/* Quantity Selection */
.quantity-selection {
    padding-bottom: 16px;
}

.quantity-selection label {
    display: block;
    font-weight: 400;
    font-size: 14px;
    margin-bottom: 10px;
    color: #1a1a1a;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0;
    width: fit-content;
    border: 1px solid #d0d0d0;
}

.qty-btn {
    width: 42px;
    height: 42px;
    border: none;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border-right: 1px solid #d0d0d0;
}

.qty-btn:last-of-type {
    border-right: none;
    border-left: 1px solid #d0d0d0;
}

.qty-btn:hover {
    background: #f5f5f5;
}

.qty-btn:active {
    background: #e5e5e5;
}

.quantity-controls input {
    width: 60px;
    height: 42px;
    text-align: center;
    border: none;
    font-size: 14px;
    font-weight: 400;
    color: #1a1a1a;
}

.quantity-controls input:focus {
    outline: none;
}

/* Add to cart Button */
.add-to-cart-btn {
    width: 100%;
    padding: 16px 32px;
    background: #3a3a3a;
    color: white;
    border: none;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 1px;
    cursor: pointer;
    transition: background 0.3s;
    margin-bottom: 24px;
    text-transform: uppercase;
}

.add-to-cart-btn:hover {
    background: #1a1a1a;
}

/* Modal Overlay */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.modal-overlay.active {
    display: flex;
}

/* Size Guide Modal */
.size-guide-modal {
    background: white;
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    border-radius: 0;
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 32px;
    height: 32px;
    border: none;
    background: none;
    font-size: 28px;
    cursor: pointer;
    color: #666;
    line-height: 1;
    z-index: 10;
}

.modal-close:hover {
    color: #1a1a1a;
}

.size-guide-header {
    padding: 30px 30px 20px;
    border-bottom: 1px solid #e5e5e5;
}

.size-guide-header h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    color: #1a1a1a;
}

.size-guide-body {
    padding: 30px;
}

.size-guide-tabs {
    display: flex;
    gap: 0;
    margin-bottom: 30px;
    border-bottom: 1px solid #e5e5e5;
}

.tab-btn {
    padding: 12px 32px;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    color: #999;
    margin-bottom: -1px;
    transition: all 0.2s;
    font-family: inherit;
}

.tab-btn.active {
    color: #1a1a1a;
    border-bottom-color: #1a1a1a;
}

.tab-btn:hover:not(.active) {
    color: #666;
}

.size-guide-content h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
}

.size-table-wrapper {
    overflow-x: auto;
    margin-bottom: 30px;
    border: 1px solid #e5e5e5;
}

.size-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

.size-table th,
.size-table td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #e5e5e5;
    font-size: 13px;
}

.size-table thead th {
    background: #f8f8f8;
    font-weight: 600;
    color: #1a1a1a;
    border-bottom: 2px solid #e5e5e5;
}

.size-table tbody td {
    color: #666;
}

.size-table tbody tr:last-child td {
    border-bottom: none;
}

.size-guide-tips {
    background: #f8f8f8;
    padding: 24px;
    border-radius: 0;
}

.size-guide-tips h4 {
    margin: 0 0 16px 0;
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
}

.size-guide-tips ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.size-guide-tips li {
    padding: 8px 0;
    font-size: 13px;
    color: #666;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
    .size-guide-modal {
        max-height: 95vh;
    }
    
    .size-guide-header,
    .size-guide-body {
        padding: 20px;
    }
    
    .size-table {
        font-size: 12px;
    }
    
    .size-table th,
    .size-table td {
        padding: 10px 8px;
    }
}
</style>

<script>
// Quantity controls
function increaseQty() {
    const input = document.getElementById('quantity');
    input.value = parseInt(input.value) + 1;
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Accordion
function toggleAccordion(button) {
    button.classList.toggle('active');
    const content = button.nextElementSibling;
    content.classList.toggle('active');
}

// Size Guide Modal
function openSizeGuide() {
    document.getElementById('sizeGuideModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeSizeGuide() {
    document.getElementById('sizeGuideModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Unit Switcher
function switchUnit(unit) {
    // Update tab active state
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Show/hide tables
    if (unit === 'inch') {
        document.getElementById('sizeTableInch').style.display = 'table';
        document.getElementById('sizeTableCm').style.display = 'none';
    } else {
        document.getElementById('sizeTableInch').style.display = 'none';
        document.getElementById('sizeTableCm').style.display = 'table';
    }
}

// Close modal on outside click
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('sizeGuideModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSizeGuide();
        }
    });
});
</script>

<?php include('../templates/footer.php'); ?>