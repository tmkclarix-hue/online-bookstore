<?php include '../config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

$id = (int)$_GET['id'];
mysqli_query($conn, "DELETE FROM books WHERE id=$id");
header("Location: index.php");
exit();
?>