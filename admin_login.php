<?php
session_start();
include('config.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {

        $query = "SELECT * FROM admin_users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {

            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {

                $admin = mysqli_fetch_assoc($result);

                // Verify password
                if (password_verify($password, $admin['password'])) {

                    // 🔐 Prevent session fixation
                    session_regenerate_id(true);

                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];

                    // ✅ Handle NULL values safely
                    $_SESSION['admin_full_name'] = !empty($admin['full_name']) 
                        ? $admin['full_name'] 
                        : 'Administrator';

                    $_SESSION['admin_designation'] = !empty($admin['designation']) 
                        ? $admin['designation'] 
                        : 'Admin';

                    // ✅ Fix image issue (IMPORTANT)
                    $_SESSION['admin_image'] = !empty($admin['profile_image']) 
                        ? $admin['profile_image'] 
                        : 'default.png'; // put default image in your folder

                    header("Location: admin_dashboard.php");
                    exit();

                } else {
                    $error = "Invalid username or password!";
                }

            } else {
                $error = "Invalid username or password!";
            }

            mysqli_stmt_close($stmt);

        } else {
            $error = "Database error!";
        }

    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Login - STRENDio</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(135deg, #eef2ff, #f8f9fa);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Arial, sans-serif;
}

.login-card{
    width: 100%;
    max-width: 420px;
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.logo{
    text-align: center;
    margin-bottom: 20px;
}

.logo img{
    height: 60px;
}

h3{
    text-align: center;
    font-weight: 700;
    margin-bottom: 20px;
    color: #0d6efd;
}

.form-control{
    border-radius: 10px;
    height: 45px;
}

.btn-primary{
    border-radius: 10px;
    height: 45px;
    font-weight: 600;
}

.error{
    background: #ffe6e6;
    color: #d60000;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 15px;
    font-size: 14px;
}
</style>

</head>
<body>

<div class="login-card">

    <div class="logo">
        <img src="strendio_logo2.png" alt="Logo">
    </div>

    <h3>Admin Login</h3>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Login
        </button>

    </form>

</div>

</body>
</html>