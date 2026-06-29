<?php
session_start();
$host = "localhost"; $user = "root"; $password = ""; $database = "bookstore_db";
$conn = mysqli_connect($host, $user, $password, $database);

if(!isset($_GET['id'])){
    header("Location: books.php"); exit();
}

$id = (int)$_GET['id'];
$book = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT b.*, c.name as category FROM books b JOIN categories c ON b.category_id = c.id WHERE b.id = $id"));

if(!$book){
    header("Location: books.php"); exit();
}

$success = "";
if(isset($_POST['add_to_cart'])){
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php"); exit();
    }
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $qty = (int)$_POST['quantity'];
    if($qty < 1) $qty = 1;
    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }
    $success = "Book added to cart! <a href='cart.php'>View Cart</a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $book['title']; ?> - BookStore</title>
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
    <a href="books.php" style="color:#3498db;">← Back to Books</a>

    <?php if($success): ?>
        <div class="success" style="margin-top:15px;"><?php echo $success; ?></div>
    <?php endif; ?>

    <div style="background:white; border-radius:10px; padding:40px; margin-top:20px;
                box-shadow:0 2px 8px rgba(0,0,0,0.1); display:flex; gap:40px; flex-wrap:wrap;">

        <div style="font-size:120px; text-align:center; min-width:200px;">📖</div>

        <div style="flex:1;">
            <h1 style="font-size:28px; color:#2c3e50; margin-bottom:10px;">
                <?php echo htmlspecialchars($book['title']); ?>
            </h1>
            <p style="font-size:18px; color:#666; margin-bottom:5px;">
                by <strong><?php echo htmlspecialchars($book['author']); ?></strong>
            </p>
            <p style="color:#3498db; margin-bottom:15px;">
                📂 <?php echo htmlspecialchars($book['category']); ?>
            </p>
            <p style="font-size:28px; color:#e74c3c; font-weight:bold; margin-bottom:15px;">
                Rs. <?php echo number_format($book['price'], 2); ?>
            </p>
            <p style="color:<?php echo $book['stock'] > 0 ? '#27ae60' : '#e74c3c'; ?>;
               font-weight:bold; margin-bottom:20px;">
                <?php echo $book['stock'] > 0 ? "✅ In Stock ({$book['stock']} available)" : "❌ Out of Stock"; ?>
            </p>
            <p style="color:#555; line-height:1.7; margin-bottom:25px;">
                <?php echo htmlspecialchars($book['description']); ?>
            </p>

            <?php if($book['stock'] > 0): ?>
            <form method="POST" action="" id="cartForm">
                <div style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
                    <div class="form-group" style="margin:0;">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="qty" value="1" min="1"
                               max="<?php echo $book['stock']; ?>"
                               style="width:80px; padding:10px; border:1px solid #ddd; border-radius:5px;">
                        <span class="error" id="qtyError"></span>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-success">
                        🛒 Add to Cart
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 BookStore. All rights reserved.</p>
</footer>

<script>
document.getElementById('cartForm') && 
document.getElementById('cartForm').addEventListener('submit', function(e){
    let qty = parseInt(document.getElementById('qty').value);
    let max = <?php echo $book['stock']; ?>;
    if(qty < 1 || qty > max){
        document.getElementById('qtyError').textContent = 'Enter quantity between 1 and ' + max;
        e.preventDefault();
    } else {
        document.getElementById('qtyError').textContent = '';
    }
});
</script>

</body>
</html>