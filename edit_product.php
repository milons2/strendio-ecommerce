<?php
session_start();
include('config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Product ID not specified.");
}

$product_id = intval($_GET['id']);
$errors = [];
$success = "";

// ✅ FIXED: Fetch product WITH category slug
$stmt = $conn->prepare("
    SELECT p.*, c.slug AS category_slug
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");

$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// ==========================
// HANDLE UPDATE
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $stock = intval($_POST['stock_quantity']);

    if (empty($name) || empty($desc)) {
        $errors[] = "All fields are required.";
    }

    // Get category slug (for upload path)
    $cat_stmt = $conn->prepare("SELECT slug FROM categories WHERE id = ?");
    $cat_stmt->bind_param("i", $category_id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    $cat = $cat_result->fetch_assoc();

    if (!$cat) {
        $errors[] = "Invalid category.";
    }

    $image_name = $product['image'];

    // ==========================
    // IMAGE UPDATE
    // ==========================
    if (!empty($_FILES['image']['name'])) {

        $file_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid image format.";
        }

        if ($file_size > 2 * 1024 * 1024) {
            $errors[] = "Max image size 2MB.";
        }

        $new_name = time() . '_' . rand(1000,9999) . '.' . $ext;

        $target_dir = "uploads/" . ($cat['slug'] ?? 'default') . "/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (empty($errors)) {
            if (move_uploaded_file($tmp_name, $target_dir . $new_name)) {
                $image_name = $new_name;
            } else {
                $errors[] = "Image upload failed.";
            }
        }
    }

    // ==========================
    // UPDATE DB
    // ==========================
    if (empty($errors)) {

        $update = $conn->prepare("
            UPDATE products 
            SET name=?, description=?, price=?, category_id=?, stock_quantity=?, image=?, updated_at=NOW()
            WHERE id=?
        ");

        $update->bind_param(
            "ssdiisi",
            $name,
            $desc,
            $price,
            $category_id,
            $stock,
            $image_name,
            $product_id
        );

        if ($update->execute()) {
            $success = "Product updated successfully!";

            // refresh product data (IMPORTANT FIX)
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

        } else {
            $errors[] = "Update failed.";
        }
    }
}
?>

<?php include('admin_header.php'); ?>

<div class="container py-5" style="max-width:600px;">
    <h2 class="mb-4">✏️ Edit Product</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger"><?= $err ?></div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="name" class="form-control mb-3"
               value="<?= htmlspecialchars($product['name']) ?>" required>

        <textarea name="description" class="form-control mb-3" required><?= htmlspecialchars($product['description']) ?></textarea>

        <input type="number" step="0.01" name="price" class="form-control mb-3"
               value="<?= $product['price'] ?>" required>

        <input type="number" name="stock_quantity" class="form-control mb-3"
               value="<?= $product['stock_quantity'] ?>" required>

        <!-- CATEGORY -->
        <select name="category_id" class="form-control mb-3" required>
            <?php
            $cats = mysqli_query($conn, "SELECT id, name FROM categories");
            while ($c = mysqli_fetch_assoc($cats)) {
                $selected = ($c['id'] == $product['category_id']) ? "selected" : "";
                echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
            }
            ?>
        </select>

        <!-- CURRENT IMAGE (FIXED) -->
        <div class="mb-3">
            <label>Current Image:</label><br>

            <?php
            $imgPath = "uploads/" . ($product['category_slug'] ?? 'default') . "/" . $product['image'];
            ?>

            <?php if (!empty($product['image'])): ?>
                <img src="<?= $imgPath ?>" width="120" style="border-radius:8px;">
            <?php else: ?>
                <p class="text-muted">No image</p>
            <?php endif; ?>
        </div>

        <!-- NEW IMAGE -->
        <input type="file" name="image" class="form-control mb-4">

        <button type="submit" class="btn btn-primary w-100">Update Product</button>
    </form>
</div>

<?php include('admin_footer.php'); ?>