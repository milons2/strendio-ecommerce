<?php
include('config.php');
include('header.php');

// Fetch category ID for 'accessories'
$catQuery = "SELECT id FROM categories WHERE slug = 'accessories' LIMIT 1";
$catResult = mysqli_query($conn, $catQuery);
$category = mysqli_fetch_assoc($catResult);
$category_id = $category['id'] ?? 0;

// Fetch products in accessories category
$query = "SELECT * FROM products WHERE category_id = $category_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Check login
$isLoggedIn = isset($_SESSION['user_id']);
?>
<style>
.card-img-top {
    height: 300px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
}

.card {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
}

.card-body h5 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    min-height: 50px;
}

.card-text {
    font-weight: bold;
    color: #333;
}

.btn {
    font-size: 0.9rem;
    font-weight: 500;
}
</style>

<div class="container py-5">
    <h2 class="mb-4">Accessories Collection</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-3">
                    <div class="card h-100">
                        <img src="uploads/accessories/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                            <p class="card-text">BDT <?= number_format($row['price'], 2) ?></p>
                            <a href="product_details.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary mb-2">View</a>
                            <form method="post" action="add_to_cart.php" class="mt-auto">
                                <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-success w-100">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-center">No accessories products available.</p>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>