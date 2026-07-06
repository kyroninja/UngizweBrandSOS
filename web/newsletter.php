<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.html');
    exit();
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    die('Invalid request.');
}

if (!rate_limit('newsletter', 5, 600)) {
    http_response_code(429);
    die('Too many attempts. Please try again later.');
}

$email = trim($_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('A valid email address is required.');
}

$conn = db_connect();

$check = $conn->prepare('SELECT id FROM newsletter_subscribers WHERE email = ?');
$check->bind_param('s', $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    header('Location: ../index.html?subscribed=already');
    exit();
}
$check->close();

$stmt = $conn->prepare('INSERT INTO newsletter_subscribers (email) VALUES (?)');
$stmt->bind_param('s', $email);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: ../index.html?subscribed=success');
    exit();
}

error_log('newsletter.php insert failed: ' . $stmt->error);
$stmt->close();
$conn->close();
http_response_code(500);
die('Could not subscribe. Please try again.');
