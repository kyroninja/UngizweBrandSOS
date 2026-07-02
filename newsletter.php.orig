<?php
$conn = new mysqli("localhost", "root", "", "ungizwedb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email
$email = trim($_POST['email'] ?? '');

// Validate
if ($email === '') {
    die("Email is required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

/*
Suggested table:

CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
*/

// Check for duplicates
$check = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    $conn->close();

    // already subscribed
    header("Location: ../index.html?subscribed=already");
    exit();
}

$check->close();

// Insert new subscriber
$stmt = $conn->prepare("
    INSERT INTO newsletter_subscribers (email)
    VALUES (?)
");

$stmt->bind_param("s", $email);

if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    header("Location: ../index.html?subscribed=success");
    exit();

} else {

    echo "Error: " . $stmt->error;

    $stmt->close();
    $conn->close();
}
?>
