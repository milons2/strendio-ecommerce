<?php
session_start();
include('config.php');

$is_logged_in = isset($_SESSION['user_id']);
$orders = [];
$emailError = "";

/**
 * Fetch orders with a clean, prepared statement approach
 */
function fetchOrders($conn, $identifier = null, $userId = null) {
    if ($userId) {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
        $stmt->bind_param("i", $userId);
    } elseif (!empty($identifier)) {
        $stmt = $conn->prepare("
            SELECT o.* 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE u.email = ? OR u.phone = ? 
            ORDER BY o.order_date DESC
        ");
        $stmt->bind_param("ss", $identifier, $identifier);
    } else {
        return [];
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Logic for fetching data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_logged_in) {
    $identifier = trim($_POST['identifier'] ?? '');
    if (empty($identifier)) {
        $emailError = "Please enter your registered email or phone.";
    } else {
        $orders = fetchOrders($conn, $identifier);
    }
} elseif ($is_logged_in) {
    $userId = $_SESSION['user_id'];
    $orders = fetchOrders($conn, null, $userId);
}

include 'header.php';
?>

<!-- GOOGLE FONTS & BOOTSTRAP ICONS -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    
    .track-header { padding: 40px 0; text-align: center; }
    .track-header h2 { font-weight: 800; letter-spacing: -1px; color: #1a1a1a; }

    /* SEARCH CARD */
    .search-container {
        max-width: 500px;
        margin: 0 auto 50px;
        background: #fff;
        padding: 30px;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }

    /* ORDER CARD */
    .order-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid #eee;
        transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .order-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

    /* STATUS BADGES */
    .badge-status {
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-pending { background: #fff4e5; color: #ff9800; }
    .status-processing { background: #e3f2fd; color: #2196f3; }
    .status-shipped { background: #f3e5f5; color: #9c27b0; }
    .status-delivered { background: #e8f5e9; color: #4caf50; }

    /* PROGRESS TRACKER */
    .track-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-top: 40px;
        padding-bottom: 20px;
    }
    .track-steps::before {
        content: "";
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 4px;
        background: #f1f1f1;
        z-index: 1;
    }
    .step-item {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }
    .step-dot {
        width: 40px;
        height: 40px;
        background: #fff;
        border: 4px solid #f1f1f1;
        border-radius: 50%;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
        color: #ccc;
    }
    .step-item.active .step-dot {
        border-color: #000;
        background: #000;
        color: #fff;
    }
    .step-label { font-size: 12px; font-weight: 600; color: #999; }
    .step-item.active .step-label { color: #000; }

    .btn-pill { border-radius: 50px; padding: 10px 25px; font-weight: 600; }

    @media (max-width: 576px) {
        .track-steps { flex-direction: column; gap: 20px; align-items: flex-start; }
        .track-steps::before { display: none; }
        .step-item { display: flex; align-items: center; gap: 15px; }
        .step-dot { margin: 0; width: 30px; height: 30px; }
    }
</style>

<div class="container py-5">
    
    <div class="track-header">
        <i class="bi bi-geo-alt-fill fs-1 text-primary mb-3"></i>
        <h2>Track Your Delivery</h2>
        <p class="text-muted">Enter your details to see the real-time status of your parcel.</p>
    </div>

    <!-- SEARCH SECTION (ONLY FOR GUESTS) -->
    <?php if (!$is_logged_in): ?>
    <div class="search-container">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Identify Your Order</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="identifier" class="form-control border-start-0" placeholder="Email or Phone Number" required>
                </div>
            </div>
            <button type="submit" class="btn btn-dark w-100 btn-pill">Find Order Details</button>
            <?php if ($emailError): ?>
                <div class="alert alert-danger mt-3 py-2 small"><?= $emailError ?></div>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>

    <!-- ORDERS LIST -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): 
                    $status = strtolower($order['order_status'] ?? 'pending');
                ?>
                <div class="order-card">
                    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                        <div>
                            <span class="text-muted small">ORDER ID</span>
                            <h4 class="fw-bold mb-1">#<?= $order['order_id'] ?></h4>
                            <p class="small text-muted mb-0">Placed on: <?= date('d M, Y', strtotime($order['order_date'])) ?></p>
                        </div>
                        <div class="text-md-end">
                            <span class="badge-status status-<?= $status ?> mb-2 d-inline-block">
                                <i class="bi bi-record-circle me-1"></i> <?= ucfirst($status) ?>
                            </span>
                            <h4 class="fw-bold mb-0">৳ <?= number_format($order['total_amount'], 0) ?></h4>
                        </div>
                    </div>

                    <!-- STEPPER LOGIC -->
                    <div class="track-steps">
                        <!-- Step 1 -->
                        <div class="step-item active">
                            <div class="step-dot"><i class="bi bi-file-earmark-check"></i></div>
                            <div class="step-label">Placed</div>
                        </div>

                        <!-- Step 2 -->
                        <?php $s2 = in_array($status, ['processing', 'shipped', 'delivered']); ?>
                        <div class="step-item <?= $s2 ? 'active' : '' ?>">
                            <div class="step-dot"><i class="bi bi-gear"></i></div>
                            <div class="step-label">Processing</div>
                        </div>

                        <!-- Step 3 -->
                        <?php $s3 = in_array($status, ['shipped', 'delivered']); ?>
                        <div class="step-item <?= $s3 ? 'active' : '' ?>">
                            <div class="step-dot"><i class="bi bi-truck"></i></div>
                            <div class="step-label">Shipped</div>
                        </div>

                        <!-- Step 4 -->
                        <?php $s4 = ($status == 'delivered'); ?>
                        <div class="step-item <?= $s4 ? 'active' : '' ?>">
                            <div class="step-dot"><i class="bi bi-house-check"></i></div>
                            <div class="step-label">Delivered</div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Expected Delivery: Within 3-5 Business Days</span>
                        <a href="product_details.php" class="btn btn-sm btn-outline-dark btn-pill">Details</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' || $is_logged_in): ?>
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" style="width: 100px; opacity: 0.3;" alt="">
                    <h5 class="mt-3 text-muted">No orders found for this account.</h5>
                    <a href="all-products.php" class="btn btn-dark btn-pill mt-3">Browse Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>