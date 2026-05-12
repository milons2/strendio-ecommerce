<?php
require 'config.php'; // Your DB connection file

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $stmt = $conn->prepare("UPDATE products SET is_deleted = 1 WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        header("Location: manage_products.php?message=Product+soft+deleted+successfully");
        exit();
    } else {
        echo "Error deleting product.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No product ID provided.";
}
?>