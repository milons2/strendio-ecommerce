<?php
session_start();
include('config.php');

// Strict error reporting for debugging during development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $stock_quantity = intval($_POST['stock_quantity']);

    // ✅ Validation
    if (empty($name) || empty($description) || $price <= 0 || $category_id <= 0) {
        $errors[] = "Please fill all required fields correctly.";
    }

    // ✅ Get category slug for dynamic folder organization
    $stmt = mysqli_prepare($conn, "SELECT slug FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cat = mysqli_fetch_assoc($result);

    if (!$cat) {
        $errors[] = "Invalid category selected.";
    }

    $cat_slug = $cat['slug'] ?? 'default';

    // ✅ Image Processing
    if (!empty($_FILES['image']['name'])) {
        $file_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($file_ext, $allowed_types)) {
            $errors[] = "File type not supported. Use JPG, PNG, or WEBP.";
        }

        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image size exceeds 2MB limit.";
        }

        // Unique name to prevent overwriting
        $new_image_name = time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $target_dir = "uploads/$cat_slug/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $new_image_name;
    } else {
        $errors[] = "A product image is required.";
    }

    // ✅ Database Operation
    if (empty($errors)) {
        if (move_uploaded_file($tmp_name, $target_file)) {
            $sql = "INSERT INTO products (name, description, price, image, category_id, stock_quantity, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = mysqli_prepare($conn, $sql);
            
            // "ssdssi" = string, string, double, string, string (or int), int
            mysqli_stmt_bind_param($stmt, "ssdssi", $name, $description, $price, $new_image_name, $category_id, $stock_quantity);

            if (mysqli_stmt_execute($stmt)) {
                $success = true;
            } else {
                $errors[] = "Failed to save product to database.";
            }
        } else {
            $errors[] = "File system error: Could not move uploaded file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product | STRENDio Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .sidebar { width: 260px; height: 100vh; background: #0f172a; position: fixed; left: 0; top: 0; padding: 20px; color: #fff; z-index: 100; }
        .main-content { margin-left: 260px; padding: 40px; }
        .nav-link { color: #94a3b8; padding: 12px 15px; border-radius: 10px; display: flex; align-items: center; gap: 12px; text-decoration: none; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: #fff; }
        
        .form-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 30px; }
        .form-label { font-weight: 600; font-size: 14px; color: #475569; }
        .form-control, .form-select { padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
        
        #imagePreview { width: 100%; height: 250px; border-radius: 12px; border: 2px dashed #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8fafc; margin-top: 10px; }
        #imagePreview img { width: 100%; height: 100%; object-fit: contain; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="text-center mb-4">
        <img src="strendio_logo2.png" style="height: 35px; filter: brightness(0) invert(1);">
    </div>
    <div class="nav flex-column">
        <a href="admin_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="manage_products.php" class="nav-link active"><i class="bi bi-box-seam"></i> Products</a>
        <a href="view_orders.php" class="nav-link"><i class="bi bi-cart"></i> Orders</a>
        <a href="admin_profile.php" class="nav-link"><i class="bi bi-person"></i> Settings</a>
        <hr style="opacity:0.1">
        <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="mb-4">
        <a href="manage_products.php" class="text-decoration-none text-muted small fw-bold">
            <i class="bi bi-arrow-left me-1"></i> BACK TO INVENTORY
        </a>
        <h3 class="fw-bold mt-2">Create New Product</h3>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> Product listed successfully! 
            <a href="manage_products.php" class="alert-link">View in Inventory</a>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?> <li><?= $error ?></li> <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="row g-4">
            <!-- Left Column: Details -->
            <div class="col-lg-8">
                <div class="form-card mb-4">
                    <h5 class="fw-bold mb-4">General Information</h5>
                    <div class="mb-4">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Premium Cotton Oversized Tee" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="8" class="form-control" placeholder="Write compelling product details..." required></textarea>
                    </div>
                </div>

                <div class="form-card">
                    <h5 class="fw-bold mb-4">Stock & Pricing</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Price (BDT)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">৳</span>
                                <input type="number" name="price" step="0.01" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quantity in Stock</label>
                            <input type="number" name="stock_quantity" class="form-control" placeholder="e.g. 50" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Media & Category -->
            <div class="col-lg-4">
                <div class="form-card mb-4">
                    <h5 class="fw-bold mb-4">Organization</h5>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Choose category...</option>
                            <?php
                            $cat_res = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");
                            while ($cat = mysqli_fetch_assoc($cat_res)) {
                                echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-card mb-4 text-center">
                    <h5 class="fw-bold mb-4 text-start">Product Media</h5>
                    <input type="file" name="image" id="productImage" class="form-control mb-2" accept="image/*" required>
                    <p class="text-muted" style="font-size: 11px;">Recommended: Square image, max 2MB.</p>
                    
                    <div id="imagePreview">
                        <span class="text-muted small">Preview will appear here</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="border-radius:12px;">
                    <i class="bi bi-cloud-arrow-up me-2"></i> PUBLISH PRODUCT
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Image Preview Logic
document.getElementById('productImage').addEventListener('change', function(event) {
    const reader = new FileReader();
    const preview = document.getElementById('imagePreview');
    
    reader.onload = function() {
        preview.innerHTML = `<img src="${reader.result}" alt="Preview">`;
    }
    
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
});

// Bootstrap Validation Logic
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

</body>
</html>