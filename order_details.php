<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo "Invalid request.";
    exit();
}

$order_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

/* ORDER INFO */
$order_query = $conn->prepare("
    SELECT o.*, u.full_name AS customer_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.order_id = ? AND o.user_id = ?
");

$order_query->bind_param("ii", $order_id, $user_id);
$order_query->execute();
$order_result = $order_query->get_result();

if ($order_result->num_rows === 0) {
    echo "Order not found or access denied.";
    exit();
}

$order = $order_result->fetch_assoc();

/* ORDER ITEMS (FIXED with category slug) */
$items_stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image, c.slug AS category_slug
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN categories c ON p.category_id = c.id
    WHERE oi.order_id = ?
");

$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

include('header.php');
?>

<div class="container py-5">
    <h2>Order Details - #<?= htmlspecialchars($order['order_id']) ?></h2>

    <div class="mb-4">
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Shipping Address:</strong> <?= htmlspecialchars($order['shipping_address'] ?? '') ?></p>
        <p><strong>Order Date:</strong> <?= date("d M Y, h:i A", strtotime($order['order_date'])) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></p>
        <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status'] ?? 'Pending') ?></p>
        <p><strong>Order Status:</strong> <?= htmlspecialchars($order['order_status'] ?? 'Pending') ?></p>
    </div>

    <h4>Ordered Products</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            $grand_total = 0;

            while ($item = $items_result->fetch_assoc()):

                $item_total = $item['quantity'] * $item['price'];
                $grand_total += $item_total;

                /* ✅ FIXED IMAGE PATH (VERY IMPORTANT) */
                $image_path = "uploads/" . $item['category_slug'] . "/" . $item['image'];

                if (empty($item['image']) || !file_exists($image_path)) {
                    $image_path = "uploads/default.png";
                }
            ?>

            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>

                <td>
                    <img src="<?= htmlspecialchars($image_path) ?>" width="60" height="60" style="object-fit:cover;">
                </td>

                <td>BDT <?= number_format($item['price'], 2) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td>BDT <?= number_format($item_total, 2) ?></td>
            </tr>

            <?php endwhile; ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Grand Total</th>
                <th>BDT <?= number_format($grand_total, 2) ?></th>
            </tr>
        </tfoot>
    </table>
</div>

<?php include('footer.php'); ?>