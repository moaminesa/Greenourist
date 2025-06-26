<?php
// MUST BE FIRST LINE - NO WHITESPACE BEFORE!
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Include configuration
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Get messages from session
$messages = [
    'login_error' => $_SESSION['login_error'] ?? '',
    'register_error' => $_SESSION['register_error'] ?? '',
    'register_success' => $_SESSION['register_success'] ?? ''
];

$activeForm = $_SESSION['active_form'] ?? 'login';

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Clear messages after displaying them
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['register_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Greenourist login page">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Greenourist Odyssey - Login</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="../assets/images/favicon.ico" type="image/x-icon">
    <style>
        /* NOTIFICATION STYLES - WILL NOT AFFECT PAGE LAYOUT */
        .force-notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 99999;
            animation: fadeIn 0.5s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: block !important;
            opacity: 1 !important;
            max-width: 80%;
            text-align: center;
        }
        .force-error {
            background-color: #ff4444 !important;
            border-left: 5px solid #cc0000 !important;
        }
        .force-success {
            background-color: #00C851 !important;
            border-left: 5px solid #007E33 !important;
        }
        .force-close {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.2em;
            padding-left: 15px;
        }
        @keyframes fadeIn {
            from { opacity: 0; top: 0; }
            to { opacity: 1; top: 20px; }
        }
    </style>
</head>
<body>

<!-- NOTIFICATION SYSTEM - ABOVE ALL CONTENT -->
<?php if (!empty($messages['login_error'])): ?>
    <div class="force-notification force-error">
        <?= htmlspecialchars($messages['login_error']) ?>
        <span class="force-close" onclick="this.parentElement.remove()">×</span>
    </div>
<?php endif; ?>

<?php if (!empty($messages['register_success'])): ?>
    <div class="force-notification force-success">
        <?= htmlspecialchars($messages['register_success']) ?>
        <span class="force-close" onclick="this.parentElement.remove()">×</span>
    </div>
<?php endif; ?>

<?php if (!empty($messages['register_error'])): ?>
    <div class="force-notification force-error">
        <?= htmlspecialchars($messages['register_error']) ?>
        <span class="force-close" onclick="this.parentElement.remove()">×</span>
    </div>
<?php endif; ?>

<!-- ORIGINAL PAGE CONTENT - PRESERVES ALL STYLING -->
<div class="container">
    <!-- Login Form -->
    <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="login_register.php" method="post" autocomplete="on">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <h2>Login</h2>
            <div class="input-group">
                <label for="login-email">Email</label>
                <input type="email" id="login-email" name="email" placeholder="Email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="input-group">
                <label for="login-password">Password</label>
                <input type="password" id="login-password" name="password" placeholder="Password" required>
                <i class="toggle-password fas fa-eye" data-target="login-password"></i>
            </div>
            <button type="submit" name="login" class="btn-primary">Login</button>
            <div class="form-footer">
                <p>Don't have an account? <a href="#" onclick="showForm('register-form'); return false;">Register</a></p>
                <p><a href="../auth/forgot_password.php">Forgot Password?</a></p>
            </div>
        </form>
    </div>

    <!-- Registration Form -->
    <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
    <form action="login_register.php" method="post" autocomplete="on">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <h2>Register</h2>
        
        <!-- Added Name Field -->
        <div class="input-group">
            <label for="register-name">Full Name</label>
            <input type="text" id="register-name" name="name" placeholder="Your Full Name" required
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        
        <div class="input-group">
            <label for="register-email">Email</label>
            <input type="email" id="register-email" name="email" placeholder="Email" required 
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        
        <div class="input-group">
            <label for="register-password">Password</label>
            <input type="password" id="register-password" name="password" placeholder="Password" required
                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                   title="Must contain at least 8 characters, including uppercase, lowercase and number">
            <i class="toggle-password fas fa-eye" data-target="register-password"></i>
            <div class="password-hint">
                Password must contain at least 8 characters, including uppercase, lowercase and number
            </div>
        </div>
        
        <div class="input-group">
            <label for="register-role">Role</label>
            <select id="register-role" name="role" required>
                <option value="">--Select Role--</option>
                <option value="user" <?= isset($_POST['role']) && $_POST['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= isset($_POST['role']) && $_POST['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        
        <button type="submit" name="register" class="btn-primary">Register</button>
        
        <div class="form-footer">
            <p>Already have an account? <a href="#" onclick="showForm('login-form'); return false;">Login</a></p>
        </div>
    </form>
</div>
</div>

<script src="../assets/script.js"></script>
<script>
// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function() {
        const target = document.getElementById(this.dataset.target);
        const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
        target.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
});

// Form switching
function showForm(formId) {
    document.querySelectorAll('.form-box').forEach(form => {
        form.style.display = form.id === formId ? 'block' : 'none';
    });
    history.pushState({}, '', `?form=${formId.replace('-form', '')}`);
}

// Initialize form display
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const formParam = urlParams.get('form');
    const defaultForm = '<?= $activeForm === 'register' ? 'register' : 'login' ?>';
    showForm(`${formParam || defaultForm}-form`);
    
    // Auto-hide notifications after 5 seconds
    document.querySelectorAll('.force-notification').forEach(notification => {
        setTimeout(() => {
            notification.style.animation = 'fadeIn 0.5s reverse';
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    });
});
</script>
</body>
</html>