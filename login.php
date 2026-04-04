<?php
session_start();

// --- DATABASE CONNECTION ---
$servername = "localhost";
$dbusername = "root";
$dbpassword = "Password"; // your XAMPP password
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

        // Get user with role
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, username, password, role FROM users WHERE username = ?"
        );
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {

                // Save session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Role-based redirect
                $role = strtolower($user['role']);

                if ($role === 'admin') {
                    header("Location: admin-dashboard.php");
                } elseif ($role === 'staff') {
                    header("Location: staff-dashboard.php");
                } else {
                    header("Location: dashboard.html");
                }
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
<title>Barangay Login</title>
<link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

<div class="bg"></div>

<div class="login-card">

    <!-- LEFT GLASS PANEL -->
    <div class="left-panel">
        <img src="assets/images/logos.png" class="logo" alt="logo">

        <h1>Barangay Abangan Norte</h1>
        <p id="tagline"></p>
    </div>

    <!-- RIGHT FORM PANEL -->
    <div class="right-panel">

        <h2>Sign In</h2>
        <p class="subtitle">Please login to your account</p>

        <form method="POST" action="">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Sign In</button>
        </form>

        <?php if (!empty($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <p class="authorized">Authorized Personnel Only</p>

    </div>

</div>

<script src="assets/js/login.js"></script>

</body>
</html>