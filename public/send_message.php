<?php
require_once(__DIR__ . '/../includes/db.php');

// Set JSON response header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get POST data and sanitize
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$messageText = trim($_POST['message'] ?? '');

// Validation
$errors = [];

if (strlen($name) < 2) $errors['name'] = 'Please enter your name (at least 2 characters).';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
if (strlen($messageText) < 10) $errors['message'] = 'Message must be at least 10 characters.';
if ($phone && !preg_match('/^\+?\d{7,15}$/', $phone)) $errors['phone'] = 'Please enter a valid phone number.';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please correct the errors and try again.',
        'errors' => $errors
    ]);
    exit;
}

try {
    // Insert into database
    $stmt = $conn->prepare(
        "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)"
    );
    $stmt->execute([$name, $email, $messageText]);

    // Optional: send email to admin
    $adminEmail = "support@almasclothing.com";
    $subjectLine = $subject ?: "New Contact Form Message from $name";
    $body = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$messageText";
    $headers = "From: no-reply@almasclothing.com";

    @mail($adminEmail, $subjectLine, $body, $headers);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you shortly.'
    ]);

} catch (PDOException $e) {
    error_log('Contact form error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later.'
    ]);
}
