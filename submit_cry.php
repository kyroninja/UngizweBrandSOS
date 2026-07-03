<?php
require_once __DIR__ . '/config.php';
csrf_token(); // ensure session/token exist

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cries.php');
    exit();
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    die('Invalid or expired form submission. Please refresh and try again.');
}

// Honeypot — bots fill hidden fields, humans don't
if (!empty($_POST['website'])) {
    header('Location: thank_you.html'); // silently pretend success
    exit();
}

if (!rate_limit('submit_cry', 5, 600)) {
    http_response_code(429);
    die('You are submitting too quickly. Please wait a few minutes and try again.');
}

$brand = trim($_POST['brand'] ?? '');
$cry   = trim($_POST['cry'] ?? '');

if ($brand === '' || $cry === '') {
    die('Brand and your experience are required.');
}
if (mb_strlen($brand) > 100 || mb_strlen($cry) > 4000) {
    die('Submission too long. Please shorten your entry.');
}
if (empty($_POST['consent'])) {
    die('You must acknowledge the submission terms.');
}

$conn = db_connect();
$stmt = $conn->prepare('INSERT INTO cries (brand, cry) VALUES (?, ?)');
$stmt->bind_param('ss', $brand, $cry);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: thank_you.html');
    exit();
}

error_log('submit_cry insert failed: ' . $stmt->error);
$stmt->close();
$conn->close();
http_response_code(500);
die('Something went wrong saving your submission. Please try again.');