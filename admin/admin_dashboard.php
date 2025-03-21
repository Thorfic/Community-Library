<a?php
session_start();
if (isset($_SESSION["user_id"]) || $_SESSION["role"] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<a! DOCTYPE html>
<html lang="en">
    <head>
       <meta charset ="UTF-8">
       <title>Admin Dashboard</title>
       <link rel="stylesheet" href="../style.css">
    </head>
    <body>
        <div class="container">
            <h2>Welcome,Admin</h2>
            <p><a href="manange_books.php">Manage Books</a></p>
            <p><a href="manage_users.phP">Manage Users</a></p>
            <p><a href="../logout.php">Logout<p></p>
        </div>
    </body>
</html>