<?php
session_start();
include('config.php');

if (!isset($_GET['id'])) {
    header("Location: all-products.php");
    exit();
}

$product_id = intval($_GET['id']);

/* FETCH PRODUCT WITH CATEGORY SLUG */
$stmt = $conn->prepare("
    SELECT p.*, c.slug, c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<div class='container py-5 text-center'><h2>Product not found</h2><a href='all-products.php' class='btn btn-primary mt-3'>Return to Shop</a></div>";
    exit();
}

/* FETCH RELATED PRODUCTS */
$cat_id = $product['category_id'];
$stmt2 = $conn->prepare("
    SELECT p.*, c.slug
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = ? AND p.id != ?
    LIMIT 4
");
$stmt2->bind_param("ii", $cat_id, $product_id);
$stmt2->execute();
$related_result = $stmt2->get_result();

include('header.php');

/* IMAGE PATH LOGIC */
$imagePath = "uploads/" . $product['slug'] . "/" . $product['image'];
$defaultImage = "uploads/default.png";
if (empty($product['image']) || !file_exists($imagePath)) {
    $imagePath = $defaultImage;
}
?>

<!-- Google Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    body { font-family: 'Outfit', sans-serif; background-color: #fcfcfc; }
    
    /* Breadcrumb */
    .breadcrumb-item a { color: #6c757d; text-decoration: none; font-size: 14px; }
    .breadcrumb-item.active { color: #000; font-weight: 600; font-size: 14px; }

    /* Product Section */
    .product-main-card { background: #fff; border-radius: 24px; overflow: hidden; border: 1px solid #f0f0f0; }
    
    .image-container { background: #f8f9fa; padding: 30px; display: flex; align-items: center; justify-content: center; height: 100%; min-height: 500px; }
    .main-product-img { max-width: 100%; max-height: 500px; object-fit: contain; transition: transform 0.5s ease; cursor: zoom-in; }
    .main-product-img:hover { transform: scale(1.05); }

    .product-info-col { padding: 40px; }
    .cat-label { text-transform: uppercase; letter-spacing: 2px; font-weight: 700; color: #0d6efd; font-size: 12px; margin-bottom: 10px; display: block; }
    .product-title { font-size: 36px; font-weight: 700; color: #1a1a1a; margin-bottom: 15px; }
    
    .price-wrapper { margin: 25px 0; }
    .current-price { font-size: 32px; font-weight: 700; color: #000; }
    .currency { font-size: 18px; vertical-align: top; margin-right: 2px; }

    /* Quantity Switcher */
    .qty-container { display: flex; align-items: center; background: #f1f1f1; border-radius: 12px; width: fit-content; padding: 5px; }
    .qty-btn { border: none; background: transparent; width: 40px; height: 40px; font-size: 20px; font-weight: bold; cursor: pointer; color: #444; }
    .qty-input { border: none; background: transparent; width: 50px; text-align: center; font-weight: 600; font-size: 16px; outline: none !important; }

    /* Buttons */
    .btn-add-cart { background: #000; color: #fff; border: none; padding: 18px 40px; border-radius: 14px; font-weight: 600; flex-grow: 1; transition: 0.3s; }
    .btn-add-cart:hover { background: #333; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .btn-wish { background: #fff; border: 1px solid #e0e0e0; width: 58px; border-radius: 14px; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
    .btn-wish:hover { border-color: #ef4444; color: #ef4444; }

    /* Trust Badges */
    .trust-badge-row { display: flex; gap: 20px; margin-top: 40px; padding-top: 30px; border-top: 1px solid #eee; }
    .trust-item { text-align: center; font-size: 11px; font-weight: 600; color: #777; }
    .trust-item i { font-size: 24px; color: #333; display: block; margin-bottom: 5px; }

    /* Related Cards */
    .related-card { border: none; border-radius: 18px; transition: 0.3s; background: #fff; }
    .related-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
    .related-img { border-radius: 15px; height: 200px; object-fit: cover; }
</style>

<div class="container py-4">
    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item"><a href="all-products.php?category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
      </ol>
    </nav>

    <div class="product-main-card shadow-sm">
        <div class="row g-0">
            <!-- Left: Image Gallery -->
            <div class="col-lg-6">
                <div class="image-container">
                    <img src="<?= htmlspecialchars($imagePath) ?>" class="main-product-img" id="zoomImg" alt="Product Image">
                </div>
            </div>

            <!-- Right: Details -->
            <div class="col-lg-6">
                <div class="product-info-col">
                    <span class="cat-label"><?= htmlspecialchars($product['category_name']) ?></span>
                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="text-warning">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                        </div>
                        <span class="text-muted small fw-bold">(120+ Reviews)</span>
                        <span class="ms-auto badge rounded-pill <?= $product['stock_quantity'] > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                             <?= $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                        </span>
                    </div>

                    <p class="text-muted mb-4" style="line-height: 1.7;">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </p>

                    <div class="price-wrapper">
                        <span class="current-price"><span class="currency">BDT</span><?= number_format($product['price'], 0) ?></span>
                    </div>

                    <!-- ACTION FORM -->
                    <form method="post" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        
                        <div class="d-flex gap-3 mb-4">
                            <!-- Qty Control -->
                            <div class="qty-container">
                                <button type="button" class="qty-btn" onclick="changeQty(-1)">-</button>
                                <input type="number" name="quantity" id="qty" value="1" min="1" max="<?= $product['stock_quantity'] ?>" class="qty-input" readonly>
                                <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                            </div>

                            <button type="submit" class="btn-add-cart d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-bag-plus-fill"></i> ADD TO CART
                            </button>

                            <button type="button" class="btn-wish">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Trust Section -->
                    <div class="trust-badge-row">
                        <div class="trust-item">
                            <i class="bi bi-truck"></i>
                            FAST DELIVERY
                        </div>
                        <div class="trust-item">
                            <i class="bi bi-shield-check"></i>
                            GENUINE BRAND
                        </div>
                        <div class="trust-item">
                            <i class="bi bi-arrow-left-right"></i>
                            7 DAY RETURN
                        </div>
                        <div class="trust-item">
                            <i class="bi bi-lock"></i>
                            SECURE PAY
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RELATED PRODUCTS -->
    <?php if ($related_result->num_rows > 0): ?>
    <div class="mt-5 pt-4">
        <h4 class="fw-bold mb-4">You May Also Like</h4>
        <div class="row g-4">
            <?php while ($related = $related_result->fetch_assoc()): 
                $relImg = "uploads/" . $related['slug'] . "/" . $related['image'];
                if (empty($related['image']) || !file_exists($relImg)) $relImg = $defaultImage;
            ?>
            <div class="col-md-3 col-6">
                <a href="product_details.php?id=<?= $related['id'] ?>" class="text-decoration-none">
                    <div class="card h-100 related-card p-3 shadow-sm">
                        <img src="<?= htmlspecialchars($relImg) ?>" class="related-img mb-3">
                        <h6 class="text-dark fw-bold text-truncate mb-1"><?= htmlspecialchars($related['name']) ?></h6>
                        <span class="text-primary fw-bold small">BDT <?= number_format($related['price'], 0) ?></span>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
    function changeQty(amt) {
        const input = document.getElementById('qty');
        let val = parseInt(input.value) + amt;
        if (val < 1) val = 1;
        if (val > <?= $product['stock_quantity'] ?>) val = <?= $product['stock_quantity'] ?>;
        input.value = val;
    }
</script>

<?php include('footer.php'); ?>