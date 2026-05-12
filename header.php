<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>STRENDio | Full Stack eCommerce System</title>

<link rel="icon" href="strendio_logo2.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

/* ===== TOP BAR (SMALL PRO STYLE) ===== */
.top-header {
    background: #f8f9fa;
    font-size: 12px;
    padding: 3px 0;
    border-bottom: 1px solid #eee;
}

.top-header a {
    color: #444;
    text-decoration: none;
    margin-left: 12px;
    font-weight: 500;
}

.top-header a:hover {
    color: #0d6efd;
}

/* ===== MAIN NAVBAR ===== */
.navbar {
    background: #fff;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

/* LOGO */
.logo img {
    height: 40px;
}

/* MENU CENTER */
.navbar-nav {
    gap: 25px;
}

.navbar .nav-link {
    color: #222 !important;
    font-weight: 600;
    font-size: 15px;
    position: relative;
}

.navbar .nav-link:hover {
    color: #0d6efd !important;
}

/* underline hover */
.navbar .nav-link::after {
    content: "";
    width: 0%;
    height: 2px;
    background: #0d6efd;
    position: absolute;
    bottom: -5px;
    left: 0;
    transition: 0.3s;
}

.navbar .nav-link:hover::after {
    width: 100%;
}

/* SEARCH */
.search-box {
    position: relative;
}

.search-box input {
    border-radius: 20px;
    height: 34px;
    font-size: 13px;
    padding-left: 30px;
    border: 1px solid #ddd;
}

.search-box i {
    position: absolute;
    left: 10px;
    top: 9px;
    font-size: 12px;
    color: #888;
}

/* RIGHT */
.right-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* CART */
.cart-icon {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    text-decoration: none;
}

.cart-icon:hover {
    color: #0d6efd;
}

</style>

</head>
<body>

<div class="top-header">
    <div class="container d-flex justify-content-between">

        <div>
           💼 Full Stack Portfolio Project – STRENDio
        </div>

        <div>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login_form.php">Login</a>
                <a href="register_form.php">Register</a>
            <?php else: ?>
                <a href="user_dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php endif; ?>

            <a href="track_order.php">Track Order</a>
        </div>

    </div>
</div>

<!-- 🔹 MAIN NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container d-flex align-items-center justify-content-between">

        <!-- LOGO -->
        <a class="navbar-brand logo d-flex align-items-center" href="index.php">
            <img src="strendio_logo2.png" class="me-2">
            <strong style="font-size:18px;">STRENDio</strong>
        </a>

        <!-- CENTER MENU -->
        <div class="d-none d-lg-flex mx-auto">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="mens.php">Men</a></li>
                <li class="nav-item"><a class="nav-link" href="womens.php">Women</a></li>
                <li class="nav-item"><a class="nav-link" href="kids.php">Kids</a></li>
                <li class="nav-item"><a class="nav-link" href="all-products.php">Products</a></li>
            </ul>
        </div>

        <!-- RIGHT SIDE -->
        <div class="right-section">

            <!-- SEARCH -->
            <form class="d-flex search-box" method="GET" action="search.php">
                <i class="fa fa-search"></i>
                <input type="search" name="q" class="form-control" placeholder="Search..." required>
            </form>

            <!-- CART -->
            <a href="cart.php" class="cart-icon">
                🛒 <?= $cart_count ?>
            </a>

        </div>

    </div>
</nav>