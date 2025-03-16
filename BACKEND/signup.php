<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['new_username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $new_role = $_POST['new_role'] ?? '';
    
    // Dummy user storage (replace with actual database logic)
    $users = [
        'admin' => ['username' => 'admin', 'password' => 'admin123', 'role' => 'admin'],
        'user' => ['username' => 'user', 'password' => 'user123', 'role' => 'user']
    ];
    
    if (!isset($users[$new_username])) {
        // Normally, you would insert into a database
        echo "<script>alert('Signup successful! You can now log in.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Username already exists!'); window.location.href='login.html';</script>";
    }
} else {
    header("Location: login.html"); 
    exit();
}
?>