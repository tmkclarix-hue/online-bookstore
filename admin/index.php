<?php
session_start();
$host = "localhost"; $user = "root"; $password = ""; $database = "bookstore_db";
$conn = mysqli_connect($host, $user, $password, $database);
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - BookStore</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav>
    <div class="logo">📚 Admin Panel</div>
    <ul>
        <li><a href="../index.php">View Site</a></li>
        <li><a href="add_book.php">Add Book</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</nav>
<div class="container">
    <h2>📊 Dashboard</h2>
    <?php
    $tb = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM books"))['c'];
    $tu = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM users"))['c'];
    $to = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM orders"))['c'];
    ?>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:30px;">
        <div style="background:#3498db;color:white;padding:25px;border-radius:10px;text-align:center;">
            <h3 style="font-size:36px;"><?php echo $tb; ?></h3><p>Total Books</p>
        </div>
        <div style="background:#27ae60;color:white;padding:25px;border-radius:10px;text-align:center;">
            <h3 style="font-size:36px;"><?php echo $tu; ?></h3><p>Total Users</p>
        </div>
        <div style="background:#e74c3c;color:white;padding:25px;border-radius:10px;text-align:center;">
            <h3 style="font-size:36px;"><?php echo $to; ?></h3><p>Total Orders</p>
        </div>
    </div>
    <?php if(isset($_GET['msg'])): ?>
        <div class="success">
            <?php echo $_GET['msg']=='deleted' ? 'Book deleted!' : 'Book updated!'; ?>
        </div>
    <?php endif; ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
        <h2>📚 Manage Books</h2>
        <a href="add_book.php" class="btn btn-success">+ Add New Book</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th><th>Title</th><th>Author</th>
                <th>Category</th><th>Price</th><th>Stock</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $books = mysqli_query($conn,"SELECT b.*,c.name as category FROM books b JOIN categories c ON b.category_id=c.id ORDER BY b.id DESC");
        $i=1;
        while($book = mysqli_fetch_assoc($books)):
        ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($book['title']); ?></td>
            <td><?php echo htmlspecialchars($book['author']); ?></td>
            <td><?php echo htmlspecialchars($book['category']); ?></td>
            <td>Rs. <?php echo number_format($book['price'],2); ?></td>
            <td><?php echo $book['stock']; ?></td>
            <td>
                <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn">Edit</a>
                <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger"
                   onclick="return confirm('Delete this book?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<footer><p>&copy; 2024 BookStore. All rights reserved.</p></footer>
</body>
</html>