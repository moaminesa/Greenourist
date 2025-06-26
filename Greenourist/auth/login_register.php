<?php
// Start session first with no parameters
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Now set cookie parameters (only if session wasn't already active)
if (!isset($_SESSION['cookie_params_set'])) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    $_SESSION['cookie_params_set'] = true;
}

require_once __DIR__ . '/../includes/config.php';
session_regenerate_id(true);

if (isset($_POST['register'])) {
    // Validate and sanitize inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['register_error'] = 'All fields are required';
        $_SESSION['active_form'] = 'register';
        header("Location: login_index.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        
        if ($stmt->execute()) {
            $_SESSION['register_success'] = 'Registration successful! Please login.';
            $_SESSION['active_form'] = 'login';
        } else {
            $_SESSION['register_error'] = 'Registration failed: ' . $conn->error;
            $_SESSION['active_form'] = 'register';
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['register_error'] = 'Database error: ' . $e->getMessage();
        $_SESSION['active_form'] = 'register';
    }
    
    header("Location: login_index.php");
    exit();
}

if (isset($_POST['login'])) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['login_error'] = 'Invalid CSRF token';
        $_SESSION['active_form'] = 'login';
        header("Location: login_index.php");
        exit();
    }
    
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Please fill in all fields';
        $_SESSION['active_form'] = 'login';
        header("Location: login_index.php");
        exit();
    }
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION['login_error'] = 'Email not found';
            $_SESSION['active_form'] = 'login';
            header("Location: login_index.php");
            exit();
        }
        
        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Incorrect password';
            $_SESSION['active_form'] = 'login';
            header("Location: login_index.php");
            exit();
        }
        
        // Successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        unset($_SESSION['login_error']);
        unset($_SESSION['register_error']);
        
        if ($user['role'] === 'admin') {
            header("Location: ../admin/admin_page.php");
        } else {
            header("Location: ../user_page.php");
        }
        exit();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['login_error'] = 'Database error: ' . $e->getMessage();
        $_SESSION['active_form'] = 'login';
        header("Location: login_index.php");
        exit();
    }
}
?>