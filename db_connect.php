<?php
$host = 'localhost';
$dbname = 'library_management';
$username = 'root';
$password = 'Bp240917';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>