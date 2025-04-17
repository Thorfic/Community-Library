<?php
require_once '../config.php';

// Check if user is logged in and is user
if (!isLoggedIn() || !isUser()) {
    redirect('../login.php');
}

$conn = getDBConnection();
$error = '';
$success = '';

// Book borrowing
if (isset($_POST['borrow_book'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Check if user has reached maximum books limit
        $stmt = $conn->prepare("SELECT COUNT(*) FROM loans WHERE user_id = ? AND status = 'active'");
        $stmt->execute([$user_id]);
        if ($stmt->fetchColumn() >= MAX_BOOKS_PER_USER) {
            $error = 'You have reached the maximum number of books you can borrow';
        } else {
            // Check if book is available
            $stmt = $conn->prepare("SELECT available_quantity FROM books WHERE book_id = ?");
            $stmt->execute([$book_id]);
            $available = $stmt->fetchColumn();
            
            if ($available > 0) {
                // Start transaction
                $conn->beginTransaction();
                
                // Update book quantity
                $stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = ?");
                $stmt->execute([$book_id]);
                
                // Create loan record
                $due_date = date('Y-m-d H:i:s', strtotime('+' . LOAN_DURATION_DAYS . ' days'));
                $stmt = $conn->prepare("INSERT INTO loans (user_id, book_id, due_date) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $book_id, $due_date]);
                
                $conn->commit();
                $success = 'Book borrowed successfully';
            } else {
                $error = 'Book is not available';
            }
        }
    } catch(PDOException $e) {
        $conn->rollBack();
        $error = "Error borrowing book: " . $e->getMessage();
    }
}

// Handle book return
if (isset($_POST['return_book'])) {
    $loan_id = $_POST['loan_id'];
    $book_id = $_POST['book_id'];
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Update book quantity
        $stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE book_id = ?");
        $stmt->execute([$book_id]);
        
        // Update loan status
        $stmt = $conn->prepare("UPDATE loans SET status = 'returned', return_date = CURRENT_TIMESTAMP WHERE loan_id = ?");
        $stmt->execute([$loan_id]);
        
        $conn->commit();
        $success = 'Book returned successfully';
    } catch(PDOException $e) {
        $conn->rollBack();
        $error = "Error returning book: " . $e->getMessage();
    }
}

// Search books
$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? '';
$where = [];
$params = [];

if ($search) {
    $where[] = "(title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($genre) {
    $where[] = "genre = ?";
    $params[] = $genre;
}

$sql = "SELECT * FROM books";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY title";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's active loans
$stmt = $conn->prepare("
    SELECT l.*, b.title, b.author 
    FROM loans l 
    JOIN books b ON l.book_id = b.book_id 
    WHERE l.user_id = ? AND l.status = 'active' 
    ORDER BY l.due_date
");
$stmt->execute([$_SESSION['user_id']]);
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique genres for filter
$genres = $conn->query("SELECT DISTINCT genre FROM books ORDER BY genre")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-book" style="margin-right: 8px;"></i>
                <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-grid"></i> Dashboard
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="bi bi-box-arrow-right"></i> Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Search-option  -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-search"></i> Search Books</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-book"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" name="search" placeholder="Search by title or author" value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="genre">
                                    <option value="">All Genres</option>
                                    <?php foreach ($genres as $g): ?>
                                        <option value="<?php echo htmlspecialchars($g); ?>" <?php echo $genre === $g ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($g); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive mt-4">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Genre</th>
                                        <th>Available</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($books as $book): ?>
                                        <tr>
                                            <td class="fw-medium"><?php echo htmlspecialchars($book['title']); ?></td>
                                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                                            <td><span class="badge bg-light text-dark"><?php echo htmlspecialchars($book['genre']); ?></span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 <?php echo $book['available_quantity'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                                        <i class="bi <?php echo $book['available_quantity'] > 0 ? 'bi-circle-fill' : 'bi-circle'; ?>"></i>
                                                    </div>
                                                    <?php echo $book['available_quantity']; ?>/<?php echo $book['quantity']; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($book['available_quantity'] > 0): ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                                        <button type="submit" name="borrow_book" class="btn btn-sm btn-success">
                                                            <i class="bi bi-plus-circle"></i> Borrow
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Not Available
                                                    </span>
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

            <!-- Active Loans Section -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-collection"></i> My Active Loans</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($loans)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-4"></i>
                                <p class="mt-3">No active loans</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($loans as $loan): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($loan['title']); ?></h6>
                                            <?php
                                                $due_date = strtotime($loan['due_date']);
                                                $days_left = ceil(($due_date - time()) / (60 * 60 * 24));
                                                $badge_class = $days_left <= 3 ? 'bg-danger' : ($days_left <= 7 ? 'bg-warning text-dark' : 'bg-success');
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo $days_left; ?> days left
                                            </span>
                                        </div>
                                        <p class="mb-2 text-muted">By <?php echo htmlspecialchars($loan['author']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Due: <?php echo date('M d, Y', strtotime($loan['due_date'])); ?></small>
                                            <form method="POST">
                                                <input type="hidden" name="loan_id" value="<?php echo $loan['loan_id']; ?>">
                                                <input type="hidden" name="book_id" value="<?php echo $loan['book_id']; ?>">
                                                <button type="submit" name="return_book" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-arrow-return-left"></i> Return
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>