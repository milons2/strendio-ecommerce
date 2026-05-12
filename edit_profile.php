<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
    $stmt->execute();
    header("Location: my_account.php?updated=1");
    exit();
}

$stmt = $conn->prepare("SELECT full_name, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Strendio</title>
    <link rel="stylesheet" href="my_account.css">
</head>
<body>
<header class="account-header">
    <h1>Edit Profile</h1>
    <nav>
        <a href="my_account.php">Back to My Account</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="account-container">
    <form method="POST">
        <label>Full Name:
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </label>
        <label>Phone:
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </label>
        <label>Address:
            <textarea name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
        </label>
        <button type="submit">Save Changes</button>
    </form>
</main>
</body>
</html>