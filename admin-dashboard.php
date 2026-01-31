<?php
session_start();

// Connect to DB
$conn = mysqli_connect("localhost", "root", "Password", "barangay_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get total residents
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registered_resi");
$row = mysqli_fetch_assoc($result);
$total_residents = $row['total'];


$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registered_household");
$row = mysqli_fetch_assoc($result);
$total_households = $row['total'];

$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM aid_program WHERE status = 'Active'");
$row = mysqli_fetch_assoc($result);
$total_active_programs = $row['total'];

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/admin-dashboarder.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-left">
        <img src="assets/images/logos.png" alt="Barangay Logo">
        <div class="nav-text">
            <span>Barangay Abangan Norte</span>
            <p>Household Data Management System</p>
        </div>
    </div>

    <div class="nav-right">
        <img src="assets/images/profiles.png" alt="User">
        <span>Welcome, Admin</span>
        <button class="logout" id="logoutBtn">
        <i class="fa-solid fa-right-from-bracket"></i>
        Logout
    </button>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="dashboard">

    <!-- TOP STATS -->
    <section class="stats">
        <div class="stat-card-1">
            <h3>Total Residents</h3>
            <p><?php echo number_format($total_residents); ?></p>
        </div>
        <div class="stat-card-2">
            <h3>Total Households</h3>
            <p><?php echo number_format($total_households); ?></p>
        </div>
        <div class="stat-card-3">
            <h3>Active Programs</h3>
            <p><?php echo number_format($total_active_programs); ?></p>
        </div>
    </section>

    <!-- ACTION CARDS -->
    <section class="actions">
    <a href="resident-profiling.php" class="actions-card-1">
        Resident Profiling
        <p>Manage resident and household information</p>
        <img src="assets/images/resident-profiling.png" alt="resident">
    </a>

    <a href="household-management.php" class="actions-card-2">
        Household Management
        <p>Group Residents into households</p>
        <img src="assets/images/household.png" alt="household">
    </a>

    <a href="aid-program-setup.php" class="actions-card-3">
        Aid Program Setup
        <p>Manage aid distribution programs</p>
        <img src="assets/images/aid.png" alt="household">
    </a>

    <a href="rfid-tags-insurance.php" class="actions-card-4">
        RFID Tag Insurance
        <p>Assign RFID tags to households</p>
    </a>

    <a href="distribution-page.html" class="action-card">
        Distribution Page
        <p>RFID scanning during aid events</p>
    </a>

    <a href="reports-logs.html" class="action-card">
        Reports & Logs
        <p>Generate distribution reports</p>
    </a>

    <a href="admin-settings.html" class="action-card">
        Admin Settings
        <p>System configuration</p>
    </a>

    <a href="signup.php" class="action-card">
        Create Staff Account
        <p>Create new staff accounts</p>
    </a>
</section>

</main>

<div id="logout-toast" class="toast">
    <p>Are you sure you want to logout?</p>
    <div class="toast-buttons">
        <button id="confirm-logout">Yes</button>
        <button id="cancel-logout">No</button>
    </div>
</div>

<script>
const logoutBtn = document.getElementById('logoutBtn');
const toast = document.getElementById('logout-toast');
const confirmBtn = document.getElementById('confirm-logout');
const cancelBtn = document.getElementById('cancel-logout');

logoutBtn.addEventListener('click', () => {
    toast.style.display = 'block'; // show toast
});

confirmBtn.addEventListener('click', () => {
    window.location.href = "login.php"; // redirect
});

cancelBtn.addEventListener('click', () => {
    toast.style.display = 'none'; // hide toast
});
</script>

</body>
</html>
