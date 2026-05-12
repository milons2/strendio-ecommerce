<?php
session_start();

// Set a session timeout duration (in seconds) - e.g., 30 minutes
$session_timeout = 1800;

// Check if the user is NOT logged in (user_id is not set)
if (!isset($_SESSION['user_id'])) {
    echo "Session not set or expired. Redirecting to login.";
    header("Location: login.php?message=not_logged_in");
    exit();
}
// User is logged in, check for session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
        echo "Session expired due to inactivity.";
        // Last activity was too long ago
        session_unset();     // Unset all session variables
        session_destroy();   // Destroy the session
        header("Location: login.php?message=session_expired");
        exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
