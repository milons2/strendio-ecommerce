<?php
session_start();
include('config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Status Update (AJAX or Post)
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
}

// Filter logic
$status_filter = $_GET['status'] ?? 'all';
$where = ($status_filter !== 'all') ? "WHERE o.order_status = '$status_filter'" : "";

$query = "SELECT o.*, u.full_name, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          $where 
          ORDER BY o.order_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders Management | STRENDio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .sidebar { width: 260px; height: 100vh; background: #0f172a; position: fixed; left: 0; top: 0; padding: 20px; color: #fff; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 40px; }
        .nav-link { color: #94a3b8; padding: 12px 15px; border-radius: 10px; display: flex; align-items: center; gap: 12px; text-decoration: none; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: #fff; }

        /* Order Table Styling */
        .order-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .table thead { background: #f1f5f9; border-bottom: 2px solid #e2e8f0; }
        .table thead th { font-weight: 600; font-size: 12px; text-transform: uppercase; color: #64748b; padding: 15px; }
        .table tbody td { padding: 18px 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }

        /* Status Badges */
        .badge-status { padding: 6px 12px; border-radius: 50px; font-weight: 600; font-size: 11px; text-transform: capitalize; }
        .status-pending { background: #fff7ed; color: #9a3412; }
        .status-processing { background: #eff6ff; color: #1d4ed8; }
        .status-shipped { background: #f5f3ff; color: #6d28d9; }
        .status-delivered { background: #f0fdf4; color: #15803d; }
        .status-cancelled { background: #fef2f2; color: #b91c1c; }

        .btn-view { background: #f1f5f9; color: #475569; border: none; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 500; transition: 0.2s; }
        .btn-view:hover { background: #e2e8f0; color: #0f172a; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-4"><img src="strendio_logo2.png" style="height: 35px; filter: brightness(0) invert(1);"></div>
    <div class="nav flex-column">
        <a href="admin_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="manage_products.php" class="nav-link"><i class="bi bi-box-seam"></i> Products</a>
        <a href="view_orders.php" class="nav-link active"><i class="bi bi-cart-check"></i> Orders</a>
        <a href="admin_profile.php" class="nav-link"><i class="bi bi-person"></i> Settings</a>
        <hr style="opacity:0.1">
        <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Customer Orders</h3>
            <p class="text-muted small mb-0">Track and manage your store sales</p>
        </div>
        <div class="dropdown">
            <button class="btn btn-white border shadow-sm dropdown-toggle" style="border-radius:10px;" type="button" data-bs-toggle="dropdown">
                Filter: <?= ucfirst($status_filter) ?>
            </button>
            <ul class="dropdown-menu shadow border-0">
                <li><a class="dropdown-menu-item p-2 d-block text-decoration-none text-dark" href="?status=all">All Orders</a></li>
                <li><a class="dropdown-menu-item p-2 d-block text-decoration-none text-dark" href="?status=pending">Pending</a></li>
                <li><a class="dropdown-menu-item p-2 d-block text-decoration-none text-dark" href="?status=delivered">Delivered</a></li>
            </ul>
        </div>
    </div>

    <div class="order-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Customer</th>
                        <th>Date & Time</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): 
                        $status_class = "status-" . strtolower($row['order_status']);
                    ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark">#<?= $row['order_id'] ?></span>
                        </td>
                        <td>
                            <div class="fw-medium text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                        </td>
                        <td>
                            <div class="text-dark small"><?= date("M d, Y", strtotime($row['order_date'])) ?></div>
                            <div class="text-muted" style="font-size: 11px;"><?= date("h:i A", strtotime($row['order_date'])) ?></div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">৳<?= number_format($row['total_amount'], 2) ?></span>
                        </td>
                        <td>
                            <span class="badge-status <?= $status_class ?>"><?= $row['order_status'] ?></span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn-view" onclick="openOrderModal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                Details
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ORDER DETAILS MODAL -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Order #<span id="modalOrderId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body py-4">
                    <input type="hidden" name="order_id" id="formOrderId">
                    <div class="mb-4">
                        <label class="text-muted small fw-bold mb-2">UPDATE SHIPPING STATUS</label>
                        <select name="status" class="form-select border-0 bg-light py-3" style="border-radius:12px;">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="p-3 bg-light" style="border-radius:12px;">
                        <p class="small mb-1"><strong>Customer:</strong> <span id="modalCustomer"></span></p>
                        <p class="small mb-0"><strong>Total:</strong> ৳<span id="modalTotal"></span></p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" name="update_status" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius:12px;">Update Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openOrderModal(order) {
    document.getElementById('modalOrderId').innerText = order.order_id;
    document.getElementById('formOrderId').value = order.order_id;
    document.getElementById('modalCustomer').innerText = order.full_name;
    document.getElementById('modalTotal').innerText = parseFloat(order.total_amount).toLocaleString();
    
    const select = document.querySelector('select[name="status"]');
    select.value = order.order_status;

    var myModal = new bootstrap.Modal(document.getElementById('orderModal'));
    myModal.show();
}
</script>

</body>
</html>