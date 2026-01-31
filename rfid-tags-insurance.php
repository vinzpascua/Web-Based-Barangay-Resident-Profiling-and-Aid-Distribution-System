<?php
session_start();

$backLink = "login.php"; // default fallback
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $backLink = "admin-dashboard.php";
    } elseif ($_SESSION['role'] === 'staff') {
        $backLink = "staff-dashboard.html";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RFID Tag Insurance</title>

    <link rel="stylesheet" href="assets/css/rfid-tags-insurance.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body>

<!-- NAVBAR -->
<nav class="rp-navbar">
    <a href="<?php echo $backLink; ?>" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <img src="assets/images/logos.png" alt="Barangay Logo">

    <div class="nav-text">
        <span class="page-title">Baragay Abanangan Norte</span>
        <p>Household Data Management System</p>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="rp-dashboard">

    <!-- SUMMARY CARDS -->
    <div class="rfid-summary">

        <div class="rfid-summary-card-1">
            <div>
                <p>Total Tags Issued</p>
                <h3>120</h3>

            </div>
        </div>

        <div class="rfid-summary-card-2">
            <div>
                <p>Active Tags</p>
                <h3>95</h3>
                
            </div>
        </div>

        <div class="rfid-summary-card-3">
            <div>
                <p>Inactive Tags</p>
                <h3>25</h3>
                
            </div>
        </div>

    </div>

    <!-- TABLE CARD -->
    <div class="rp-card">

        <div class="rp-header">
            <div class="header-text">
                <h2>RFID Tags</h2>
                <p>Manage household RFID tags</p>
            </div>

            <div class="rp-actions">
                <input type="text" placeholder="Search RFID...">
                <button class="add-tag">
                    <i class="fa-solid fa-plus"></i>
                    Issue RFID Tag
                </button>
            </div>
        </div>

        <div class="rp-table">
            <table>
                <thead>
                    <tr>
                        <th>RFID Number</th>
                        <th>Household No.</th>
                        <th>Head Of Family</th>
                        <th>Date Issued</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>

    </div>

</main>

<div class="modal-overlay" id="modalOverlay"></div>

<!-- Add/Edit Resident Modal -->
<div class="resident-modal" id="residentModal">
    <div class="resident-modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h3>Add / Edit Tag</h3>
        <form id="addResidentForm">
            <input type="hidden" name="resident_id" id="resident_id">

            <label>RFID Number</label>
            <input type="text" name="rfid_number" placeholder="RFID Number" required>

            <label>Household Number</label>
            <input type="text" name="household_number" placeholder="Household Number">

            <label>Head Of Family</label>
            <input type="text" name="head_of_family" placeholder="head_of_family" required>

            <button type="submit">Issue Tag</button>
        </form>
    </div>
</div>

<script src="assets/js/rfid-tag.js"></script>

</body>
</html>
