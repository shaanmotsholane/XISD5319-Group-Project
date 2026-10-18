<?php
$data = json_encode(["name" => "Test User", "email" => "testapi@test.com", "password" => "password123"]);
$ch = curl_init('http://localhost/websites/XISD5319-Group-Project-main/mustbedelah_api/register.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
?>