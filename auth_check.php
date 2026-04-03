<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "Password", "barangay_db");

$userId = $_SESSION['user_id'];


$query = "SELECT first_name, last_name, username FROM users WHERE id = $userId";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$firstName = trim($user['first_name'] ?? '');
$lastName  = trim($user['last_name'] ?? '');
$username  = trim($user['username'] ?? '');

if ($firstName || $lastName) {
    $currentName =$firstName ;
} elseif ($username) {
    $currentName = $username;
} else {
    $currentName = 'Admin';
}

mysqli_close($conn);
?>