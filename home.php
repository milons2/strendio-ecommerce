<?php
session_start();
include('config.php');
include('header.php');
?>

<style>

/* HERO */
.hero {
    position: relative;
    background: url('uploads/assets/banner.png') center/cover no-repeat;
    height: 420px;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 50px;
}

.hero::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.hero-content {
    position: absolute;
    z-index: 2;
    color: #fff;
    top: 50%;
    left: 50px;
    transform: translateY(-50%);
}

.hero h1 {
    font-size: 42px;
    font-weight: 700;
}

.hero p {
    opacity: 0.9;
}

/* CATEGORY */
.category-card {
    background: #fff;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    transition: 0.3s;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}

.category-card:hover {
    transform: translateY(-6px);
}

/* PRODUCT */
.product-card {
    border-radius: 15px;
    overflow: hidden;
    background: #fff;
    transition: 0.3s;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.product-card:hover {
    transform: translateY(-6px);
}

.product-card img {
    height: 220px;
    object-fit: cover;
}

.price {
    color: #0d6efd;
    font-weight: bold;
}

/* SECTION */
.section-title {
    font-weight: 700;
    margin-bottom: 25px;
}

/* UPDATED NEWSLETTER BANNER */
.newsletter-banner {
    position: relative;
    background: url('uploads/assets/banner.png') center/center no-repeat;
    background-size: cover;
    min-height: 300px; /* Increased height */
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 50px;
    overflow: hidden;
    width: 100%; /* Fits full container width */
}

/* ELITE NEWSLETTER BANNER - 2026 EDITION */
.newsletter-banner {
    position: relative;
    width: 100%;
    min-height: 250px; /* Increased height for professional breathing room */
    background: #fdfdfd; /* Sophisticated light cream/off-white */
    border-radius: 24px;
    margin: 60px 0;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #eee; /* Subtle border for definition */
    box-shadow: 0 20px 40px rgba(0,0,0,0.03);
}

/* Subtle abstract fashion background pattern */
.newsletter-banner::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(#e5e5e5 1px, transparent 1px);
    background-size: 30px 30px;
    opacity: 0.3;
}

.newsletter-overlay {
    position: relative;
    z-index: 2;
    max-width: 900px;
    width: 90%;
    text-align: center;
}

.newsletter-overlay h2 {
    /* Using a serif font here adds instant luxury */
    font-family: 'Playfair Display', serif; 
    font-size: 3.5rem; /* Large, bold presence */
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: -1px;
    margin-bottom: 15px;
}

.newsletter-overlay p {
    font-family: 'Inter', sans-serif;
    font-size: 1.1rem;
    color: #666;
    letter-spacing: 0.5px;
    margin-bottom: 40px;
    text-transform: uppercase;
}

.newsletter-form {
    display: flex;
    background: #fff;
    padding: 8px;
    border-radius: 100px;
    border: 1px solid #e0e0e0;
    transition: 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.newsletter-form:focus-within {
    border-color: #000;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.newsletter-form input {
    border: none;
    padding: 15px 30px;
    flex-grow: 1;
    border-radius: 100px;
    font-size: 1rem;
    outline: none;
    background: transparent;
}

.btn-join {
    background-color: #1a1a1a; /* Pitch black for premium contrast */
    color: #fff;
    border-radius: 100px;
    padding: 15px 45px;
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    border: none;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.btn-join:hover {
    background-color: #333;
    letter-spacing: 2px; /* Smooth expansion effect on hover */
}

/* Responsive adjustment */
@media (max-width: 768px) {
    .newsletter-overlay h2 { font-size: 2.2rem; }
    .newsletter-banner { min-height: 400px; }
    .newsletter-form { flex-direction: column; border-radius: 20px; background: transparent; border: none; box-shadow: none; }
    .newsletter-form input { background: #fff; border: 1px solid #eee; margin-bottom: 10px; }
}

</style>

<div class="container py-4">

    <!-- HERO -->
    <div class="hero">
        <div class="hero-content">
            <h1>Upgrade Your Style</h1>
            <p>Premium Fashion Collection 2026</p>
            <a href="all-products.php" class="btn btn-primary mt-3">Shop Now</a>
        </div>
    </div>

    <!-- CATEGORIES -->
    <h4 class="section-title">Shop by Category</h4>
    <div class="row g-3 mb-5">

        <?php
        $cat = mysqli_query($conn, "SELECT * FROM categories LIMIT 4");
        while($c = mysqli_fetch_assoc($cat)) {
        ?>
        <div class="col-md-3">
            <a href="<?= $c['slug'] ?>.php" style="text-decoration:none;color:black;">
                <div class="category-card">
                    <h6><?= $c['name'] ?></h6>
                </div>
            </a>
        </div>
        <?php } ?>

    </div>

    <!-- NEW ARRIVALS -->
    <h4 class="section-title">New Arrivals</h4>

    <div class="row g-4 mb-5">
        <?php
        $new = mysqli_query($conn, "
            SELECT p.*, c.slug FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.is_deleted = 0
            ORDER BY p.created_at DESC LIMIT 8
        ");
        while($p = mysqli_fetch_assoc($new)) {
        ?>
        <div class="col-md-3">
            <div class="product-card">
                <img src="uploads/<?= $p['slug'] ?>/<?= $p['image'] ?>" class="w-100">
                <div class="p-3">
                    <h6><?= $p['name'] ?></h6>
                    <p class="price">BDT <?= $p['price'] ?></p>

                    <a href="product_details.php?id=<?= $p['id'] ?>" class="btn btn-outline-dark btn-sm w-100 mb-2">View</a>
                    

                    <form method="post" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button class="btn btn-primary btn-sm w-100">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

</div>


<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;700&display=swap" rel="stylesheet">

<!-- ELITE NEWSLETTER BANNER -->
<div class="newsletter-banner">
    <div class="newsletter-overlay">
        <div class="newsletter-content">
            <h2>Join the Strendio Inner Circle</h2>
            <p>Exclusive early access & 25% discount on your first piece.</p>
        </div>
        <form action="subscribe.php" method="POST" class="newsletter-form">
            <input type="email" name="email" placeholder="Enter your email address" required>
            <button type="submit" class="btn-join">Join Now</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>