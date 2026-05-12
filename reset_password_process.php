<?php
require_once 'config.php';

if (isset($_POST['token']) && isset($_POST['newPassword']) && isset($_POST['confirmNewPassword'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    if ($newPassword !== $confirmNewPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $hashedPassword, $token);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password reset successfully. You will be redirected to the login page."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating password."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

$conn->close();
?>