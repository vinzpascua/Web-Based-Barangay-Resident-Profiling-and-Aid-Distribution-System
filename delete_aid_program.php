<?php
session_start();
$conn = mysqli_connect("localhost", "root", "Password", "barangay_db");
if(!$conn) { die("Connection failed: " . mysqli_connect_error()); }

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM aid_program WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if(mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        echo mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request";
}

mysqli_close($conn);
?>
