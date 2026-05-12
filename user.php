<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Optionally, you can fetch user details from the database using $_SESSION['user_id']
// Example:
// require_once 'config.php';
// $stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
// $stmt->bind_param("i", $_SESSION['user_id']);
// $stmt->execute();
// $stmt->bind_result($fullName, $email);
// $stmt->fetch();
// $stmt->close();
// $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="user.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Ridge Fashion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger">Logout</a>

                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="text-center">
            <h1>Welcome to Ridge Fashion!</h1>
            <p>Your one-stop destination for the latest trends and styles.</p>
        </div>

        <!-- Example Sections -->
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="card">
                    <img src="images/new-arrivals.jpg" class="card-img-top" alt="New Arrivals">
                    <div class="card-body">
                        <h5 class="card-title">New Arrivals</h5>
                        <p class="card-text">Check out the latest additions to our collection.</p>
                        <a href="#" class="btn btn-primary">Shop Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="images/sale.jpg" class="card-img-top" alt="Sale">
                    <div class="card-body">
                        <h5 class="card-title">On Sale</h5>
                        <p class="card-text">Grab the best deals before they're gone.</p>
                        <a href="#" class="btn btn-primary">Explore Sale</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="images/fashion-tips.jpg" class="card-img-top" alt="Fashion Tips">
                    <div class="card-body">
                        <h5 class="card-title">Fashion Tips</h5>
                        <p class="card-text">Stay ahead with our style guide and tips.</p>
                        <a href="#" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 Strendio | All rights reserved.</p>
    </footer>

    <script src="index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
