<?php
session_start();
include('config.php');

// Check login status
$isLoggedIn = isset($_SESSION['user_id']);

// Category ID for women's products (Ensure this matches your DB)
$category_id = 2; 

// Fetch products with Category Slug for correct image paths
$query = "SELECT p.*, c.slug FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.category_id = $category_id 
          ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);

$defaultImage = "uploads/womens/default.png";
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

.product-img { width: 100%; height: 380px; object-fit: cover; background: #f9f9f9; }

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

.btn-pill { border-radius: 50px; padding: 8px 20px; font-weight: 600; transition: 0.3s; }
</style>

<div class="container py-5">

    <!-- BD ZONE EID-26 HEADER -->
    <div class="shop-header d-flex justify-content-between align-items-center">
        <div>
            <span class="promo-badge">EID-UL-ADHA 2026</span>
            <h2>Women's Eid Collection</h2>
            <p class="text-muted mb-0">Elevate your elegance with our premium trending ensembles.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <h5 class="mb-1 text-danger">Flat 25% OFF</h5>
            <p class="small text-muted mb-0">Automatic festive discount applied</p>
        </div>
    </div>

    <!-- PRODUCT GRID -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $img = "uploads/womens/" . $row['image'];
                if (empty($row['image']) || !file_exists($img)) {
                    $img = $defaultImage;
                }
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card">
                    
                    <a href="product_details.php?id=<?= $row['id'] ?>">
                        <img src="<?= htmlspecialchars($img) ?>" class="product-img" alt="<?= htmlspecialchars($row['name']) ?>">
                    </a>

                    <div class="card-body p-4 text-center">
                        <div class="product-title">
                            <?= htmlspecialchars($row['name']) ?>
                        </div>
                        <div class="price">
                            ৳ <?= number_format($row['price'], 0) ?>
                        </div>

                        <div class="d-grid gap-2 mt-auto">
                            <a href="product_details.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm btn-pill">
                                View Details
                            </a>
                            
                            <?php if ($isLoggedIn): ?>
                            <form method="post" action="add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-dark btn-sm btn-pill w-100">
                                    Add to Cart
                                </button>
                            </form>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-dark btn-sm btn-pill">Login to Shop</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-bag-x text-muted" style="font-size: 3rem;"></i>
                <h4 class="text-muted mt-3">The collection is currently being updated.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- TRUST INFO SECTION -->
<section class="trust-info">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 info-item">
                <i class="bi bi-clock-history"></i>
                <h6>Quick Delivery</h6>
                <p>24h Inside Dhaka | 72h Nationwide</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-patch-check"></i>
                <h6>Premium Quality</h6>
                <p>Curated fabrics for ultimate comfort</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-shield-lock"></i>
                <h6>Secure Checkout</h6>
                <p>Payment via SSLCommerz / bKash</p>
            </div>
            <div class="col-md-3 info-item">
                <i class="bi bi-arrow-left-right"></i>
                <h6>7-Day Return</h6>
                <p>Hassle-free local exchange policy</p>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>