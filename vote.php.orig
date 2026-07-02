<?php
$conn = new mysqli("localhost", "root", "", "ungizwedb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$brand = trim($_POST['brand'] ?? '');
$topic = trim($_POST['topic'] ?? '');

if ($brand === '' || $topic === '') {
    die("Invalid vote request");
}

/*
Increment support count
*/
$stmt = $conn->prepare("
    UPDATE brand_topic_scores
    SET num_supporting = num_supporting + 1
    WHERE brand = ? AND topic = ?
");

$stmt->bind_param("ss", $brand, $topic);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    // redirect back to same brand page
    header("Location: search.php?q=" . urlencode($brand));
    exit();
} else {
    echo "Error updating vote: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
