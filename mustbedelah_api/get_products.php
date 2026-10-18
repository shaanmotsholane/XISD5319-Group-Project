<?php
// get_products.php
include 'db_connect.php';

// Optional filter by size (e.g., get_products.php?size=M)
$size = isset($_GET['size']) ? $_GET['size'] : null;

if ($size) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE size = ? AND is_active = 1");
    $stmt->bind_param("s", $size);
} else {
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_active = 1");
}

$stmt->execute();
$result = $stmt->get_result();

$products = array();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode([
    "status" => "success",
    "count" => count($products),
    "products" => $products
]);

$stmt->close();
$conn->close();
?>