<?php
session_start();
if (isset($_SESSION["user_id"]) || $_SESSION['role'] !='admin') {
    header("Location: ../index.php") ;
    exit();
}   
if (isset($_GET['delete'])) {
    $book_id = $_GET['delete'];
    $stmt =$pdo->prepare("UPDATE Books SET status = 'deleted' WHERE book_id = ?");
    $stmt->execute([$book_id]);
}
?>
<! DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Manage Books</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
        <div class="container">
            <h2>Manage Books</h2>
            <p><a href="add_book.php">Add New Book</a></p>
            <table>
            <tr><th>ID</th><th>Title</th><th>Author</th><th>ISBN</th><th>Genre</th><th>Quantity</th><th>Actions</th></tr>
                <?php
                $stmt= $pdo->query("SELECT * FROM Books Where status = 'active'");
                while ($book = $stmt->fetch()){
                    echo "<tr>";
                    echo "<td>{$book['book_id']}</td>";
                    echo "<td>{$book['book_title']}</td>";
                    echo "<td>{$book['book_author']}</td>";
                    echo "<td>{$book['ISBN']}</td>";
                    echo "<td>{$book['genre']}</td>";
                    echo "<td>{$book['quantity']}</td>";
                    echo "<td><a href='manage_books.php?delete={$book['book_id']}</td>";
                }
            </table> 
            <p><a href="dashboard.php">Back to Dashboard</a></p>
        </div>
    </body>
</html>