<?php
session_start();
include('config.php');

$isLoggedIn = isset($_SESSION['user_id']);

/* CATEGORY FILTER */
$filter = $_GET['type'] ?? '';

// category_id = 1 for Mens
$query = "SELECT * FROM products WHERE category_id = 1";

/* SIMPLE CATEGORY FILTER */
if ($filter == "shirt") {
    $query .= " AND name LIKE '%shirt%'";
} elseif ($filter == "panjabi") {
    $query .= " AND name LIKE '%panjabi%'";
} elseif ($filter == "tshirt") {
    $query .= " AND (name LIKE '%t-shirt%' OR name LIKE '%tshirt%')";
} elseif ($filter == "pant") {
    $query .= " AND name LIKE '%pant%'";
} elseif ($filter == "shoe") {
    $query .= " AND name LIKE '%shoe%'";
} elseif ($filter == "accessories") {
    $query .= " AND (name LIKE '%belt%' OR name LIKE '%wallet%' OR name LIKE '%watch%')";
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$defaultImage = "uploads/mens/default.png";
?>

<?php include('header.php'); ?>

<!-- GOOGLE FONTS & ICONS -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
body { background: #fdfdfd; font-family: 'Inter', sans-serif; }

/* PRO SHOP HEADER (BD ZONE) */
.shop-header {
    background: #fff;
    padding: 40px;
    border-radius: 20px;
    margin-bottom: 40px;
    border: 1px solid #eee;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
}
.promo-badge {
    background: #000;
    color: #fff;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-block;
    margin-bottom: 10px;
}
.shop-header h2 { font-family: 'Playfair Display', serif; font-weight: 700; color: #1a1a1a; }

/* FILTER BAR */
.filter-bar {
    display: flex; justify-content: center; gap: 12px;
    margin-bottom: 40px; flex-wrap: wrap;
}
.filter-bar a {
    padding: 8px 20px; border-radius: 50px;
    border: 1px solid #eee; text-decoration: none;
    font-size: 14px; color: #666; transition: 0.3s;
    background: #fff;
}
.filter-bar a:hover, .filter-bar a.active {
    background: #1a1a1a; color: #fff; border-color: #1a1a1a;
}

/* PRODUCT CARD */
.product-card {
    border: none; border-radius: 20px;
    overflow: hidden; background: #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.04);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    height: 100%; position: relative;
}
.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.product-img { width: 100%; height: 320px; object-fit: cover; background: #f9f9f9; }

/* INFO SECTION */
.product-title {
    font-size: 16px; font-weight: 600; color: #222;
    margin-bottom: 8px; min-height: 44px;
}
.price { font-size: 18px; font-weight: 700; color: #000; margin-bottom: 15px; }

/* TRUST INFO SECTION */
.trust-info {
    background: #fff; padding: 80px 0;
    border-top: 1px solid #eee; margin-top: 60px;
}
.info-item i { font-size: 32px; color: #1a1a1a; margin-bottom: 20px; display: block; }
.info-item h6 { font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 13px; }
.info-item p { font-size: 13px; color: #888; }
</style>

<div class="container py-5">

    <!-- BD ZONE EID-26 HEADER -->
    <div class="shop-header d-flex justify-content-between align-items-center">
        <div>
            <span class="promo-badge">EID-UL-ADHA 2026</span>
            <h2>Men's Eid Collection</h2>
            <p class="text-muted mb-0">Premium Panjabis, Shirts, and Essentials for your festive look.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <h5 class="mb-1 text-danger">Flat 25% OFF</h5>
            <p class="small text-muted mb-0">Automatic discount applied at checkout</p>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <a href="mens.php" class="<?= $filter==''?'active':'' ?>">All Items</a>
        <a href="mens.php?type=shirt" class="<?= $filter=='shirt'?'active':'' ?>">Shirts</a>
        <a href="mens.php?type=panjabi" class="<?= $filter=='panjabi'?'active':'' ?>">Panjabi</a>
        <a href="mens.php?type=tshirt" class="<?= $filter=='tshirt'?'active':'' ?>">T-Shirts</a>
        <a href="mens.php?type=pant" class="<?= $filter=='pant'?'active':'' ?>">Pants</a>
        <a href="mens.php?type=shoe" class="<?= $filter=='shoe'?'active':'' ?>">Shoes</a>
        <a href="mens.php?type=accessories" class="<?= $filter=='accessories'?'active':'' ?>">Accessories</a>
    </div>

    <!-- PRODUCT GRID -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $img = "uploads/mens/" . $row['image'];
                if (empty($row['image']) || !file_exists($img)) {
                    $img = $defaultImage;
                }
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card">
                    
                    <a href="product_details.php?id=<?= $row['id'] ?>">
                        <img src="<?= htmlspecialchars($img) ?>" class="product-img">
                    </a>

                    <div class="card-body p-4 text-center">
                        <div class="product-title">
                            <?= htmlspecialchars($row['name']) ?>
                        </div>
                        <div class="price">
                            ৳ <?= number_format($row['price'], 0) ?>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="product_details.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm rounded-pill">
                                View Details
                            </a>
                            <?php if ($isLoggedIn): ?>
                                <button class="btn btn-dark btn-sm rounded-pill" onclick="addToCart(<?= $row['id'] ?>)">
                                    Add to Cart
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">No items found in this category.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- TRUST INFO SECTION -->
<section class="trust-info">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 info-item">
                <i class="bi bi-truck"></i>
                <h6>Inside Dhaka</h6>
                <p>Delivery within 24-48 Hours</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-geo-alt"></i>
                <h6>Outside Dhaka</h6>
                <p>Reliable shipping in 3-5 Days</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-arrow-repeat"></i>
                <h6>Easy Exchange</h6>
                <p>7-day hassle-free exchange policy</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-headset"></i>
                <h6>Customer Care</h6>
                <p>Active support for your shopping</p>
            </div>
        </div>
    </div>
</section>

<script>
function addToCart(id) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + id + '&quantity=1'
    }).then(() => {
        alert("Product added to your cart!");
        window.location.href = "cart.php";
    });
}
</script>

<?php include('footer.php'); ?>