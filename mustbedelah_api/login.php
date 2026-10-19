<?php
// login.php
include 'db_connect.php';

// Get the JSON data sent from the C# app
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Missing email or password"]);
    exit();
}

$email = $data['email'];
$password = $data['password'];

// Look up the customer by email
$stmt = $conn->prepare("SELECT customer_id, name, email, password FROM customers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();

    // Verify the password against the hashed password in the database
    if (password_verify($password, $customer['password'])) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "customerId" => $customer['customer_id'],
            "name" => $customer['name'],
            "email" => $customer['email']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Incorrect password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Customer not found"]);
}

$stmt->close();
$conn->close();
?>