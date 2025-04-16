# Library Management System

A web-based library management system built with PHP, MySQL, and Bootstrap. This system allows administrators to manage books and users, while users can search for, borrow, and return books.

## Features

- Role-based access control (Admin/User)
- Book management (Add, Edit, Delete)
- User management (Add, Edit, Delete)
- Book search with filters
- Book borrowing and returning
- Loan management
- Real-time book availability updates

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. Clone the repository to your web server directory:
   ```bash
   git clone https://github.com/yourusername/library-management.git
   ```

2. Create a MySQL database and import the database structure:
   ```bash
   mysql -u root -p < database.sql
   ```

3. Configure the database connection in `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'library_db');
   ```

4. Set up your web server to point to the project directory.

## Default Admin Account

- Username: admin
- Password: Admin@123

## Directory Structure

```
library-management/
├── admin/
│   ├── dashboard.php
│   ├── add_book.php
│   └── add_user.php
├── user/
│   └── dashboard.php
├── config.php
├── database.sql
├── login.php
└── logout.php
└── index.php
```

## Usage

1. Access the system through your web browser
2. Log in using the default admin account or create a new user account
3. Admin users can:
   - Add, edit, and delete books
   - Manage user accounts
   - View all books and users
4. Regular users can:
   - Search for books
   - Borrow available books
   - Return borrowed books
   - View their active loans

## Security Features

- Password hashing
- SQL injection prevention using prepared statements
- XSS prevention using htmlspecialchars
- Session management
- Role-based access control

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.