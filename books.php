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
    <title>Books - BookStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

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

<div class="container">
    <h2>📚 All Books</h2>

    <!-- Search & Filter Form -->
    <div style="background:white; padding:20px; border-radius:10px; margin-bottom:25px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
        <form method="GET" action="" id="searchForm">
            <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:flex-end;">
                <div class="form-group" style="margin:0; flex:1;">
                    <label>Search Book</label>
                    <input type="text" name="search" id="search" placeholder="Title or Author..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <span class="error" id="searchError"></span>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php
                        $cats = mysqli_query($conn, "SELECT * FROM categories");
                        while($cat = mysqli_fetch_assoc($cats)):
                        $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $selected; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group" style="margin:0;">
                    <label>Sort By</label>
                    <select name="sort">
                        <option value="">Default</option>
                        <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort']=='price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort']=='price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="title" <?php echo (isset($_GET['sort']) && $_GET['sort']=='title') ? 'selected' : ''; ?>>Title A-Z</option>
                    </select>
                </div>
                <button type="submit" class="btn">Search 🔍</button>
                <a href="books.php" class="btn" style="background:#95a5a6;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Books Grid -->
    <?php
    $where = "WHERE 1=1";
    if(!empty($_GET['search'])){
        $search = mysqli_real_escape_string($conn, $_GET['search']);
        $where .= " AND (b.title LIKE '%$search%' OR b.author LIKE '%$search%')";
    }
    if(!empty($_GET['category'])){
        $cat_id = (int)$_GET['category'];
        $where .= " AND b.category_id = $cat_id";
    }

    $order = "ORDER BY b.id DESC";
    if(!empty($_GET['sort'])){
        if($_GET['sort'] == 'price_asc') $order = "ORDER BY b.price ASC";
        if($_GET['sort'] == 'price_desc') $order = "ORDER BY b.price DESC";
        if($_GET['sort'] == 'title') $order = "ORDER BY b.title ASC";
    }

    $result = mysqli_query($conn, "SELECT b.*, c.name as category FROM books b JOIN categories c ON b.category_id = c.id $where $order");
    $count = mysqli_num_rows($result);
    ?>

    <p style="margin-bottom:15px; color:#666;">Showing <strong><?php echo $count; ?></strong> books</p>

    <?php if($count == 0): ?>
        <div style="text-align:center; padding:50px; background:white; border-radius:10px;">
            <p style="font-size:20px;">😕 No books found!</p>
            <a href="books.php" class="btn" style="margin-top:15px;">View All Books</a>
        </div>
    <?php else: ?>
    <div class="book-grid">
        <?php while($book = mysqli_fetch_assoc($result)): ?>
        <div class="book-card">
            <div class="book-img">📖</div>
            <h3><?php echo $book['title']; ?></h3>
            <p class="author">by <?php echo $book['author']; ?></p>
            <p class="category">📂 <?php echo $book['category']; ?></p>
            <p class="price">Rs. <?php echo number_format($book['price'], 2); ?></p>
            <p style="font-size:12px; color:<?php echo $book['stock'] > 0 ? '#27ae60' : '#e74c3c'; ?>">
                <?php echo $book['stock'] > 0 ? "In Stock ({$book['stock']})" : "Out of Stock"; ?>
            </p>
            <a href="book_detail.php?id=<?php echo $book['id']; ?>" class="btn" style="margin-top:10px;">View Details</a>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 BookStore. All rights reserved.</p>
</footer>

<script>
document.getElementById('searchForm').addEventListener('submit', function(e){
    let search = document.getElementById('search').value.trim();
    if(search.length === 1){
        document.getElementById('searchError').textContent = 'Enter at least 2 characters!';
        e.preventDefault();
    } else {
        document.getElementById('searchError').textContent = '';
    }
});
</script>

</body>
</html>