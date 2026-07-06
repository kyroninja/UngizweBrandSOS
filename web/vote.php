<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit();
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    die('Invalid request.');
}

if (!rate_limit('vote', 20, 300)) {
    http_response_code(429);
    die('Too many votes. Please slow down.');
}

$brand = trim($_POST['brand'] ?? '');
$topic = trim($_POST['topic'] ?? '');

if ($brand === '' || $topic === '') {
    die('Invalid vote request');
}

// Prevent the same session voting for the same brand/topic repeatedly
session_start();
$voteKey = 'voted_' . md5($brand . '|' . $topic);
if (!empty($_SESSION[$voteKey])) {
    header('Location: search.php?q=' . urlencode($brand));
    exit();
}

$conn = db_connect();
$stmt = $conn->prepare('
    UPDATE brand_topic_scores
    SET num_supporting = num_supporting + 1
    WHERE brand = ? AND topic = ?
');
$stmt->bind_param('ss', $brand, $topic);

if ($stmt->execute()) {
    $_SESSION[$voteKey] = true;
    $stmt->close();
    $conn->close();
    header('Location: search.php?q=' . urlencode($brand));
    exit();
}

error_log('vote.php update failed: ' . $stmt->error);
$stmt->close();
$conn->close();
http_response_code(500);
die('Could not register vote.');
