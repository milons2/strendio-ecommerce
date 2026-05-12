<?php
require_once 'config.php'; // Database connection

$fullName = $_POST['fullName'];
$contactType = $_POST['contactType'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
$contactValue = $_POST[$contactType];
$address = $_POST['address'];

$stmt = $conn->prepare("INSERT INTO users (full_name, $contactType, password, address) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $fullName, $contactValue, $password, $address);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed. " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>