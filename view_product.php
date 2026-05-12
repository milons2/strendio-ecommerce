<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.is_deleted = 0
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<h3 style='text-align:center;margin-top:50px;'>Product not found</h3>";
    exit();
}

$product = $result->fetch_assoc();

/* IMAGE */
$image_path = "uploads/" . $product['category_slug'] . "/" . $product['image'];
$default_image = "uploads/default.png";

if (empty($product['image']) || !file_exists($image_path)) {
    $image_path = $default_image;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Product - STRENDio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .product-wrapper {
            max-width: 1100px;
            margin: 40px auto;
        }

        .product-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .product-img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            background: #eee;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            color: #222;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }

        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            margin-right: 6px;
        }

        .category {
            background: #eef4ff;
            color: #0d6efd;
        }

        .stock {
            background: #e9fbe9;
            color: #28a745;
        }

        .desc {
            margin-top: 15px;
            color: #555;
            line-height: 1.6;
        }

        .btn-group-custom {
            margin-top: 25px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .product-img {
                height: 300px;
            }
        }
    </style>
</head>

<body>

<div class="container product-wrapper">

    <div class="product-card">

        <div class="row g-0">

            <!-- IMAGE -->
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($image_path) ?>" class="product-img" alt="Product">
            </div>

            <!-- DETAILS -->
            <div class="col-md-6 p-4">

                <div class="title">
                    <?= htmlspecialchars($product['name']) ?>
                </div>

                <div class="price my-2">
                    BDT <?= number_format($product['price'], 2) ?>
                </div>

                <div class="mb-2">
                    <span class="badge-custom category">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </span>

                    <span class="badge-custom stock">
                        Stock: <?= (int)$product['stock_quantity'] ?>
                    </span>
                </div>

                <div class="desc">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </div>

                <!-- ACTION -->
                <div class="btn-group-custom">

                    <a href="manage_products.php" class="btn btn-secondary">
                        ← Back
                    </a>

                    <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-primary">
                        Edit
                    </a>

                    <a href="delete_product.php?id=<?= $product['id'] ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure?')">
                        Delete
                    </a>

                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>