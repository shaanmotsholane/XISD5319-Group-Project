<?php
// register.php
include 'db_connect.php';

// Get the JSON data sent from the C# app
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Missing name, email or password"]);
    exit();
}

$name = $data['name'];
$email = $data['email'];
$password = $data['password'];

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit();
}

// Hash the password (IMPORTANT: The assignment brief says "encrypt", but hashing is the secure standard)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if the email already exists in the 'customers' table
$check = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered"]);
    exit();
}

// Insert the new customer into the database
$stmt = $conn->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Customer registered successfully",
        "customerId" => $stmt->insert_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed"]);
}

$stmt->close();
$conn->close();
?>