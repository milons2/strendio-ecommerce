<?php
include('config.php');
include('header.php');

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<div class="container py-5">
    <h3>Search Results for: <?= htmlspecialchars($search_query) ?></h3>

    <?php
    if ($search_query !== '') {
        $stmt = $conn->prepare("SELECT p.*, c.slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.name LIKE CONCAT('%', ?, '%')");
        $stmt->bind_param("s", $search_query);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <img src="uploads/<?= htmlspecialchars($row['slug']) ?>/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>" style="height: 250px; object-fit: cover;">
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
            <p>No products found matching your search.</p>
        <?php endif;
        $stmt->close();
    } else {
        echo "<p>Please enter a search query.</p>";
    }
    ?>
</div>

<?php include('footer.php'); ?>