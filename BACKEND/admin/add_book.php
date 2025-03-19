<?php
session_start();
if (isset($_SESSION["user id"]) && $_SESSION["role"] != 'admin') {
    header('Location: ../index.php');
    exit();
}
include'../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title =$_POST['title'];
    $author=$_POST['author'];
    $genre=$_POST['genre'];
    $quantity=$_POST['quantity'];
}

    $stmt =$pdo->prepare('INSERT INTO Books (title, author, genre, quantity) VALUES (?,?,?,?)');
    if ($stmt->execute([$title, $author, $genre, $quantity])) {
        header("Location:manage_books.php");
        exit();
    } else {
        echo"Error adding book.";
    }
?>
<a! DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add Book</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
        <div class="container">
            <h2>Add New Book</h2>
            <form method="POST">
                <label>Title:</label><input type="text" name="title" required>
                <label>Author:</label><input type="text"> name="author" required>
                <label>Genre:</label><input type="text"> name="genre" required>
                <label>quantity:</label><input type="number" name="quantity" min="0" required>
                <button type="submit">Add Book</button>
            </form>
            <p><a href="manage_books.php">Back to Manage Books</a></p>p>
        </div>
    </body>
</html>