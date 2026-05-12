<?php
require_once 'auth.php';
require_once 'config.php';
require_once 'cart_functions.php';
include('header.php');

$full_name = $_SESSION['full_name'] ?? "User";
$user_id = $_SESSION['user_id'] ?? "";
$cartItemCount = getCartItemCount($user_id, $conn);

// Fetch categories
$category_query = "SELECT id, name FROM categories";
$categories = mysqli_query($conn, $category_query);

// Filter
$filter = $_GET['category'] ?? null;

$product_query = "
    SELECT p.*, c.slug AS category_slug 
    FROM products p 
    JOIN categories c ON p.category_id = c.id
    WHERE p.is_deleted = 0
";

if ($filter) {
    $product_query .= " AND c.id = " . intval($filter);
}

$product_query .= " ORDER BY p.created_at DESC LIMIT 20";
$result = mysqli_query($conn, $product_query);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    :root {
        --primary: #0d6efd;
        --glass-bg: rgba(255, 255, 255, 0.8);
        --glass-border: rgba(255, 255, 255, 0.3);
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Inter', sans-serif;
    }

    .welcome-header {
        padding: 40px 20px;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: white;
        border-radius: 0 0 30px 30px;
        margin-bottom: 40px;
        text-align: center;
    }

    /* SLIDER SECTION */
    .section-title {
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .slider-wrapper {
        overflow: hidden;
        position: relative;
        padding: 20px 0;
    }

    .product-grid-slider {
        display: flex;
        gap: 25px;
        width: max-content;
        animation: scrollSlider 50s linear infinite;
    }

    /* MODERN PRODUCT CARD */
    .product-card {
        flex: 0 0 auto;
        width: 240px;
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 15px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        background: #fff;
    }

    .card-img-wrapper {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        height: 200px;
        margin-bottom: 12px;
    }

    .product-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.5s;
    }

    .product-card:hover img {
        transform: scale(1.1);
    }

    .wishlist-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: white;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: #ef4444;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 2;
    }

    .product-info h6 {
        font-weight: 600;
        margin-bottom: 4px;
        color: #1e293b;
    }

    .price-tag {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.1rem;
    }

    /* FILTER STYLES */
    .filter-container {
        display: flex;
        justify-content: center;
        margin-top: -30px;
        margin-bottom: 40px;
    }

    .filter-bar {
        background: white;
        padding: 10px 20px;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .filter-bar select {
        border: none;
        font-weight: 500;
        outline: none;
        background: transparent;
        cursor: pointer;
    }

    /* BANNER */
    .banner-card {
        border-radius: 24px;
        overflow: hidden;
        margin: 40px 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .banner-card img {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }

    @keyframes scrollSlider {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-265px * <?= count($products) ?>)); }
    }

    .product-grid-slider:hover { animation-play-state: paused; }

    @media (max-width: 768px) {
        .banner-card img { height: 200px; }
        .main-container { padding: 15px; }
    }
</style>

<div class="welcome-header">
    <h2 class="fw-bold">Exclusive Styles for <?= htmlspecialchars($full_name) ?></h2>
    <p class="opacity-75">Curated trends just for your wardrobe.</p>
</div>

<div class="container main-container">
    
    <!-- FILTER -->
    <div class="filter-container">
        <div class="filter-bar">
            <form method="GET">
                <i class="bi bi-filter-left me-2"></i>
                <select name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php 
                    mysqli_data_seek($categories, 0);
                    while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?= $cat['id'] ?>" <?= $filter == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
    </div>

    <h4 class="section-title"><i class="bi bi-fire text-danger"></i> Trending Now</h4>

    <!-- SLIDER -->
    <div class="slider-wrapper">
        <div class="product-grid-slider">

            <?php foreach (array_merge($products, $products) as $row): // Duplicate for loop ?>
                <?php
                    $image = $row['image'] ?? '';
                    $slug = $row['category_slug'] ?? '';
                    $imgPath = (!empty($image) && !empty($slug))
                        ? "uploads/" . $slug . "/" . $image
                        : "uploads/default.png";
                ?>
                <div class="product-card">
                    <button class="wishlist-btn"><i class="bi bi-heart"></i></button>
                    <a href="product_details.php?id=<?= $row['id'] ?>" class="text-decoration-none">
                        <div class="card-img-wrapper">
                            <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        </div>
                        <div class="product-info">
                            <h6 class="text-truncate"><?= htmlspecialchars($row['name']) ?></h6>
                            <p class="price-tag mb-3">৳<?= number_format($row['price'], 0) ?></p>
                        </div>
                    </a>
                    <button class="btn btn-primary w-100 rounded-pill btn-sm fw-bold" 
                            onclick="addToCart(<?= $row['id'] ?>)">
                        <i class="bi bi-bag-plus me-1"></i> Add to Cart
                    </button>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- BANNER -->
    <div class="banner-card">
        <img src="uploads/banners/strendio-app.png" alt="Seasonal Promotion">
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function addToCart(id) {
    const formData = new URLSearchParams();
    formData.append('product_id', id);
    formData.append('quantity', 1);

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: formData
    })
    .then(response => {
        // Trigger a professional toast notification
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: 'success',
            title: 'Added to your bag'
        });
    });
}
</script>

<?php include('footer.php'); ?>