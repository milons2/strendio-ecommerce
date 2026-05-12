<?php

// cart_functions.php

function addToCart($user_id, $product_id, $conn, $quantity = 1) {
    $user_id = (int)$user_id;
    $product_id = (int)$product_id;
    $quantity = (int)$quantity;

    if ($user_id > 0 && $product_id > 0 && $quantity > 0 && $conn) {
        // Check if the item is already in the cart
        $stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Item exists, update quantity
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
            return $stmt->execute();
        } else {
            // Item doesn't exist, add new item
            $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            return $stmt->execute();
        }
    }
    return false;
}

function removeFromCart($user_id, $product_id, $conn) {
    $user_id = (int)$user_id;
    $product_id = (int)$product_id;

    if ($user_id > 0 && $product_id > 0 && $conn) {
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        return $stmt->execute();
    }
    return false;
}

function getCartItems($user_id, $conn) {
    $user_id = (int)$user_id;
    if ($user_id > 0 && $conn) {
        $stmt = $conn->prepare("SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.name AS product_name, p.price AS product_price, p.image AS product_image
                               FROM cart_items ci
                               JOIN products p ON ci.product_id = p.id
                               WHERE ci.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

function clearCart($user_id, $conn) {
    $user_id = (int)$user_id;
    if ($user_id > 0 && $conn) {
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
    return false;
}

function getCartTotal($user_id, $conn) {
    $cartItems = getCartItems($user_id, $conn);
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['product_price'] * $item['quantity'];
    }
    return number_format($total, 2);
}

function getCartItemCount($user_id, $conn) {
    $user_id = (int)$user_id;
    if ($user_id > 0 && $conn) {
        $stmt = $conn->prepare("SELECT SUM(quantity) AS total_items FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total_items'] ? $row['total_items'] : 0;
    }
    return 0;
}

?>