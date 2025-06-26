<?php
session_start();
require_once 'config.php';

$token = $_GET['token'] ?? '';
$error = $_SESSION['reset_error'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $token = $_POST['token'];
    
    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update password and clear token
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $password, $token);
        $stmt->execute();
        
        $_SESSION['reset_success'] = "Password updated successfully!";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['reset_error'] = "Invalid or expired token!";
        header("Location: forgot_password.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

</head>
<body>
    <div class="container">
        <div class="form-box active">
            <h2>Set New Password</h2>
            <?php if (!empty($error)): ?>
                <p class="error-message"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <input type="password" name="password" placeholder="New Password" required>
                <button type="submit">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>
