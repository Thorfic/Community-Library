<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Dummy authentication (replace with actual database check)
    $valid_user = "admin";
    $valid_pass = "password123";
    
    if ($username === $valid_user && $password === $valid_pass) {
        $_SESSION['user'] = $username;
        header("Location: dashboard.php"); // Redirect to dashboard
        exit();
    } else {
        echo "<script>alert('Invalid username or password!'); window.location.href='login.html';</script>";
    }
} else {
    header("Location: login.html"); // Redirect if accessed directly
    exit();
}
?>
