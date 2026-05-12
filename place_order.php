<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* GET CART */
$query = "SELECT ci.product_id, ci.quantity, p.price
          FROM cart_items ci
          JOIN products p ON ci.product_id = p.id
          WHERE ci.user_id = $user_id";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Cart is empty");
}

$total = 0;
$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $total += $row['price'] * $row['quantity'];
    $items[] = $row;
}

/* INSERT ORDER */
$order_query = "INSERT INTO orders (user_id, total_amount, order_date)
                VALUES ($user_id, $total, NOW())";

if (!mysqli_query($conn, $order_query)) {
    die("Order failed: " . mysqli_error($conn));
}

$order_id = mysqli_insert_id($conn);

/* INSERT ORDER ITEMS */
foreach ($items as $item) {
    $pid = $item['product_id'];
    $qty = $item['quantity'];
    $price = $item['price'];

    mysqli_query($conn,
        "INSERT INTO order_items (order_id, product_id, quantity, price)
         VALUES ($order_id, $pid, $qty, $price)"
    );
}

/* CLEAR CART */
mysqli_query($conn, "DELETE FROM cart_items WHERE user_id = $user_id");

/* REDIRECT */
header("Location: order_success.php?order_id=" . $order_id);
exit();
?>