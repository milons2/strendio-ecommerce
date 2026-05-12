<?php
session_start();
include('config.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'] ?? 1; // fallback if not stored

// GET ADMIN DATA
$result = mysqli_query($conn, "SELECT * FROM admin_users WHERE id = $admin_id");
$admin = mysqli_fetch_assoc($result);

$message = "";

// UPDATE PROFILE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // IMAGE UPLOAD
    $image = $admin['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/admin/";
        $image = time() . "_" . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_dir . $image);
    }

    // PASSWORD UPDATE
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE admin_users 
                  SET full_name='$full_name',
                      role='$role',
                      password='$hashed_password',
                      profile_image='$image'
                  WHERE id=$admin_id";
    } else {
        $query = "UPDATE admin_users 
                  SET full_name='$full_name',
                      role='$role',
                      profile_image='$image'
                  WHERE id=$admin_id";
    }

    mysqli_query($conn, $query);

    $message = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile Update</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <h3>Admin Profile Update</h3>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4">

        <!-- NAME -->
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control"
                   value="<?= $admin['full_name'] ?>" required>
        </div>

        <!-- ROLE -->
        <div class="mb-3">
            <label>Role</label>
            <input type="text" name="role" class="form-control"
                   value="<?= $admin['role'] ?>">
        </div>

        <!-- PASSWORD -->
        <div class="mb-3">
            <label>New Password (leave blank if no change)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <!-- IMAGE -->
        <div class="mb-3">
            <label>Profile Image</label><br>
            <img src="uploads/admin/<?= $admin['profile_image'] ?>" width="80"><br><br>
            <input type="file" name="profile_image" class="form-control">
        </div>

        <button class="btn btn-primary">Update Profile</button>

    </form>
</div>

</body>
</html>