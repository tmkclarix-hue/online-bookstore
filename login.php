<?php include 'config.php';

if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'admin'){
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - BookStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
    <div class="logo">📚 BookStore</div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="books.php">Books</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    </ul>
</nav>

<div class="form-container">
    <h2>🔐 Login</h2>

    <?php if($error): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" id="email" placeholder="Enter your email">
            <span class="error" id="emailError"></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password">
            <span class="error" id="passError"></span>
        </div>
        <button type="submit" class="btn" style="width:100%">Login</button>
        <p style="text-align:center; margin-top:15px">No account? <a href="register.php">Register here</a></p>
    </form>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e){
    let valid = true;

    let email = document.getElementById('email').value.trim();
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(email)){
        document.getElementById('emailError').textContent = 'Enter a valid email!';
        valid = false;
    } else {
        document.getElementById('emailError').textContent = '';
    }

    let pass = document.getElementById('password').value;
    if(pass === ''){
        document.getElementById('passError').textContent = 'Password is required!';
        valid = false;
    } else {
        document.getElementById('passError').textContent = '';
    }

    if(!valid) e.preventDefault();
});
</script>

</body>
</html>