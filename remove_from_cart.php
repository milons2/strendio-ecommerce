<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $cart_item_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $query = "DELETE FROM cart_items WHERE cart_item_id = $cart_item_id AND user_id = $user_id";
    mysqli_query($conn, $query);
}

header("Location: cart.php");
exit();
?>

