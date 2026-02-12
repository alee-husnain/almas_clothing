<?php
// functions.php - Helper functions

// Function to merge session cart with database cart after login
function merge_carts($conn, $user_id) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If there are items in the session cart
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            // Add or update each item in the database cart
            $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) 
                                  VALUES (?, ?, ?) 
                                  ON DUPLICATE KEY UPDATE quantity = quantity + ?");
            $stmt->execute([$user_id, $product_id, $quantity, $quantity]);
        }
        // Clear the session cart after merging
        unset($_SESSION['cart']);
    }
}

// Function to get featured products
function get_featured_products($conn, $limit = 4) {
    $limit = (int)$limit; // Sanitize the limit value
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            ORDER BY RAND() 
            LIMIT " . $limit;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
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
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to add product to the cart
function add_to_cart($conn, $product_id, $quantity) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $q = (int)$quantity;
    if ($q <= 0) return;

    // If user is logged in, store in database
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) 
                              VALUES (?, ?, ?) 
                              ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->execute([$_SESSION['user_id'], $product_id, $q, $q]);
    } else {
        // Store in session for non-logged in users
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $q;
        } else {
            $_SESSION['cart'][$product_id] = $q;
        }
    }
}

// Function to get total amount from cart
function get_cart_total($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $total = 0;

    // If user is logged in, get from database
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT p.price, ci.quantity 
                              FROM cart_items ci 
                              JOIN products p ON ci.product_id = p.product_id 
                              WHERE ci.user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $total += $row['price'] * $row['quantity'];
        }
    } else {
        // Get from session for non-logged in users
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) return $total;
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = get_product_by_id($conn, $product_id);
            if ($product) {
                $total += $product['price'] * $quantity;
            }
        }
    }
    return $total;
}

// Update quantity for a product in the cart
function update_cart_item($conn, $product_id, $quantity) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $q = (int)$quantity;

    if (isset($_SESSION['user_id'])) {
        if ($q <= 0) {
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
        } else {
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$q, $_SESSION['user_id'], $product_id]);
        }
    } else {
        if ($q <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $q;
        }
    }
}

// Remove an item from the cart
function remove_from_cart($conn, $product_id) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If user is logged in, remove from database
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
    } else {
        // Remove from session for non-logged in users
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

// Clear the whole cart
function clear_cart($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If user is logged in, clear from database
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        // Clear session cart for non-logged in users
        unset($_SESSION['cart']);
    }
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

// Function to get category products count
function get_category_product_count($conn, $category_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// Function to get category details
function get_category_by_id($conn, $category_id) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$category_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Return cart items with product data
function get_cart_items($conn) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $items = [];

    // If user is logged in, get from database
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT p.*, c.name as category_name, ci.quantity,
                              (p.price * ci.quantity) as line_total 
                              FROM cart_items ci 
                              JOIN products p ON ci.product_id = p.product_id 
                              LEFT JOIN categories c ON p.category_id = c.category_id 
                              WHERE ci.user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Get from session for non-logged in users
        if (empty($_SESSION['cart'])) return $items;
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = get_product_by_id($conn, $product_id);
            if ($product) {
                $product['quantity'] = $quantity;
                $product['line_total'] = $product['price'] * $quantity;
                $items[] = $product;
            }
        }
    }
    return $items;
}

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
