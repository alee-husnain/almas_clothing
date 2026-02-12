<?php
// functions.php - Helper functions

// ============================================================================
// CART FUNCTIONS WITH SIZE SUPPORT
// ============================================================================

// Function to add product to the cart (WITH SIZE)
function add_to_cart($conn, $product_id, $quantity = 1, $size = 'M') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $q = (int)$quantity;
    if ($q <= 0) return false;
    
    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        // Logged-in user - save to database
        $user_id = $_SESSION['user_id'];
        
        // Check if item with same size already exists in cart
        $stmt = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? AND size = ?");
        $stmt->execute([$user_id, $product_id, $size]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update quantity
            $new_quantity = $existing['quantity'] + $q;
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
            $stmt->execute([$new_quantity, $existing['cart_item_id']]);
        } else {
            // Insert new item
            $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $q, $size]);
        }
    } else {
        // Guest user - save to session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Create unique key for product + size combination
        $cart_key = $product_id . '_' . $size;
        
        if (isset($_SESSION['cart'][$cart_key])) {
            // Update quantity
            $_SESSION['cart'][$cart_key]['quantity'] += $q;
        } else {
            // Add new item
            $_SESSION['cart'][$cart_key] = [
                'product_id' => $product_id,
                'quantity' => $q,
                'size' => $size
            ];
        }
    }
    
    return true;
}

// Get cart items with product data and size
function get_cart_items($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $cart_items = [];
    
    if (isset($_SESSION['user_id'])) {
        // Logged-in user - get from database
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("
            SELECT c.cart_item_id, c.product_id, c.quantity, c.size,
                   p.name, p.price, p.image_url,
                   cat.name as category_name
            FROM cart_items c
            JOIN products p ON c.product_id = p.product_id
            LEFT JOIN categories cat ON p.category_id = cat.category_id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Guest user - get from session
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $cart_key => $item) {
                $stmt = $conn->prepare("
                    SELECT p.*, cat.name as category_name 
                    FROM products p 
                    LEFT JOIN categories cat ON p.category_id = cat.category_id 
                    WHERE p.product_id = ?
                ");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    $cart_items[] = [
                        'cart_item_id' => $cart_key,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'size' => $item['size'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image_url' => $product['image_url'],
                        'category_name' => $product['category_name'] ?? null
                    ];
                }
            }
        }
    }
    
    return $cart_items;
}

// Update quantity for a cart item
function update_cart_item($conn, $cart_item_id, $quantity) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $q = (int)$quantity;
    
    if ($q <= 0) {
        return remove_from_cart($conn, $cart_item_id);
    }
    
    if (isset($_SESSION['user_id'])) {
        // Logged-in user - update database
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ? AND user_id = ?");
        $stmt->execute([$q, $cart_item_id, $_SESSION['user_id']]);
    } else {
        // Guest user - update session
        if (isset($_SESSION['cart'][$cart_item_id])) {
            $_SESSION['cart'][$cart_item_id]['quantity'] = $q;
        }
    }
    
    return true;
}

// Remove an item from the cart
function remove_from_cart($conn, $cart_item_id) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id'])) {
        // Logged-in user - remove from database
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id = ? AND user_id = ?");
        $stmt->execute([$cart_item_id, $_SESSION['user_id']]);
    } else {
        // Guest user - remove from session
        if (isset($_SESSION['cart'][$cart_item_id])) {
            unset($_SESSION['cart'][$cart_item_id]);
        }
    }
    
    return true;
}

// Clear the whole cart
function clear_cart($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id'])) {
        // Logged-in user - clear from database
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        // Guest user - clear session cart
        unset($_SESSION['cart']);
    }
    
    return true;
}

// Get cart total amount
function get_cart_total($conn) {
    $cart_items = get_cart_items($conn);
    $total = 0;
    
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    return $total;
}

// Get cart item count
function get_cart_count($conn) {
    $cart_items = get_cart_items($conn);
    $count = 0;
    
    foreach ($cart_items as $item) {
        $count += $item['quantity'];
    }
    
    return $count;
}

// Function to merge session cart with database cart after login
function merge_carts($conn, $user_id) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If there are items in the session cart
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $cart_key => $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $size = $item['size'];
            
            // Check if item with same size already exists
            $stmt = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? AND size = ?");
            $stmt->execute([$user_id, $product_id, $size]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                // Update quantity
                $new_quantity = $existing['quantity'] + $quantity;
                $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
                $stmt->execute([$new_quantity, $existing['cart_item_id']]);
            } else {
                // Insert new item
                $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $product_id, $quantity, $size]);
            }
        }
        // Clear the session cart after merging
        unset($_SESSION['cart']);
    }
}

// ============================================================================
// PRODUCT FUNCTIONS
// ============================================================================

// Function to get featured products
function get_featured_products($conn, $limit = 4) {
    $limit = (int)$limit;
    $stmt = $conn->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        ORDER BY RAND() 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch all products with optional sorting
function get_all_products($conn, $sort = '') {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id";
    
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.price DESC";
            break;
        case 'newest':
            $sql .= " ORDER BY p.created_at DESC";
            break;
        default:
            $sql .= " ORDER BY p.product_id DESC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get product by ID
function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get products by category with optional sorting
function get_products_by_category($conn, $category_id, $sort = '') {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.category_id = ?";
    
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.price DESC";
            break;
        case 'newest':
            $sql .= " ORDER BY p.created_at DESC";
            break;
        default:
            $sql .= " ORDER BY p.product_id DESC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$category_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================================
// CATEGORY FUNCTIONS
// ============================================================================

// Function to get category details
function get_category_by_id($conn, $category_id) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get category products count
function get_category_product_count($conn, $category_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// ============================================================================
// EMAIL FUNCTIONS
// ============================================================================

// Order Confirmation Email
function send_order_confirmation($user_email, $order_details) {
    $subject = "Order Confirmation - Almas Clothing Brand";
    $message = "Thank you for your order! Here are the details:\n\n";
    foreach ($order_details as $order) {
        $message .= $order['name'] . " x " . $order['quantity'] . " = " . $order['total_price'] . "\n";
    }
    $message .= "\nTotal: " . $order_details['total_amount'] . " PKR";
    
    $headers = "From: no-reply@almasclothing.com";
    
    mail($user_email, $subject, $message, $headers);
}
?>