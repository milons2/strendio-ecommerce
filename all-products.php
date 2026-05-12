<?php
session_start();
include('config.php');

$category = $_GET['category'] ?? '';
$filter = "";

// CATEGORY FILTER
if (!empty($category)) {
    $category = mysqli_real_escape_string($conn, $category);
    $filter = "WHERE c.slug = '$category'";
}

$query = "SELECT p.*, c.slug 
          FROM products p
          JOIN categories c ON p.category_id = c.id
          $filter
          ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? 0;
$defaultImage = "uploads/default.png";
?>

<?php include('header.php'); ?>

<!-- GOOGLE FONTS & ICONS -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
body { background: #fdfdfd; font-family: 'Inter', sans-serif; }

/* PRO SHOP HEADER */
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

.product-img { width: 100%; height: 320px; object-fit: cover; }

/* OVERLAYS */
.badge-new {
    position: absolute; top: 15px; left: 15px;
    background: #1a1a1a; color: #fff;
    font-size: 10px; font-weight: 700; padding: 5px 10px;
    border-radius: 4px; z-index: 2;
}
.wishlist {
    position: absolute; top: 15px; right: 15px;
    background: #fff; border-radius: 50%;
    width: 35px; height: 35px; display: flex;
    align-items: center; justify-content: center;
    cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    z-index: 2; transition: 0.3s;
}
.wishlist.active { background: #ff4757; color: #fff; }

/* CARD BODY */
.product-title {
    font-size: 16px; font-weight: 600; color: #222;
    margin-bottom: 8px; min-height: 44px;
}
.price { font-size: 18px; font-weight: 700; color: #000; }

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

    <!-- PRO LEVEL HEADER -->
    <div class="shop-header d-flex justify-content-between align-items-center">
        <div>
            <span class="promo-badge">EID-UL-ADHA 2026</span>
            <h2>Strendio Eid Collection</h2>
            <p class="text-muted mb-0">Discover the finest trending styles for your Eid celebrations.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <h5 class="mb-1 text-danger">Flat 25% OFF</h5>
            <p class="small text-muted mb-0">Discount applied automatically at checkout</p>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="filter-bar">
        <a href="all-products.php" class="<?= $category==''?'active':'' ?>">All Pieces</a>
        <a href="all-products.php?category=mens" class="<?= $category=='mens'?'active':'' ?>">Men</a>
        <a href="all-products.php?category=womens" class="<?= $category=='womens'?'active':'' ?>">Women</a>
        <a href="all-products.php?category=kids" class="<?= $category=='kids'?'active':'' ?>">Kids</a>
        <a href="all-products.php?category=accessories" class="<?= $category=='accessories'?'active':'' ?>">Accessories</a>
    </div>

    <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($result)): 
                $imagePath = "uploads/" . $product['slug'] . "/" . $product['image'];
                if (empty($product['image']) || !file_exists($imagePath)) $imagePath = $defaultImage;
                $isNew = strtotime($product['created_at']) > strtotime('-7 days');
                
                // Wishlist check
                $isWish = false;
                if($isLoggedIn){
                    $wishCheck = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$user_id AND product_id=".$product['id']);
                    $isWish = mysqli_num_rows($wishCheck) > 0;
                }
            ?>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card">
                    <?php if ($isNew): ?>
                        <span class="badge-new">NEW ARRIVAL</span>
                    <?php endif; ?>

                    <div class="wishlist <?= $isWish ? 'active' : '' ?>" onclick="toggleWishlist(this, <?= $product['id'] ?>)">
                        <i class="bi bi-heart-fill"></i>
                    </div>

                    <a href="product_details.php?id=<?= $product['id'] ?>">
                        <img src="<?= htmlspecialchars($imagePath) ?>" class="product-img">
                    </a>

                    <div class="card-body p-4">
                        <div class="product-title">
                            <?= htmlspecialchars($product['name']) ?>
                        </div>
                        <div class="price mb-3">
                            ৳ <?= number_format($product['price'], 0) ?>
                        </div>

                        <div class="d-grid gap-2">
                            <?php if ($isLoggedIn): ?>
                                <button class="btn btn-dark btn-sm rounded-pill py-2" onclick="addToCart(<?= $product['id'] ?>)">
                                    Add to Cart
                                </button>
                            <?php else: ?>
                                <a href="login_form.php" class="btn btn-outline-dark btn-sm rounded-pill py-2">
                                    Add To Cart
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 py-5 text-center">
                <i class="bi bi-search mb-3 d-block text-muted" style="font-size: 3rem;"></i>
                <h4 class="text-muted">No products found in this category</h4>
                <a href="all-products.php" class="btn btn-dark mt-3">View All Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- TRUST INFO SECTION -->
<section class="trust-info">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 info-item">
                <i class="bi bi-box-seam"></i>
                <h6>Fast Shipping</h6>
                <p>Doorstep delivery within 48 hours</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-arrow-repeat"></i>
                <h6>Easy Returns</h6>
                <p>7-day hassle-free return policy</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-shield-lock"></i>
                <h6>Secure Checkout</h6>
                <p>Encrypted SSL secure payments</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-stars"></i>
                <h6>Premium Quality</h6>
                <p>100% authentic curated fabrics</p>
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
        window.location.href = "cart.php";
    });
}

function toggleWishlist(el, id) {
    fetch('wishlist.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + id
    })
    .then(res => res.text())
    .then(data => {
        if (data.includes('login_required')) {
            alert('Please login first');
            return;
        }
        el.classList.toggle('active');
    });
}
</script>

<?php include('footer.php'); ?>