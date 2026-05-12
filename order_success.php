<?php
session_start();
include('config.php');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    include('header.php');
    echo "<div class='container my-5 text-center'><div class='alert alert-danger'>Invalid order reference.</div><a href='index.php' class='btn btn-dark'>Back to Home</a></div>";
    include('footer.php');
    exit();
}

/* FETCH ORDER DETAILS WITH PREPARED STATEMENT */
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    include('header.php');
    echo "<div class='container my-5 text-center'><h3>Order Not Found</h3><p>We couldn't find the details for Order #$order_id.</p></div>";
    include('footer.php');
    exit();
}
?>

<?php include('header.php'); ?>

<!-- GOOGLE FONTS & ANIMATE.CSS -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    body { background-color: #fcfcfc; font-family: 'Inter', sans-serif; }

    .success-container {
        max-width: 600px;
        margin: 60px auto;
        background: #fff;
        padding: 50px;
        border-radius: 30px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.05);
        text-align: center;
        border: 1px solid #f1f1f1;
    }

    /* THE CELEBRATION ICON */
    .check-wrapper {
        width: 100px;
        height: 100px;
        background: #e6f9ed;
        color: #28a745;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 30px;
        font-size: 45px;
    }

    .order-number {
        display: inline-block;
        background: #f8f9fa;
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 700;
        color: #555;
        margin-bottom: 25px;
    }

    /* POST-PURCHASE TIMELINE */
    .steps-container {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid #eee;
        text-align: left;
    }
    .step-item {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .step-icon {
        width: 32px;
        height: 32px;
        background: #000;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .step-text h6 { margin: 0; font-weight: 700; font-size: 14px; }
    .step-text p { margin: 0; font-size: 13px; color: #888; }

    /* ACTION BUTTONS */
    .btn-action {
        border-radius: 50px;
        padding: 14px 30px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-dash { background: #000; color: #fff; border: none; }
    .btn-dash:hover { background: #333; color: #fff; transform: translateY(-2px); }
</style>

<div class="container">
    <div class="success-container animate__animated animate__fadeInUp">
        
        <div class="check-wrapper animate__animated animate__zoomIn animate__delay-1s">
            <i class="bi bi-check-all"></i>
        </div>

        <h2 class="fw-bold mb-2">Thank you, <?= htmlspecialchars($order['full_name'] ?? 'Guest') ?>!</h2>
        <p class="text-muted mb-4">Your order has been received and is being processed.</p>

        <div class="order-number">
            Order Reference: #<?= $order_id ?>
        </div>

        <!-- ORDER MINI-SUMMARY -->
        <div class="p-3 mb-4 text-start" style="background: #fff9f0; border-radius: 15px; border: 1px dashed #ffdca8;">
            <div class="d-flex justify-content-between mb-1">
                <span class="small">Total Paid/Payable:</span>
                <span class="fw-bold">৳ <?= number_format($order['total_amount'], 0) ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="small">Payment Method:</span>
                <span class="fw-bold"><?= htmlspecialchars($order['payment_method'] ?? 'Cash on Delivery') ?></span>
            </div>
        </div>

        <!-- CUSTOMER JOURNEY STEPS -->
        <div class="steps-container">
            <h6 class="mb-4 text-uppercase small fw-bold text-muted" style="letter-spacing: 1px;">What happens next?</h6>
            
            <div class="step-item">
                <div class="step-icon">1</div>
                <div class="step-text">
                    <h6>Confirmation Email</h6>
                    <p>Check your inbox for the receipt and order summary.</p>
                </div>
            </div>

            <div class="step-item">
                <div class="step-icon">2</div>
                <div class="step-text">
                    <h6>Quality Check</h6>
                    <p>Our team is inspecting your items for the best quality.</p>
                </div>
            </div>

            <div class="step-item">
                <div class="step-icon">3</div>
                <div class="step-text">
                    <h6>On its way</h6>
                    <p>You'll get a SMS notification once your parcel is dispatched.</p>
                </div>
            </div>
        </div>

        <!-- CALL TO ACTIONS -->
        <div class="d-grid gap-2 d-md-flex justify-content-center mt-5">
            <a href="track_order.php" class="btn btn-action btn-dash">
                <i class="bi bi-grid-fill me-2"></i> Track Order
            </a>
            <a href="all-products.php" class="btn btn-action btn-outline-dark">
                Continue Shopping
            </a>
        </div>

        <div class="mt-4 pt-4 border-top text-muted" style="font-size: 12px;">
            Need help? Contact us at <strong>support@strendio.com</strong>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>