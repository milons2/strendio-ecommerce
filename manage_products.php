<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include('config.php');

// ==========================
// FILTER + SEARCH + PAGINATION
// ==========================
$filter = $_GET['filter'] ?? 'active';
$search = $_GET['search'] ?? '';
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

$where_clauses = ["1=1"];
$params = [];
$types = "";

// Filter logic
if ($filter === 'active') {
    $where_clauses[] = "p.is_deleted = 0";
} elseif ($filter === 'deleted') {
    $where_clauses[] = "p.is_deleted = 1";
}

// Search logic (Prepared Statement ready)
if (!empty($search)) {
    $where_clauses[] = "p.name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

// ==========================
// TOTAL COUNT
// ==========================
$countQuery = "SELECT COUNT(*) as total FROM products p JOIN categories c ON p.category_id = c.id WHERE $where_sql";
$stmt_count = $conn->prepare($countQuery);
if (!empty($params)) { $stmt_count->bind_param($types, ...$params); }
$stmt_count->execute();
$totalRows = $stmt_count->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// ==========================
// MAIN QUERY
// ==========================
$query = "SELECT p.*, c.name AS category_name, c.slug 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE $where_sql
          ORDER BY p.id DESC
          LIMIT ? OFFSET ?";

$stmt_main = $conn->prepare($query);
$main_params = array_merge($params, [$limit, $offset]);
$main_types = $types . "ii";
$stmt_main->bind_param($main_types, ...$main_params);
$stmt_main->execute();
$result = $stmt_main->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products | STRENDio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        
        /* REUSING THE DASHBOARD SIDEBAR LOGIC */
        .sidebar { width: 260px; height: 100vh; background: #0f172a; position: fixed; left: 0; top: 0; padding: 20px; color: #fff; }
        .main-content { margin-left: 260px; padding: 40px; }
        
        .nav-link { color: #94a3b8; padding: 12px 15px; border-radius: 10px; display: flex; align-items: center; gap: 12px; text-decoration: none; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: #fff; }

        /* PRODUCT TABLE STYLES */
        .data-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .table { margin-bottom: 0; }
        .table thead { background: #f1f5f9; }
        .table thead th { font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px; border: none; }
        .table tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        .product-img { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: #f8fafc; }
        
        /* STATUS BADGES */
        .badge-pill { padding: 5px 12px; border-radius: 50px; font-weight: 600; font-size: 11px; }
        .bg-success-subtle { background: #dcfce7 !important; color: #15803d !important; }
        .bg-warning-subtle { background: #fef9c3 !important; color: #854d0e !important; }
        .bg-danger-subtle { background: #fee2e2 !important; color: #b91c1c !important; }

        .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: 0.2s; }
        
        /* SEARCH BAR */
        .search-wrapper { position: relative; max-width: 400px; }
        .search-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .search-wrapper .form-control { padding-left: 40px; border-radius: 10px; border: 1px solid #e2e8f0; }

        @media (max-width: 992px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-4">
        <img src="strendio_logo2.png" style="height: 35px; filter: brightness(0) invert(1);">
    </div>
    <div class="nav flex-column">
        <a href="admin_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="manage_products.php" class="nav-link active"><i class="bi bi-box-seam"></i> Products</a>
        <a href="view_orders.php" class="nav-link"><i class="bi bi-cart"></i> Orders</a>
        <a href="admin_profile.php" class="nav-link"><i class="bi bi-person"></i> Settings</a>
        <a href="logout.php" class="nav-link text-danger mt-5"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Inventory Management</h3>
            <p class="text-muted small">Viewing <?= $totalRows ?> total products</p>
        </div>
        <a href="add_product.php" class="btn btn-primary px-4 shadow-sm" style="border-radius: 10px;">
            <i class="bi bi-plus-lg me-2"></i> Add Product
        </a>
    </div>

    <!-- FILTERS & SEARCH -->
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-md-6">
            <div class="btn-group p-1 background: #fff; border-radius: 12px; background: #e2e8f0;">
                <a href="?filter=active" class="btn btn-sm <?= $filter == 'active' ? 'btn-white shadow-sm bg-white' : '' ?>" style="border-radius: 8px;">Active</a>
                <a href="?filter=deleted" class="btn btn-sm <?= $filter == 'deleted' ? 'btn-white shadow-sm bg-white' : '' ?>" style="border-radius: 8px;">Archived</a>
                <a href="?filter=all" class="btn btn-sm <?= $filter == 'all' ? 'btn-white shadow-sm bg-white' : '' ?>" style="border-radius: 8px;">All Items</a>
            </div>
        </div>
        <div class="col-md-6">
            <form method="GET" class="search-wrapper ms-auto">
                <input type="hidden" name="filter" value="<?= $filter ?>">
                <i class="bi bi-search"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by name...">
            </form>
        </div>
    </div>

    <!-- DATA TABLE -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $img_path = "uploads/" . $row['slug'] . "/" . $row['image'];
                            $stock = (int)$row['stock_quantity'];
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= file_exists($img_path) ? $img_path : 'assets/no-image.png' ?>" class="product-img">
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                        <div class="text-muted" style="font-size: 11px;">ID: #<?= $row['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-secondary"><?= htmlspecialchars($row['category_name']) ?></span></td>
                            <td class="fw-medium">৳<?= number_format($row['price'], 0) ?></td>
                            <td>
                                <div class="fw-bold <?= $stock <= 5 ? 'text-danger' : '' ?>"><?= $stock ?></div>
                                <div class="progress mt-1" style="height: 4px; width: 60px;">
                                    <div class="progress-bar <?= $stock > 10 ? 'bg-success' : ($stock > 0 ? 'bg-warning' : 'bg-danger') ?>" style="width: <?= min(($stock/50)*100, 100) ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <?php if ($stock > 10): ?>
                                    <span class="badge-pill bg-success-subtle">In Stock</span>
                                <?php elseif ($stock > 0): ?>
                                    <span class="badge-pill bg-warning-subtle">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge-pill bg-danger-subtle">Out</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="view_product.php?id=<?= $row['id'] ?>" class="btn-action btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn-action btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    
                                    <?php if ((int)$row['is_deleted'] === 1): ?>
                                        <a href="restore_product.php?id=<?= $row['id'] ?>" class="btn-action btn-outline-success" title="Restore"><i class="bi bi-arrow-counterclockwise"></i></a>
                                    <?php else: ?>
                                        <a href="delete_product.php?id=<?= $row['id'] ?>" 
                                           class="btn-action btn-outline-danger" 
                                           onclick="return confirm('Archive this product?')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No products found matching your criteria.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
    <nav class="d-flex justify-content-center mt-4">
        <ul class="pagination gap-2">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm rounded-3" href="?page=<?= $page-1 ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>"><i class="bi bi-chevron-left"></i></a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item">
                    <a class="page-link border-0 shadow-sm rounded-3 <?= ($i == $page) ? 'bg-primary text-white' : 'text-dark' ?>" href="?page=<?= $i ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link border-0 shadow-sm rounded-3" href="?page=<?= $page+1 ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>"><i class="bi bi-chevron-right"></i></a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

</body>
</html>