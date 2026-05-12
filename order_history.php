<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
include('config.php');
include('header.php');

// Fetch order history
$query = "SELECT order_id, order_date, total_amount, order_status 
          FROM orders 
          WHERE user_id = ? 
          ORDER BY order_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<link rel="stylesheet" href="order_history.css">

<style>
.badge {
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 13px;
    color: #fff;
    font-weight: 600;
}

.bg-success { background-color: #28a745; }
.bg-warning { background-color: #ffc107; color: #000; }
.bg-danger { background-color: #dc3545; }
.bg-secondary { background-color: #6c757d; }
</style>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Your Order History</h2>

    <?php if (!empty($orders)) : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Order #</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) : ?>

                        <?php
                        // ✅ NULL-safe status handling
                        $statusRaw = $order['order_status'] ?? 'Pending';
                        $status = strtolower($statusRaw);

                        $badgeClass = 'secondary';
                        if ($status === 'completed') $badgeClass = 'success';
                        elseif ($status === 'pending') $badgeClass = 'warning';
                        elseif ($status === 'cancelled') $badgeClass = 'danger';
                        ?>

                        <tr>
                            <td>#<?= htmlspecialchars($order['order_id']) ?></td>

                            <td>
                                <?= date("d M Y, h:i A", strtotime($order['order_date'])) ?>
                            </td>

                            <td>
                                BDT <?= number_format($order['total_amount'], 2) ?>
                            </td>

                            <td>
                                <span class="badge bg-<?= $badgeClass ?>">
                                    <?= htmlspecialchars(ucfirst($statusRaw)) ?>
                                </span>
                            </td>

                            <td>
                                <a href="order_details.php?id=<?= urlencode($order['order_id']) ?>" 
                                   class="btn btn-sm btn-info">
                                   View
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else : ?>
        <p class="text-muted">You haven't placed any orders yet.</p>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>