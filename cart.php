<?php
session_start();
$host = "localhost"; $user = "root"; $password = ""; $database = "bookstore_db";
$conn = mysqli_connect($host, $user, $password, $database);

if(!isset($_SESSION['user_id'])){
    header("Location: login.php"); exit();
}

$success = ""; $error = "";

// Add to cart
if(isset($_POST['add']) && isset($_POST['book_id'])){
    $book_id = (int)$_POST['book_id'];
    $qty = (int)$_POST['quantity'];
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if(isset($_SESSION['cart'][$book_id])){
        $_SESSION['cart'][$book_id] += $qty;
    } else {
        $_SESSION['cart'][$book_id] = $qty;
    }
    $success = "Book added to cart!";
}

// Update quantity
if(isset($_POST['update'])){
    foreach($_POST['qty'] as $book_id => $qty){
        $book_id = (int)$book_id;
        $qty = (int)$qty;
        if($qty <= 0){
            unset($_SESSION['cart'][$book_id]);
        } else {
            $_SESSION['cart'][$book_id] = $qty;
        }
    }
    $success = "Cart updated!";
}

// Remove item
if(isset($_GET['remove'])){
    $remove_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    $success = "Item removed from cart!";
}

// Place order
if(isset($_POST['place_order'])){
    if(empty($_SESSION['cart'])){
        $error = "Your cart is empty!";
    } else {
        $user_id = $_SESSION['user_id'];
        $total = 0;

        // Calculate total
        foreach($_SESSION['cart'] as $book_id => $qty){
            $b = mysqli_fetch_assoc(mysqli_query($conn,"SELECT price FROM books WHERE id=$book_id"));
            if($b) $total += $b['price'] * $qty;
        }

        // Insert order
        mysqli_query($conn,"INSERT INTO orders (user_id, total) VALUES ($user_id, $total)");
        $order_id = mysqli_insert_id($conn);

        // Insert order items
        foreach($_SESSION['cart'] as $book_id => $qty){
            $b = mysqli_fetch_assoc(mysqli_query($conn,"SELECT price FROM books WHERE id=$book_id"));
            if($b){
                $price = $b['price'];
                mysqli_query($conn,"INSERT INTO order_items (order_id, book_id, quantity, price) 
                                    VALUES ($order_id, $book_id, $qty, $price)");
                // Update stock
                mysqli_query($conn,"UPDATE books SET stock = stock - $qty WHERE id=$book_id AND stock >= $qty");
            }
        }

        $_SESSION['cart'] = [];
        $success = "Order placed successfully! Your order ID is #$order_id";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - BookStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
    <div class="logo">📚 BookStore</div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="books.php">Books</a></li>
        <li><a href="cart.php">Cart 🛒</a></li>
        <li><a href="logout.php">Logout (<?php echo $_SESSION['user_name']; ?>)</a></li>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <li><a href="admin/index.php">Admin</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="container">
    <h2>🛒 My Cart</h2>

    <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if(empty($_SESSION['cart'])): ?>
        <div style="text-align:center; padding:60px; background:white; border-radius:10px;">
            <p style="font-size:22px; margin-bottom:20px;">🛒 Your cart is empty!</p>
            <a href="books.php" class="btn">Browse Books</a>
        </div>
    <?php else: ?>

    <form method="POST" action="" id="cartForm">
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $grand_total = 0;
            foreach($_SESSION['cart'] as $book_id => $qty):
                $book = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM books WHERE id=$book_id"));
                if(!$book) continue;
                $subtotal = $book['price'] * $qty;
                $grand_total += $subtotal;
            ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($book['title']); ?></strong>
                </td>
                <td><?php echo htmlspecialchars($book['author']); ?></td>
                <td>Rs. <?php echo number_format($book['price'], 2); ?></td>
                <td>
                    <input type="number" name="qty[<?php echo $book_id; ?>]"
                           value="<?php echo $qty; ?>" min="1"
                           max="<?php echo $book['stock']; ?>"
                           style="width:70px; padding:5px; border:1px solid #ddd; border-radius:5px;">
                </td>
                <td>Rs. <?php echo number_format($subtotal, 2); ?></td>
                <td>
                    <a href="cart.php?remove=<?php echo $book_id; ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Remove this item?')">Remove</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Total & Buttons -->
        <div style="background:white; padding:25px; border-radius:10px; margin-top:20px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                <div>
                    <h3 style="font-size:24px; color:#2c3e50;">
                        Grand Total: <span style="color:#e74c3c;">Rs. <?php echo number_format($grand_total, 2); ?></span>
                    </h3>
                    <p style="color:#666; font-size:14px;"><?php echo count($_SESSION['cart']); ?> item(s) in cart</p>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="submit" name="update" class="btn" style="background:#3498db;">
                        🔄 Update Cart
                    </button>
                    <a href="books.php" class="btn" style="background:#95a5a6;">
                        ← Continue Shopping
                    </a>
                    <button type="submit" name="place_order" class="btn btn-success"
                            onclick="return confirm('Place order for Rs. <?php echo number_format($grand_total, 2); ?>?')">
                        ✅ Place Order
                    </button>
                </div>
            </div>
        </div>
    </form>

    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 BookStore. All rights reserved.</p>
</footer>

<script>
// Validate quantities before update
document.getElementById('cartForm') && 
document.getElementById('cartForm').addEventListener('submit', function(e){
    if(e.submitter && e.submitter.name === 'update'){
        let inputs = document.querySelectorAll('input[type="number"]');
        let valid = true;
        inputs.forEach(function(input){
            if(parseInt(input.value) < 0){
                alert('Quantity cannot be negative!');
                valid = false;
            }
        });
        if(!valid) e.preventDefault();
    }
});
</script>

</body>
</html>