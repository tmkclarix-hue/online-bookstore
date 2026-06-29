<?php include 'config.php';

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Check if email exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    
    if(mysqli_num_rows($check) > 0){
        $error = "Email already registered!";
    } elseif($password != $confirm){
        $error = "Passwords do not match!";
    } else {
        $hashed = md5($password);
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed')";
        if(mysqli_query($conn, $sql)){
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - BookStore</title>
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
    <h2>📝 Create Account</h2>

    <?php if($error): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form id="registerForm" method="POST" action="">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" id="name" placeholder="Enter your name">
            <span class="error" id="nameError"></span>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" id="email" placeholder="Enter your email">
            <span class="error" id="emailError"></span>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" id="phone" placeholder="07X XXXXXXX">
            <span class="error" id="phoneError"></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" placeholder="Min 6 characters">
            <span class="error" id="passError"></span>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm" placeholder="Repeat password">
            <span class="error" id="confirmError"></span>
        </div>
        <div class="form-group">
            <label>Gender</label>
            <input type="radio" name="gender" value="male"> Male &nbsp;
            <input type="radio" name="gender" value="female"> Female
        </div>
        <div class="form-group">
            <label>Favourite Category</label>
            <select name="category">
                <option value="">-- Select --</option>
                <option value="Fiction">Fiction</option>
                <option value="Science">Science</option>
                <option value="History">History</option>
                <option value="Technology">Technology</option>
                <option value="Children">Children</option>
            </select>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="agree" id="agree"> 
                I agree to the Terms & Conditions
            </label>
            <span class="error" id="agreeError"></span>
        </div>
        <button type="submit" class="btn" style="width:100%">Register</button>
        <p style="text-align:center; margin-top:15px">Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e){
    let valid = true;

    // Name
    let name = document.getElementById('name').value.trim();
    if(name === ''){
        document.getElementById('nameError').textContent = 'Name is required!';
        valid = false;
    } else {
        document.getElementById('nameError').textContent = '';
    }

    // Email
    let email = document.getElementById('email').value.trim();
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(email)){
        document.getElementById('emailError').textContent = 'Enter a valid email!';
        valid = false;
    } else {
        document.getElementById('emailError').textContent = '';
    }

    // Phone
    let phone = document.getElementById('phone').value.trim();
    let phoneRegex = /^0[0-9]{9}$/;
    if(!phoneRegex.test(phone)){
        document.getElementById('phoneError').textContent = 'Enter valid phone number (10 digits)!';
        valid = false;
    } else {
        document.getElementById('phoneError').textContent = '';
    }

    // Password
    let pass = document.getElementById('password').value;
    if(pass.length < 6){
        document.getElementById('passError').textContent = 'Password must be at least 6 characters!';
        valid = false;
    } else {
        document.getElementById('passError').textContent = '';
    }

    // Confirm Password
    let confirm = document.getElementById('confirm').value;
    if(confirm !== pass){
        document.getElementById('confirmError').textContent = 'Passwords do not match!';
        valid = false;
    } else {
        document.getElementById('confirmError').textContent = '';
    }

    // Agree
    if(!document.getElementById('agree').checked){
        document.getElementById('agreeError').textContent = 'You must agree to the terms!';
        valid = false;
    } else {
        document.getElementById('agreeError').textContent = '';
    }

    if(!valid) e.preventDefault();
});
</script>

</body>
</html>