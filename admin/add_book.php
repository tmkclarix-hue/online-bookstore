<?php
session_start();
include '../config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title       = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
    $author      = mysqli_real_escape_string($conn, trim($_POST['author'] ?? ''));
    $price       = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $stock       = isset($_POST['stock']) ? (int)$_POST['stock'] : -1;
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $description = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));

    if(empty($title)){
        $error = "Book title is required!";
    } elseif(empty($author)){
        $error = "Author name is required!";
    } elseif($category_id <= 0){
        $error = "Please select a valid category!";
    } elseif($price <= 0){
        $error = "Price must be greater than 0!";
    } elseif($stock < 0){
        $error = "Stock quantity cannot be negative!";
    } elseif(empty($description)){
        $error = "Description is required!";
    } else {
        $sql = "INSERT INTO books (title, author, price, stock, category_id, description) 
                VALUES ('$title', '$author', $price, $stock, $category_id, '$description')";

        if(mysqli_query($conn, $sql)){
            $success = "Book added successfully!";
            $title = $author = $description = "";
            $price = $stock = 0;
            $category_id = 0;
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav>
    <div class="logo">📚 Admin Panel</div>
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="../index.php">View Site</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</nav>

<div class="form-container" style="max-width:650px;">
    <h2>➕ Add New Book</h2>

    <?php if(!empty($error)): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if(!empty($success)): ?>
        <div class="success">
            <?php echo $success; ?>
            <a href="index.php">Back to Dashboard</a>
        </div>
    <?php endif; ?>

    <form method="POST" action="" id="addBookForm">
        <div class="form-group">
            <label>Book Title <span style="color:red">*</span></label>
            <input type="text" name="title" id="title"
                   placeholder="Enter book title"
                   value="<?php echo htmlspecialchars($title ?? ''); ?>">
            <span class="error" id="titleError"></span>
        </div>

        <div class="form-group">
            <label>Author <span style="color:red">*</span></label>
            <input type="text" name="author" id="author"
                   placeholder="Enter author name"
                   value="<?php echo htmlspecialchars($author ?? ''); ?>">
            <span class="error" id="authorError"></span>
        </div>

        <div class="form-group">
            <label>Category <span style="color:red">*</span></label>
            <select name="category_id" id="category">
                <option value="">-- Select Category --</option>
                <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo (int)$cat['id']; ?>"
                    <?php echo (isset($category_id) && $category_id == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
                <?php endwhile; ?>
            </select>
            <span class="error" id="categoryError"></span>
        </div>

        <div class="form-group">
            <label>Price (Rs.) <span style="color:red">*</span></label>
            <input type="number" name="price" id="price"
                   placeholder="0.00" step="0.01" min="0.01"
                   value="<?php echo isset($price) && $price > 0 ? $price : ''; ?>">
            <span class="error" id="priceError"></span>
        </div>

        <div class="form-group">
            <label>Stock Quantity <span style="color:red">*</span></label>
            <input type="number" name="stock" id="stock"
                   placeholder="0" min="0"
                   value="<?php echo isset($stock) && $stock >= 0 ? $stock : ''; ?>">
            <span class="error" id="stockError"></span>
        </div>

        <div class="form-group">
            <label>Description <span style="color:red">*</span></label>
            <textarea name="description" id="description" rows="4"
                      placeholder="Enter book description..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
            <span class="error" id="descError"></span>
        </div>

        <button type="submit" class="btn btn-success" style="width:100%">➕ Add Book</button>
        <a href="index.php" class="btn" style="width:100%; text-align:center; margin-top:10px; display:block; background:#95a5a6;">Cancel</a>
    </form>
</div>

<script>
document.getElementById('addBookForm').addEventListener('submit', function(e){
    let valid = true;

    const clearError = (id) => document.getElementById(id).textContent = '';
    const setError   = (id, msg) => { document.getElementById(id).textContent = msg; valid = false; };

    const title = document.getElementById('title').value.trim();
    title === '' ? setError('titleError', 'Title is required!') : clearError('titleError');

    const author = document.getElementById('author').value.trim();
    author === '' ? setError('authorError', 'Author is required!') : clearError('authorError');

    const category = document.getElementById('category').value;
    category === '' ? setError('categoryError', 'Please select a category!') : clearError('categoryError');

    const price = parseFloat(document.getElementById('price').value);
    (isNaN(price) || price <= 0) ? setError('priceError', 'Enter a valid price!') : clearError('priceError');

    const stock = parseInt(document.getElementById('stock').value);
    (isNaN(stock) || stock < 0) ? setError('stockError', 'Enter valid stock (0 or more)!') : clearError('stockError');

    const desc = document.getElementById('description').value.trim();
    desc === '' ? setError('descError', 'Description is required!') : clearError('descError');

    if(!valid) e.preventDefault();
});
</script>

</body>
</html>