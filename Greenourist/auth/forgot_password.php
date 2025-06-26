<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600); // 1-hour expiry
        
        // Store token in DB
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expires, $email);
        $stmt->execute();
        
        // Send email via PHP mail()
        $resetLink = "http://greenourist.com/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click here to reset your password: $resetLink (expires in 1 hour)";
        $headers = "From: no-reply@yourwebsite.com";
        if (isset($_SESSION['reset_success'])) {
            echo '<div class="success-message">' . $_SESSION['reset_success'] . '</div>';
            unset($_SESSION['reset_success']); 
        }
        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['reset_success'] = "Password reset link sent to your email!";
        } else {
            $_SESSION['reset_error'] = "Failed to send email. Check server settings.";
        }
    } else {
        $_SESSION['reset_error'] = "Email not found!";
    }
    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <div class="container">
        <div class="form-box active">
            <h2>Reset Password</h2>
            <?php if (!empty($error)): ?>
                <p class="error-message"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Your Email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            <p><a href="../auth/login_index.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>