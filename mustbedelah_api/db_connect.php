<?php
// db_connect.php
$host = "localhost";
$user = "root";
$password = "";
$database = "mustbedelah";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . mysqli_connect_error()
    ]);
    exit();
}
?>