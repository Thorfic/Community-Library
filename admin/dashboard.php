<?php
require_once '../config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$conn = getDBConnection();

// book deletion
if (isset($_POST['delete_book'])) {
    $book_id = $_POST['book_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
        $stmt->execute([$book_id]);
    } catch(PDOException $e) {
        $error = "Error deleting book: " . $e->getMessage();
    }
}

// user deletion
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role != 'admin'");
        $stmt->execute([$user_id]);
    } catch(PDOException $e) {
        $error = "Error deleting user: " . $e->getMessage();
    }
}

// Get search and sort parameters
$search = $_GET['search'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'title';
$sort_order = $_GET['sort_order'] ?? 'asc';

// search and sort query for books
$books_query = "SELECT b.*, 
                (SELECT COUNT(*) FROM loans l WHERE l.book_id = b.book_id AND l.return_date IS NULL) as current_loans
                FROM books b";
if (!empty($search)) {
    $books_query .= " WHERE b.title LIKE :search OR b.author LIKE :search OR b.isbn LIKE :search";
}
$books_query .= " ORDER BY " . $sort_by . " " . $sort_order;

// Fetch books with search and sort
$stmt = $conn->prepare($books_query);
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
}
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users
$users = $conn->query("SELECT * FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);

// Fetch current loans
$loans_query = "SELECT l.*, b.title as book_title, u.username 
                FROM loans l 
                JOIN books b ON l.book_id = b.book_id 
                JOIN users u ON l.user_id = u.user_id 
                WHERE l.return_date IS NULL 
                ORDER BY l.loan_date DESC";
$current_loans = $conn->query($loans_query)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .books-section, .loans-section, .users-section {
            display: none;
            transition: all 0.3s ease;
        }
        .books-section.show, .loans-section.show, .users-section.show {
            display: block;
        }
        .display-btn {
            transition: all 0.3s ease;
        }
        .display-btn i {
            transition: transform 0.3s ease;
        }
        .display-btn.active i {
            transform: rotate(180deg);
        }
        .card {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><?php echo SITE_NAME; ?> - Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_book.php">Add Book</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_user.php">Add User</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Books -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Books Management</h5>
                        <div>
                            <button class="btn btn-outline-primary display-btn me-2" type="button" data-bs-toggle="collapse" data-bs-target="#booksSection">
                                <i class="bi bi-chevron-down"></i> Display Books
                            </button>
                            <a href="add_book.php" class="btn btn-primary btn-sm">Add New Book</a>
                        </div>
                    </div>
                    <div class="collapse books-section" id="booksSection">
                        <div class="card-body">
                            <!-- Form for Search -->
                            <form class="mb-3" method="GET">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="?sort_by=title&sort_order=<?php echo $sort_by === 'title' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>" class="text-dark text-decoration-none">
                                                    Title
                                                    <?php if ($sort_by === 'title'): ?>
                                                        <i class="bi bi-arrow-<?php echo $sort_order === 'asc' ? 'up' : 'down'; ?>"></i>
                                                    <?php endif; ?>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="?sort_by=author&sort_order=<?php echo $sort_by === 'author' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>&search=<?php echo urlencode($search); ?>" class="text-dark text-decoration-none">
                                                    Author
                                                    <?php if ($sort_by === 'author'): ?>
                                                        <i class="bi bi-arrow-<?php echo $sort_order === 'asc' ? 'up' : 'down'; ?>"></i>
                                                    <?php endif; ?>
                                                </a>
                                            </th>
                                            <th>Available</th>
                                            <th>Current Loans</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($books as $book): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                                <td><?php echo $book['available_quantity']; ?>/<?php echo $book['quantity']; ?></td>
                                                <td><?php echo $book['current_loans']; ?></td>
                                                <td>
                                                    <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                                        <button type="submit" name="delete_book" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- To display Current Loans -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Current Loans</h5>
                        <button class="btn btn-outline-primary display-btn btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#loansSection">
                            <i class="bi bi-chevron-down"></i> Display Loans
                        </button>
                    </div>
                    <div class="collapse loans-section" id="loansSection">
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach ($current_loans as $loan): ?>
                                    <div class="list-group-item">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($loan['book_title']); ?></h6>
                                        <p class="mb-1">Borrowed by: <?php echo htmlspecialchars($loan['username']); ?></p>
                                        <small class="text-muted">Loan Date: <?php echo date('M d, Y', strtotime($loan['loan_date'])); ?></small>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($current_loans)): ?>
                                    <div class="list-group-item text-center text-muted">
                                        No active loans
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Users Management</h5>
                        <div>
                            <button class="btn btn-outline-primary display-btn me-2" type="button" data-bs-toggle="collapse" data-bs-target="#usersSection">
                                <i class="bi bi-chevron-down"></i> Display Users
                            </button>
                            <a href="add_user.php" class="btn btn-primary btn-sm">Add New User</a>
                        </div>
                    </div>
                    <div class="collapse users-section" id="usersSection">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo ucfirst($user['role']); ?></td>
                                                <td>
                                                    <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if ($user['role'] !== 'admin'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const displayBtns = document.querySelectorAll('.display-btn');
            
            displayBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    this.classList.toggle('active');
                });
            });

            // Show sections if there's a search query or sort parameter
            if (window.location.search.includes('search=') || window.location.search.includes('sort_by=')) {
                document.querySelector('.books-section').classList.add('show');
                document.querySelector('.display-btn[data-bs-target="#booksSection"]').classList.add('active');
            }
        });
    </script>
</body>
</html>