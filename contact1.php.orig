<?php
// contact.php

$conn = new mysqli("localhost", "root", "", "ungizwedb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Basic validation
if ($name === '' || $email === '' || $subject === '' || $message === '') {
    die("All fields are required.");
}

// Optional: sanitize email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}


$stmt = $conn->prepare("
    INSERT INTO contact_messages (name, email, subject, message)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    // redirect back to thank you page or home
    header("Location: thank_you.html");
    exit();

} else {

    echo "Error saving message: " . $stmt->error;

    $stmt->close();
    $conn->close();
}
?>
