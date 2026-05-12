<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

header('Content-Type: application/json');

// ✅ Block GET request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$loginContact = trim($_POST['loginContact'] ?? '');
$password = trim($_POST['loginPassword'] ?? '');

if (empty($loginContact) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields required"]);
    exit();
}

// Detect email or phone
if (filter_var($loginContact, FILTER_VALIDATE_EMAIL)) {
    $sql = "SELECT id, full_name, password FROM users WHERE email = ?";
} else {
    $sql = "SELECT id, full_name, password FROM users WHERE phone = ?";
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "DB error"]);
    exit();
}

$stmt->bind_param("s", $loginContact);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    if (password_verify($password, $row['password'])) {

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['full_name'] = $row['full_name'];

        echo json_encode([
            "status" => "success",
            "message" => "Login successful"
        ]);

    } else {
        echo json_encode(["status" => "error", "message" => "Wrong password"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$stmt->close();
$conn->close();
exit();
?>