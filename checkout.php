<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* CART ITEMS WITH IMAGE PATH LOGIC */
$query = "SELECT ci.id, ci.quantity, p.name, p.price, p.image, c.slug AS category_slug 
          FROM cart_items ci
          JOIN products p ON ci.product_id = p.id
          JOIN categories c ON p.category_id = c.id
          WHERE ci.user_id = $user_id";

$result = mysqli_query($conn, $query);

$cart_items = [];
$subtotal = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $row['total_price'] = $row['price'] * $row['quantity'];
    $subtotal += $row['total_price'];
    $cart_items[] = $row;
}

$vat = $subtotal * 0.05;
$shipping = ($subtotal > 0) ? 60 : 0; // BD Zone Shipping
$grand_total = $subtotal + $vat + $shipping;
?>

<?php include('header.php'); ?>

<!-- GOOGLE FONTS & STYLES -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    .checkout-title { font-weight: 700; letter-spacing: -0.5px; margin-bottom: 30px; }
    
    /* FORM STYLING */
    .checkout-card {
        background: #fff;
        border-radius: 20px;
        padding: 35px;
        border: 1px solid #eee;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .form-floating > .form-control:focus { border-color: #000; box-shadow: none; }
    
    /* ORDER SUMMARY SIDEBAR */
    .order-summary-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #eee;
        position: sticky;
        top: 100px;
    }
    .checkout-item-img {
        width: 50px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 15px;
    }
    
    /* PAYMENT SELECTOR */
    .payment-option {
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 15px;
        cursor: pointer;
        transition: 0.3s;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .payment-option:hover { background: #fdfdfd; border-color: #000; }
    .payment-option input[type="radio"] { margin-right: 15px; }

    .total-row {
        font-size: 22px;
        font-weight: 700;
        border-top: 2px dashed #eee;
        margin-top: 15px;
        padding-top: 15px;
    }
    
    .btn-place-order {
        background: #1a1a1a;
        color: #fff;
        border: none;
        padding: 16px;
        border-radius: 50px;
        font-weight: 600;
        width: 100%;
        margin-top: 20px;
        transition: 0.3s;
    }
    .btn-place-order:hover { background: #000; transform: translateY(-2px); }
</style>

<div class="container py-5">
    <h2 class="checkout-title text-center">Secure Checkout</h2>

    <?php if (!empty($cart_items)): ?>
    <form action="place_order.php" method="POST">
        <div class="row g-5">

            <!-- LEFT: SHIPPING & PAYMENT -->
            <div class="col-lg-7">
                <div class="checkout-card mb-4">
                    <h5 class="mb-4 fw-bold"><i class="bi bi-truck me-2"></i> Shipping Information</h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-floating">
                                <input type="text" name="full_name" class="form-control" id="fName" placeholder="Full Name" required>
                                <label for="fName">Recipient's Full Name</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating">
                                <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone" required>
                                <label for="phone">Phone Number (For Delivery)</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating">
                                <textarea name="shipping_address" class="form-control" id="address" style="height: 120px" placeholder="Address" required></textarea>
                                <label for="address">Complete Shipping Address</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="checkout-card">
                    <h5 class="mb-4 fw-bold"><i class="bi bi-credit-card me-2"></i> Payment Method</h5>
                    
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="Cash on Delivery" id="cod" checked>
                        <label for="cod" class="mb-0 flex-grow-1">
                            <strong>Cash on Delivery</strong>
                            <div class="small text-muted">Pay when you receive the parcel</div>
                        </label>
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>

                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="SSLCommerz" id="ssl">
                        <label for="ssl" class="mb-0 flex-grow-1">
                            <strong>Digital Payment</strong>
                            <div class="small text-muted">bKash, Nagad, or Credit/Debit Cards</div>
                        </label>
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                </div>
            </div>

            <!-- RIGHT: ORDER SUMMARY -->
            <div class="col-lg-5">
                <div class="order-summary-card">
                    <h5 class="mb-4 fw-bold">Order Summary</h5>
                    
                    <div class="mb-4" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($cart_items as $item): 
                            $img = "uploads/" . $item['category_slug'] . "/" . $item['image'];
                            if (empty($item['image']) || !file_exists($img)) $img = "uploads/default.png";
                        ?>
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= $img ?>" class="checkout-item-img" alt="">
                            <div class="flex-grow-1">
                                <div class="small fw-bold"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="small text-muted">Qty: <?= $item['quantity'] ?></div>
                            </div>
                            <div class="text-end small fw-bold">
                                ৳<?= number_format($item['total_price'], 0) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>৳<?= number_format($subtotal, 0) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">VAT (5%)</span>
                        <span>৳<?= number_format($vat, 0) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Shipping</span>
                        <span>৳<?= number_format($shipping, 0) ?></span>
                    </div>

                    <div class="d-flex justify-content-between total-row">
                        <span>Grand Total</span>
                        <span>৳<?= number_format($grand_total, 0) ?></span>
                    </div>

                    <input type="hidden" name="total_amount" value="<?= $grand_total ?>">

                    <button type="submit" class="btn btn-place-order">
                        Complete Order <i class="bi bi-arrow-right ms-2"></i>
                    </button>

                    <div class="text-center mt-4">
                        <p style="font-size: 11px; color: #aaa;">By clicking place order you agree to our Terms & Conditions</p>
                        <img src="https://securepay.sslcommerz.com/gw/asset/img/lib/all-card.png" class="img-fluid opacity-50" style="max-height: 20px;" alt="">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php else: ?>
        <div class="text-center py-5">
            <h3>No items to checkout.</h3>
            <a href="all-products.php" class="btn btn-dark mt-3">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>