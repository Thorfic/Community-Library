<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Your MySQL username
define('DB_PASS', '');         // Your MySQL password (leave empty if no password)
define('DB_NAME', 'library_db');

// Application configuration
define('SITE_NAME', 'Library Management System');
define('MAX_BOOKS_PER_USER', 3); // Maximum number of books a user can borrow
define('LOAN_DURATION_DAYS', 14); // Number of days a book can be borrowed

// Error reporting
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
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage() . "<br>Please check your database credentials in config.php");
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
    header("Location: $path");
    exit();
}
?> 