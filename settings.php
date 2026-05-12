<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
include('config.php');

// You can fetch current settings here if needed

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process submitted settings
    if (isset($_POST['notification_preference'])) {
        $notificationPreference = $_POST['notification_preference'];
        // Update user's notification preference in the database
        $updateQuery = "UPDATE users SET notification_preference = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateQuery);
        $stmtUpdate->bind_param("si", $notificationPreference, $user_id);

        if ($stmtUpdate->execute()) {
            $success = 'Notification settings updated successfully!';
        } else {
            $error = 'Error updating notification settings: ' . $stmtUpdate->error;
        }
        $stmtUpdate->close();
    }
    // Add more settings processing as needed
}

define('ACCESS_ALLOWED', true);
include('dashboard_layout.php');
?>

<main class="main-content">
    <header class="dashboard-header">
        <div class="mobile-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div class="user-info">
            <span class="greeting">Hello, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span>
            <span class="user-id">User ID: <?php echo htmlspecialchars($user_id); ?></span>
        </div>
        <link rel="stylesheet" href="settings.css">
    </header>

    <section class="settings-section">
        <h2>Account Settings</h2>
        <?php if ($success) : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error) : ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="settings-form">
            <div class="form-group">
                <label for="notification_preference">Notification Preferences:</label>
                <select id="notification_preference" name="notification_preference">
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                    <option value="both">Both</option>
                    <option value="none">None</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="button">Save Settings</button>
            </div>
        </form>

        </section>
</main>

</div> </body>
</html>