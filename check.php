<?php
require_once 'config.php';

$type = $_GET['type'];
$value = $_GET['value'];

$stmt = $conn->prepare("SELECT id FROM users WHERE $type = ?");
$stmt->bind_param("s", $value);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["message" => "$type already exists."]);
} else {
    echo json_encode(["message" => ""]);
}

$stmt->close();
$conn->close();
?>