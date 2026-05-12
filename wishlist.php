<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    echo "login_required";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);

// check exists
$check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");

if (mysqli_num_rows($check) > 0) {
    // remove
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");
    echo "removed";
} else {
    // add
    mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
    echo "added";
}
?>