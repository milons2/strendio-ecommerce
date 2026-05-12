<?php
require_once 'auth.php';
require_once 'config.php';
include('header.php');

$user_id = $_SESSION['user_id'] ?? 0;
$full_name = $_SESSION['full_name'] ?? 'User';

// ===== USER PROFILE =====
$user_stmt = $conn->prepare("SELECT full_name, email, created_at FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

// ===== TOTAL ORDERS =====
$order_stmt = $conn->prepare("SELECT COUNT(*) AS total_orders FROM orders WHERE user_id = ?");
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$total_orders = $order_stmt->get_result()->fetch_assoc()['total_orders'] ?? 0;
$order_stmt->close();

// ===== RECENT ORDERS =====
$recent_stmt = $conn->prepare("
    SELECT order_id, order_date, total_amount, order_status 
    FROM orders 
    WHERE user_id = ? 
    ORDER BY order_date DESC 
    LIMIT 5
");
$recent_stmt->bind_param("i", $user_id);
$recent_stmt->execute();
$recent_orders = $recent_stmt->get_result();
?>

<style>
.dashboard-container {
    max-width: 1100px;
    margin: auto;
    padding: 30px 20px;
}

/* HEADER */
.dashboard-header {
    text-align: center;
    margin-bottom: 30px;
}

/* PROFILE */
.profile-box {
    background: #fff;
    border: 1px solid #eee;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* STATS */
.stat-box {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}
.stat-card {
    flex: 1;
    min-width: 200px;
    background: #000;
    color: #fff;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
}
.stat-card h3 {
    margin: 0;
    font-size: 26px;
}

/* QUICK ACTIONS */
.quick-actions {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}
.quick-actions a {
    flex: 1;
    text-align: center;
    padding: 12px;
    background: #f1f1f1;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    font-weight: 600;
}

/* TABLE */
.recent-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}
.recent-table th, .recent-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}
.recent-table th {
    background: #000;
    color: #fff;
}

/* STATUS COLORS */
.status {
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
    color: #fff;
}
.pending { background: orange; }
.completed { background: green; }
.cancelled { background: red; }

/* BUTTON */
.view-btn {
    padding: 5px 10px;
    background: #007bff;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
    font-size: 12px;
}

@media(max-width:768px){
    .stat-box, .quick-actions {
        flex-direction: column;
    }
}
</style>

<div class="dashboard-container">

    <!-- HEADER -->
    <div class="dashboard-header">
        <h2>Welcome, <?= htmlspecialchars($full_name) ?> 👋</h2>
        <p>Your shopping activity overview</p>
    </div>

    <!-- PROFILE -->
    <div class="profile-box">
        <h4>Profile Info</h4>

        <?php if ($user_result): ?>
            <p><strong>Name:</strong> <?= htmlspecialchars($user_result['full_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user_result['email']) ?></p>
            <p><strong>Member Since:</strong> <?= date("M d, Y", strtotime($user_result['created_at'])) ?></p>
        <?php else: ?>
            <p>User info not found.</p>
        <?php endif; ?>

        <a href="my_account.php" class="view-btn">Edit Profile</a>
    </div>

    <!-- STATS -->
    <div class="stat-box">
        <div class="stat-card">
            <h3><?= $total_orders ?></h3>
            <p>Total Orders</p>
        </div>
        <div class="stat-card">
            <h3><?= $recent_orders->num_rows ?></h3>
            <p>Recent Orders</p>
        </div>
        <div class="stat-card">
            <h3><?= date('M d, Y') ?></h3>
            <p>Today</p>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="quick-actions">
        <a href="cart.php">🛒 View Cart</a>
        <a href="order_history.php">📦 Orders</a>
        <a href="track_order.php">🚚 Track Order</a>
    </div>

    <!-- RECENT ORDERS -->
    <h4>Recent Orders</h4>

    <table class="recent-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($recent_orders->num_rows > 0): ?>
            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                <?php
                    $status = strtolower($order['order_status'] ?? '');
                    $class = "pending";

                    if ($status == 'completed') $class = "completed";
                    if ($status == 'cancelled') $class = "cancelled";
                ?>
                <tr>
                    <td>#<?= $order['order_id'] ?></td>
                    <td><?= date("M d, Y", strtotime($order['order_date'])) ?></td>
                    <td>BDT <?= number_format($order['total_amount'], 2) ?></td>
                    <td>
                        <span class="status <?= $class ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </td>
                    <td>
                        <a href="track_order.php?id=<?= $order['order_id'] ?>" class="view-btn">
                            View
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No orders found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>

<?php include('footer.php'); ?>