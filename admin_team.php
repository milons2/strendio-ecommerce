<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config.php'; // your database connection

$admins = mysqli_query($conn, "SELECT full_name, profile_image, designation FROM admin_users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Team - STRENDio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 30px;
        }
        .team-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .admin-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            width: 220px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .admin-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #007bff;
            margin-bottom: 10px;
        }
        .admin-card h4 {
            margin: 5px 0;
            color: #333;
        }
        .admin-card p {
            margin: 0;
            color: #777;
        }
    </style>
</head>
<body>

<h2>STRENDio Admin Team</h2>
<div class="team-container">
    <?php while($row = mysqli_fetch_assoc($admins)): ?>
        <div class="admin-card">
            <img src="uploads/admin/<?= htmlspecialchars($row['profile_image']) ?>" alt="<?= htmlspecialchars($row['full_name']) ?>">
            <h4><?= htmlspecialchars($row['full_name']) ?></h4>
            <p><?= htmlspecialchars($row['designation']) ?></p>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>