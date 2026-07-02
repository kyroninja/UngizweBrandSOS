<?php
// DB connection settings
$host = "localhost";
$db   = "ungizwedb";
$user = "root";
$pass = "";
$charset = "utf8mb4";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$brand = isset($_POST['brand']) ? trim($_POST['brand']) : '';
$cry   = isset($_POST['cry']) ? trim($_POST['cry']) : '';

// Basic validation
if (empty($brand) || empty($cry)) {
    die("Brand and Cry are required.");
}

if (!isset($_POST['consent'])) {
    die("You must acknowledge the submission terms.");
}

// Prepare SQL (safe insert)
$stmt = $conn->prepare("INSERT INTO criestb (brand, cry) VALUES (?, ?)");
$stmt->bind_param("ss", $brand, $cry);

// Execute
if ($stmt->execute()) {
    header("Location: http://localhost//UngizweBrandSOS/listing.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
