<?php
session_start();
include('config.php'); // Ensure DB connection for stats

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// FETCH LIVE STATS FOR THE "PRO" LOOK
// FETCH LIVE STATS - CORRECTED
$total_products = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];

$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];

// FIX: Added fetch_assoc() before trying to access the ['total'] key
$revenue_query = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE order_status='delivered'");
$revenue_data = $revenue_query->fetch_assoc();
$total_revenue = $revenue_data['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | STRENDio Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="strendio_logo2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0d6efd;
            --dark-bg: #0f172a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* SIDEBAR STYLES */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--dark-bg);
            position: fixed;
            left: 0;
            top: 0;
            color: #fff;
            padding: 20px;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar .logo-area {
            padding: 10px 0 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .nav-link {
            color: #94a3b8;
            padding: 12px 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: 0.3s;
            text-decoration: none;
            margin-bottom: 5px;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .nav-link i { font-size: 1.2rem; }

        /* MAIN CONTENT AREA */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .admin-profile-card {
            background: #fff;
            padding: 8px 15px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            cursor: pointer;
            border: 1px solid #e2e8f0;
        }

        .admin-profile-card img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* DASHBOARD CARDS */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .bg-light-blue { background: #e0f2fe; color: #0369a1; }
        .bg-light-green { background: #dcfce7; color: #15803d; }
        .bg-light-purple { background: #f3e8ff; color: #7e22ce; }

        .action-card {
            background: #fff;
            border-radius: 16px;
            text-align: center;
            padding: 30px;
            height: 100%;
            border: 1px solid #f1f5f9;
            transition: 0.3s;
        }
        
        .action-card:hover { border-color: var(--primary-color); background: #fdfdff; }

        @media (max-width: 992px) {
            .sidebar { left: -100%; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo-area text-center">
        <img src="strendio_logo2.png" alt="Logo" style="height: 40px; filter: brightness(0) invert(1);">
        <h5 class="mt-2 fw-bold" style="letter-spacing: 1px;">STRENDio</h5>
    </div>

    <div class="nav flex-column">
        <a href="admin_dashboard.php" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="manage_products.php" class="nav-link"><i class="bi bi-box-seam"></i> Products</a>
        <a href="view_orders.php" class="nav-link"><i class="bi bi-cart-check"></i> Orders</a>
        <a href="admin_team.php" class="nav-link"><i class="bi bi-people"></i> Admin Team</a>
        <a href="admin_profile.php" class="nav-link"><i class="bi bi-person-gear"></i> Settings</a>
        <hr style="opacity: 0.1;">
        <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    
    <!-- TOPBAR -->
    <div class="topbar">
        <div>
            <h4 class="fw-bold mb-0">Welcome back, Admin 👋</h4>
            <p class="text-muted small">Here's what's happening today.</p>
        </div>
        
        <div class="admin-profile-card" onclick="window.location.href='admin_profile.php'">
            <div class="text-end d-none d-md-block">
                <div class="fw-bold small" style="line-height: 1;"><?= htmlspecialchars($_SESSION['admin_full_name']) ?></div>
                <span class="text-muted" style="font-size: 10px;"><?= htmlspecialchars($_SESSION['admin_designation']) ?></span>
            </div>
            <img src="uploads/admin/<?= htmlspecialchars($_SESSION['admin_image']) ?>" alt="Admin">
        </div>
    </div>

    <!-- STATS ROW -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-light-blue"><i class="bi bi-box-seam"></i></div>
                <h3 class="fw-bold"><?= $total_products ?></h3>
                <p class="text-muted mb-0">Total Products</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-light-purple"><i class="bi bi-bag-check"></i></div>
                <h3 class="fw-bold"><?= $total_orders ?></h3>
                <p class="text-muted mb-0">Total Orders</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-light-green"><i class="bi bi-currency-dollar"></i></div>
                <h3 class="fw-bold">৳ <?= number_format($total_revenue, 0) ?></h3>
                <p class="text-muted mb-0">Total Revenue (Delivered)</p>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS GRID -->
    <h5 class="fw-bold mb-4">Quick Management</h5>
    <div class="row g-4">
        
        <div class="col-md-3">
            <div class="action-card">
                <div class="fs-2 text-primary mb-2"><i class="bi bi-plus-circle-dotted"></i></div>
                <h6 class="fw-bold">Add Product</h6>
                <p class="small text-muted">Upload new inventory</p>
                <a href="add_product.php" class="btn btn-sm btn-outline-primary stretched-link">Launch</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="action-card">
                <div class="fs-2 text-success mb-2"><i class="bi bi-kanban"></i></div>
                <h6 class="fw-bold">Manage Stock</h6>
                <p class="small text-muted">Edit price & details</p>
                <a href="manage_products.php" class="btn btn-sm btn-outline-success stretched-link">View List</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="action-card">
                <div class="fs-2 text-warning mb-2"><i class="bi bi-receipt"></i></div>
                <h6 class="fw-bold">Review Orders</h6>
                <p class="small text-muted">Process shipping</p>
                <a href="view_orders.php" class="btn btn-sm btn-outline-warning stretched-link">Check</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="action-card">
                <div class="fs-2 text-info mb-2"><i class="bi bi-shield-lock"></i></div>
                <h6 class="fw-bold">Admin Team</h6>
                <p class="small text-muted">Control permissions</p>
                <a href="admin_team.php" class="btn btn-sm btn-outline-info stretched-link">Manage</a>
            </div>
        </div>

    </div>

</div>

</body>
</html>