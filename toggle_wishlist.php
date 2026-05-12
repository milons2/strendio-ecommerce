<?php
session_start();
include('config.php');

$user_id = $_SESSION['user_id'] ?? 0;
$product_id = $_POST['product_id'] ?? 0;

if (!$user_id || !$product_id) {
    exit;
}

// check exists
$check = mysqli_query($conn, 
    "SELECT * FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");

if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, 
        "DELETE FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");
    echo "removed";
} else {
    mysqli_query($conn, 
        "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
    echo "added";
}
?>