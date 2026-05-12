<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<?php include 'header.php'; ?>

<link rel="stylesheet" href="my_account.css">

<main class="account-container container mt-5 mb-5">
    <section class="account-details card p-4 shadow">
        <h2 class="mb-4">Account Information</h2>

        <?php if (isset($_SESSION['account_update_success'])): ?>
            <p class="alert alert-success"><?php echo $_SESSION['account_update_success']; unset($_SESSION['account_update_success']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['account_update_errors'])): ?>
            <ul class="alert alert-danger">
                <?php foreach ($_SESSION['account_update_errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; unset($_SESSION['account_update_errors']); ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="update_account.php">
            <div class="mb-3">
                <label class="form-label">Full Name:</label>
                <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone:</label>
                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address:</label>
                <textarea class="form-control" name="address" required><?php echo htmlspecialchars($user['address'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="submit" class="btn btn-success">Update Account</button>
                <a href="change_password.php" class="btn btn-outline-secondary">Change Password</a>
            </div>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>