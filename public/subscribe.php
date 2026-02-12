<?php
// Include database connection
require_once(__DIR__ . '/../includes/db.php');

// Set JSON response header
header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get and validate email
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a valid email address'
    ]);
    exit;
}

try {
    // Check if already subscribed
    $stmt = $conn->prepare("SELECT subscriber_id FROM subscribers WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'You are already subscribed to our newsletter'
        ]);
        exit;
    }

    // Add new subscriber
    $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
    $stmt->execute([$email]);

    // Optional: Send welcome email (won't break API if fails)
    $subject = "Welcome to Almas Clothing Newsletter";
    $message = "Hello!\n\nThank you for subscribing to Almas Clothing Newsletter.\n\n";
    $message .= "You are now first to know about our latest collections, offers & fashion tips.\n\n";
    $message .= "Here's your welcome coupon: WELCOME10\n\nHappy Shopping!\nAlmas Clothing Team";
    $headers = "From: no-reply@almasclothing.com";

    @mail($email, $subject, $message, $headers);

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for subscribing! Check your email for your welcome discount.'
    ]);

} catch (PDOException $e) {
    error_log('Newsletter subscription error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Something went wrong. Please try again later.'
    ]);
}
