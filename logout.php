<?php
session_start(); // Start the session

// Destroy the session
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session itself

// Redirect to the login page
header("Location: login_form.php");
exit;
?>
