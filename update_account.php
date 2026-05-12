<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Sanitize inputs
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$address = trim($_POST['address']);

// Validate input (basic example, expand as needed)
$errors = [];
if (empty($full_name)) $errors[] = "Full name is required.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
if (empty($phone)) $errors[] = "Phone number is required.";
if (empty($address)) $errors[] = "Address is required.";

if (!empty($errors)) {
    $_SESSION['account_update_errors'] = $errors;
    header("Location: my_account.php");
    exit();
}

// Update the database
$stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
$stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);

if ($stmt->execute()) {
    $_SESSION['account_update_success'] = "Account updated successfully.";
} else {
    $_SESSION['account_update_errors'] = ["Failed to update account. Please try again."];
}

header("Location: my_account.php");
exit();
?>