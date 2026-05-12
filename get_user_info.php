<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['full_name'])) {
    echo json_encode([
        "status" => "success",
        "full_name" => $_SESSION['full_name']
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "User not logged in or session expired."
    ]);
}
?>