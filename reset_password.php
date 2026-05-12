<?php
require 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists and is valid
    $stmt = $conn->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid
        $user = $result->fetch_assoc();
        $email = $user['email'];

        // Check if the form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password !== $confirm_password) {
                $message = "Passwords do not match. Please try again.";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the user's password and clear the reset token
                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $email);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "<div class='alert alert-success'>Your password has been successfully reset! <a href='login.html'>Login here</a>.</div>";
                    exit;
                } else {
                    $message = "Failed to reset password. Please try again.";
                }
            }
        }

        // Display the password reset form
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reset Password</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        </head>
        <body>
            <div class="container mt-5">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Reset Your Password</h2>
                        <?php if (isset($message)) { ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                        <?php } ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter your new password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        // Invalid or expired token
        echo "<div class='alert alert-danger'>Invalid or expired token. Please request a new password reset link.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>No reset token provided. Please request a password reset link.</div>";
}
?>
