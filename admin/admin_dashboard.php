<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if db_connect.php exists and include it
if (!file_exists('../db_connect.php')) {
    die("Error: db_connect.php not found at ../db_connect.php");
}
include '../db_connect.php';

// Session timeout (30 minutes)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: ../login.html?error=" . urlencode("Session timed out. Please log in again."));
    exit();
}

// Update last activity
$_SESSION['last_activity'] = time();

// Debug session variables
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "Debug: Session variables not set. user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'unset') . 
         ", role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'unset') . "<br>";
    header("Location: ../login.html?error=" . urlencode("Please log in first."));
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    echo "Debug: Role is " . $_SESSION['role'] . ", expected 'admin'<br>";
    header("Location: ../login.html?error=" . urlencode("Unauthorized access. Admin role required."));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, Admin</h2>
        <p><a href="manage_books.php">Manage Books</a></p>
        <p><a href="manage_users.php">Manage Users</a></p>
        <p><a href="../logout.php">Logout</a></p>
    </div>
</body>
</html>