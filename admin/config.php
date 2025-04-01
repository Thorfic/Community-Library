<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); 
define('DB_PASS', '');  // Set password if required
define('DB_NAME', 'library_db');

// Application configuration
define('SITE_NAME', 'Library Management System');
define('MAX_BOOKS_PER_USER', 3); // Max books a user can borrow
define('LOAN_DURATION_DAYS', 14); // Loan period in days

// Error reporting (Turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection function
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
            DB_USER, 
            DB_PASS, 
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return $conn;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage()); // Log error
        die("Database connection issue. Please try again later.");
    }
}

// Authentication check function
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Role check functions
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

// Redirect function
function redirect($path) {
    if (!headers_sent()) {
        header("Location: $path");
        exit();
    } else {
        echo "<script>window.location.href='$path';</script>";
        exit();
    }
}
?>
