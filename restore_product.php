<?php
require 'config.php';

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $stmt = $conn->prepare("UPDATE products SET is_deleted = 0 WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        header("Location: manage_products.php?message=Product+restored+successfully");
        exit();
    } else {
        echo "Error restoring product.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No product ID provided.";
}
?>
