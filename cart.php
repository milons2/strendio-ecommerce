<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* CART QUERY */
$query = "SELECT ci.id, ci.quantity, p.name, p.price, p.image, c.slug AS category_slug 
          FROM cart_items ci
          JOIN products p ON ci.product_id = p.id
          JOIN categories c ON p.category_id = c.id
          WHERE ci.user_id = $user_id";

$result = mysqli_query($conn, $query);

$cart_items = [];
$subtotal = 0;

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['total_price'] = $row['price'] * $row['quantity'];
        $subtotal += $row['total_price'];
        $cart_items[] = $row;
    }
}

$vat_rate = 0.05;
$vat = $subtotal * $vat_rate;
$shipping = ($subtotal > 0) ? 60 : 0; // Flat 60 BDT shipping for BD Zone
$grand_total = $subtotal + $vat + $shipping;
?>

<?php include('header.php'); ?>

<!-- GOOGLE FONTS & ICONS -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; color: #333; }
    
    .cart-header { padding: 40px 0 20px; }
    .cart-header h2 { font-weight: 700; letter-spacing: -0.5px; }

    /* ITEM CARD */
    .cart-item-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 15px;
        border: 1px solid #eee;
        transition: 0.3s;
    }
    .cart-item-card:hover { border-color: #ddd; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

    .product-img-cart {
        width: 100px;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        background: #f1f1f1;
    }

    .qty-input {
        max-width: 80px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #ddd;
    }

    /* SUMMARY BOX */
    .summary-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #eee;
        position: sticky;
        top: 100px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 15px;
        color: #666;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px dashed #eee;
        font-size: 20px;
        font-weight: 700;
        color: #000;
    }

    .btn-checkout {
        background: #1a1a1a;
        color: #fff;
        border: none;
        padding: 15px;
        border-radius: 50px;
        font-weight: 600;
        width: 100%;
        transition: 0.3s;
    }
    .btn-checkout:hover { background: #000; transform: translateY(-2px); color: #fff;}

    .remove-link {
        color: #ff4757;
        font-size: 13px;
        text-decoration: none;
        font-weight: 600;
    }
    .remove-link:hover { text-decoration: underline; }
</style>

<div class="container mb-5">
    <div class="cart-header">
        <h2>Shopping Bag <span class="text-muted" style="font-size: 18px;">(<?= count($cart_items) ?> items)</span></h2>
    </div>

    <?php if (!empty($cart_items)): ?>
    <div class="row g-4">
        
        <!-- LEFT: ITEMS LIST -->
        <div class="col-lg-8">
            <form action="update_cart.php" method="POST" id="cart-form">
                <?php foreach ($cart_items as $item): 
                    $imagePath = "uploads/" . $item['category_slug'] . "/" . $item['image'];
                    if (!file_exists($imagePath) || empty($item['image'])) {
                        $imagePath = "uploads/default.png";
                    }
                ?>
                <div class="cart-item-card d-flex align-items-center">
                    <img src="<?= htmlspecialchars($imagePath) ?>" class="product-img-cart" alt="">
                    
                    <div class="ms-4 flex-grow-1">
                        <h5 class="mb-1" style="font-weight: 600;"><?= htmlspecialchars($item['name']) ?></h5>
                        <p class="text-muted small mb-2">Category: <?= ucfirst($item['category_slug']) ?></p>
                        <a href="remove_from_cart.php?id=<?= $item['id'] ?>" class="remove-link" onclick="return confirm('Remove item?')">
                            <i class="bi bi-trash3 me-1"></i> Remove
                        </a>
                    </div>

                    <div class="text-end me-4">
                        <p class="small text-muted mb-1">Quantity</p>
                        <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control qty-input">
                    </div>

                    <div class="text-end" style="min-width: 120px;">
                        <p class="small text-muted mb-1">Total</p>
                        <span style="font-weight: 700;">৳<?= number_format($item['total_price'], 0) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="d-flex justify-content-between mt-4">
                    <a href="all-products.php" class="btn btn-outline-dark btn-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i> Continue Shopping
                    </a>
                    <button type="submit" class="btn btn-outline-primary btn-pill px-4">
                        <i class="bi bi-arrow-clockwise me-2"></i> Update Bag
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT: SUMMARY -->
        <div class="col-lg-4">
            <div class="summary-card">
                <h4 class="mb-4" style="font-weight: 700;">Order Summary</h4>
                
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>৳<?= number_format($subtotal, 0) ?></span>
                </div>
                <div class="summary-item">
                    <span>Estimated Tax (VAT 5%)</span>
                    <span>৳<?= number_format($vat, 0) ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping Fee</span>
                    <span>৳<?= number_format($shipping, 0) ?></span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>৳<?= number_format($grand_total, 0) ?></span>
                </div>

                <p class="text-muted xsmall mt-3 mb-4" style="font-size: 12px;">
                    <i class="bi bi-shield-check me-1"></i> Secure Checkout Guaranteed
                </p>

                <a href="checkout.php" class="btn btn-checkout">
                    Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
                </a>

                <!-- PAYMENT PREVIEW SECTION -->
                <div class="mt-4 pt-3 border-top">
                    <p class="text-center small text-muted mb-3" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase;">
                        Secure Payment Partners
                    </p>
                    <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                        <!-- We use a more stable combined image link or individual logos -->
                        <img src="https://admin.getbee.com/public/payment-methods-icons.png" 
                            class="img-fluid" 
                            style="max-height: 25px; filter: grayscale(100%); opacity: 0.8;" 
                            alt="Visa, Mastercard, Amex"
                            onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/2560px-Visa_Inc._logo.svg.png'; this.style.maxHeight='15px';">
                        
                        <!-- Mobile Banking Icons -->
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 10px;">bKash</span>
                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 10px;">Nagad</span>
                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 10px;">Rocket</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php else: ?>
    <!-- EMPTY CART STATE -->
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="bi bi-bag-x text-muted" style="font-size: 5rem;"></i>
        </div>
        <h3>Your bag is empty</h3>
        <p class="text-muted">Looks like you haven't added anything to your bag yet.</p>
        <a href="all-products.php" class="btn btn-dark btn-pill px-5 py-3 mt-3">Start Shopping</a>
    </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>