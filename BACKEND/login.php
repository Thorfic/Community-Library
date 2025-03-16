<?php
session_start();
include 'db_connect.php';

// CSRF Token Generation and Validation Functions
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

session_regenerate_id(true);
ini_set('session.cookie_httponly', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        generateCsrfToken();

     if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    $username = filter_input(INPUT_POST, 'username'); 
    $password = $_POST['password'];
    $role = filter_input(INPUT_POST, var_name: 'role');

    if (!in_array($role, ['admin', 'user'])) {
         header("Location: index.html?error=" . urlencode("Invalid role selected"));
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ? AND role = ? AND status = 'active'");
    $stmt->execute([$username, $role]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time(); 
        header("Location: user_dashboard.php" . ($role == 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
        exit();
    } else {
       
        header("Location: index.html?error=" . urlencode("Invalid credentials or role"));
        exit();
    }
} else {
    header("Location: index.html");
    exit();
}
?>