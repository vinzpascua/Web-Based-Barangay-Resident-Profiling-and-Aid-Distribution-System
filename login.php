<?php
session_start();

$servername = "localhost";
$dbusername = "root";
$dbpassword = "Password"; // 🔴 Replace with your XAMPP root password
$dbname = "barangay_db";

$conn = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === "" || $password === "") {
        $error = "Please fill in all fields.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // hashed password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: dashboard.html"); // Redirect after login
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Account not found.";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Web-Based-Barangay-Resident-Profiling-and-Aid-Distribution-System</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="auth-body">

<div class="auth-box">
    <img src="assets/images/logo.png" alt="logo">
    <h2>Barangay Abangan Norte</h2>
    <p class="Household">Household Data Management System</p>

    <form method="POST" action="">
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter your username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" name="login">Sign in</button>
    </form>

    <?php if (!empty($error)) { ?>
        <p style="color:red; text-align:center; margin-top:10px;">
            <?php echo $error; ?>
        </p>
    <?php } ?>

    <p class="authorized-p">Authorized Personnel Only</p>
    <a href="signup.php" class="Register">Register New Account</a>
</div>

</body>
</html>
