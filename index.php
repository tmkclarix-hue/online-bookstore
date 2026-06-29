<?php
session_start();
$host = "localhost"; $user = "root"; $password = ""; $database = "bookstore_db";
$conn = mysqli_connect($host, $user, $password, $database);
?>
<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookStore - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navigation -->
<nav>
    <div class="logo">📚 BookStore</div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="books.php">Books</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="cart.php">Cart 🛒</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <li><a href="admin/index.php">Admin Panel</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Hero Section -->
<div class="hero">
    <h1>Welcome to BookStore 📚</h1>
    <p>Find your next favorite book!</p>
    <a href="books.php" class="btn">Browse Books</a>
</div>

<!-- Featured Books -->
<div class="container">
    <h2>Featured Books</h2>
    <div class="book-grid">
        <?php
        $result = mysqli_query($conn, "SELECT b.*, c.name as category FROM books b JOIN categories c ON b.category_id = c.id LIMIT 6");
        while($book = mysqli_fetch_assoc($result)):
        ?>
        <div class="book-card">
            <div class="book-img">📖</div>
            <h3><?php echo $book['title']; ?></h3>
            <p class="author">by <?php echo $book['author']; ?></p>
            <p class="category"><?php echo $book['category']; ?></p>
            <p class="price">Rs. <?php echo number_format($book['price'], 2); ?></p>
            <a href="book_detail.php?id=<?php echo $book['id']; ?>" class="btn">View Details</a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 BookStore. All rights reserved.</p>
</footer>

</body>
</html>