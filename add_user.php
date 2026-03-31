<?php
$conn = mysqli_connect("localhost", "root", "Password", "barangay_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// GET FORM DATA
$first_name = $_POST['first_name'];
$last_name  = $_POST['last_name'];
$username   = $_POST['username'];
$password   = $_POST['password'];
$role       = $_POST['role'];
$status     = $_POST['status'] ?? 'active';

// HASH PASSWORD (IMPORTANT 🔐)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// CHECK IF USERNAME EXISTS
$check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
if (mysqli_num_rows($check) > 0) {
    echo "exists";
    exit();
}

// INSERT
$sql = "INSERT INTO users (first_name, last_name, username, password, role, status)
        VALUES ('$first_name', '$last_name', '$username', '$hashed_password', '$role', '$status')";

if (mysqli_query($conn, $sql)) {
    echo "success";
} else {
    echo "error";
}

mysqli_close($conn);
?>