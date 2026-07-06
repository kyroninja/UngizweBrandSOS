<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html#contact');
    exit();
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    die('Invalid request. Please refresh and try again.');
}

if (!rate_limit('contact', 5, 600)) {
    http_response_code(429);
    die('Too many messages sent. Please try again later.');
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    die('All fields are required.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email address.');
}
if (mb_strlen($name) > 150 || mb_strlen($subject) > 200 || mb_strlen($message) > 5000) {
    die('One or more fields exceed the maximum length.');
}

$conn = db_connect();
$stmt = $conn->prepare('
    INSERT INTO contact_messages (name, email, subject, message)
    VALUES (?, ?, ?, ?)
');
$stmt->bind_param('ssss', $name, $email, $subject, $message);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: thank_you.html');
    exit();
}

error_log('contact1.php insert failed: ' . $stmt->error);
$stmt->close();
$conn->close();
http_response_code(500);
die('Message could not be saved. Please try again.');
