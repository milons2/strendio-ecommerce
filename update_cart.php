<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!empty($_POST['quantities'])) {

    foreach ($_POST['quantities'] as $cart_id => $qty) {

        $cart_id = intval($cart_id);
        $qty = intval($qty);

        if ($qty > 0) {
            $stmt = $conn->prepare("UPDATE cart_items SET quantity=? WHERE id=?");
            $stmt->bind_param("ii", $qty, $cart_id);
            $stmt->execute();
        }
    }
}

header("Location: cart.php");
exit();