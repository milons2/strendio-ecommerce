<?php
require 'config.php'; // Include your database connection file
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('UTC'); // Set PHP timezone to UTC
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON body
    $data = json_decode(file_get_contents("php://input"), true);
    $email = isset($data['email']) ? trim($data['email']) : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email address."]);
        exit;
    }

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, generate reset token and expiry
        $reset_token = bin2hex(random_bytes(32)); // Secure random token
        $reset_token_expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Expiry time (1 hour from now)

        // Update the database with the reset token
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $reset_token, $reset_token_expiry, $email);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Send email with PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
                $mail->SMTPAuth = true;
                $mail->Username = 'amazingboy367@gmail.com'; // Replace with your email
                $mail->Password = 'hoglwumwibgqotnw'; // Replace with your email app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('amazingboy367@gmail.com', 'Strendio'); // Replace with your info
                $mail->addAddress($email);

                // Content
                $reset_link = "https://www.strendio.com/reset_password.php?token=" . $reset_token;
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Click <a href='$reset_link'>here</a> to reset your password. This link will expire in 1 hour.";

                $mail->send();
                echo json_encode(["status" => "success", "message" => "A password reset link has been sent to your email."]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Failed to send email. Mailer Error: " . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update reset token in the database."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
