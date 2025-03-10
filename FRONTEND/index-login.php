<!DOCTYPE html>
<html lang="eng">
    <head>
        <meta charset="UTF-8">
        <h1>URQUHART LIBRARY MANAGEMENT</h1><br>
        <title>Library Login</title>
        <link rel="stylesheet" href="style.css">
        <script>
            function validateForm () {
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;
                if(!username || !password) {
                    alert('Please fill in all fields');
                    return false
                }
                return true;
            }
        </script>
    </head>
    <body>
        <div class="container">
            <h2>Library Login</h2>
            <form method="POST" action="index.php" onsubmit="return validateForm()">
                    <label>Username:</label>
                    <input type="text" name="username" id="username" required><br>
                    <label>Passwowrd:</label>
                    <input type="password" name="password" id="password" required><br>
                    <label>Role:</label>
                    <select name="role" required>
                        <option value="user">User</option>
                    </select><br>
                    <button type="submit">Login</button>
            </form>
        <?php
        session start ();
        if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
            include 'db_connect.php';
            $username = $_POST['username'];
            $password = $_POST['password'];
            $role = $_POST['role'];

            $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ? AND role = ? AND status = 'active'")
            $stmt->execute([$username, role]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                header("Location; " . ($role == 'admin' ? 'admin/dashboard.php' : user/dashboard.php'));
                exit();
                } else {
                    echo "<p style='color:red;'Invalid credentials or role</p>"    
                }
        }
        ?>
    </div>
    </body>
</html>