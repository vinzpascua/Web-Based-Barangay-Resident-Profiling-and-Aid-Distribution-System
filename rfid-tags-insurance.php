<?php
require 'auth_check.php';

$backLink = "login.php"; // default fallback
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $backLink = "admin-dashboard.php";
    } elseif ($_SESSION['role'] === 'staff') {
        $backLink = "staff-dashboard.php";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RFID Tag Issuance</title>

    <link rel="stylesheet" href="assets/css/rfid-tags-insurance.css">
    <link rel="stylesheet" href="includes/sidebars.css">
    <link rel="stylesheet" href="fontawesome/fontawesome/css/all.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="rp-navbar">
    <!-- Sidebar Toggle -->
    <button class="toggle-sidebar" id="toggleBtn">
        <i class="fa-solid fa-bars" id="toggleIcon"></i>
    </button>

    <!-- Back Button -->
    <a href="<?php echo $backLink; ?>" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <!-- Navbar Content -->
    <div class="rp-navbar-content">
        <img src="assets/images/logos.png" alt="Barangay Logo">
        <div class="nav-text">
            <span class="page-title">Barangay Abangan Norte</span>
            <p>Household Data Management System</p>
        </div>
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

  <div class="rp-card">
    <?php
    $status = $_GET['status'] ?? 'Active';
    $search = $_GET['search'] ?? '';
    ?>
    <div class="rp-header">
        <div class="header-text">
            <h2>RFID Tags</h2>
            <p>Manage household RFID tags</p>

            <!-- TABS -->
            <div class="tabs-container">

                <a href="?status=Active&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" 
                   class="tab <?php echo (($status ?? 'Active') == 'Active') ? 'active' : ''; ?>">
                   Active
                </a>

                <a href="?status=Inactive&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" 
                   class="tab <?php echo (($status ?? 'Active') == 'Inactive') ? 'active' : ''; ?>">
                   Inactive
                </a>

                <a href="?status=all&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" 
                   class="tab <?php echo (($status ?? 'Active') == 'all') ? 'active' : ''; ?>">
                   All
                </a>

            </div>
        </div>

        <div class="rp-actions">
            <!-- SEARCH FORM -->
            <form method="GET" style="display:flex; gap:10px;">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search RFID..."
                    value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                >

                <!-- KEEP STATUS WHEN SEARCHING -->
                <input 
                    type="hidden" 
                    name="status" 
                    value="<?php echo htmlspecialchars($_GET['status'] ?? 'Active'); ?>"
                >
            </form>

            <!-- ADD BUTTON -->
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
                    <th>Toggle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = mysqli_connect("localhost", "root", "Password", "barangay_db");

                // Get current status and search query
                $status = $_GET['status'] ?? 'Active';
                $search = $_GET['search'] ?? '';

                // Base query
                $query = "SELECT rfid_id, rfid_number, household_number, head_of_family, date_issued, status 
                          FROM rfid_tags 
                          WHERE 1 ";

                // Filter by status if not "all"
                if ($status !== 'all') {
                    $status_safe = mysqli_real_escape_string($conn, $status);
                    $query .= " AND status = '$status_safe' ";
                }

                // Filter by search
                if (!empty($search)) {
                    $search_safe = mysqli_real_escape_string($conn, $search);
                    $query .= " AND (
                        rfid_number LIKE '%$search_safe%' OR
                        household_number LIKE '%$search_safe%' OR
                        head_of_family LIKE '%$search_safe%'
                    ) ";
                }

                // Sort by date issued
                $query .= " ORDER BY date_issued DESC";

                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr data-id="<?= $row['rfid_id'] ?>">
                    <td><?= htmlspecialchars($row['rfid_number']) ?></td>
                    <td><?= htmlspecialchars($row['household_number']) ?></td>
                    <td><?= htmlspecialchars($row['head_of_family']) ?></td>
                    <td><?= date("M d, Y", strtotime($row['date_issued'])) ?></td>
                    <td>
                        <span class="status <?= $row['status'] === 'Active' ? 'active' : 'inactive' ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] === 'Active'): ?>
                            <button class="deactivate-btn" data-id="<?= $row['rfid_id'] ?>">Deactivate</button>
                        <?php else: ?>
                            <button class="activate-btn" data-id="<?= $row['rfid_id'] ?>">Activate</button>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="edit" 
                            data-id="<?= $row['rfid_id'] ?>"
                            data-rfid="<?= htmlspecialchars($row['rfid_number']) ?>"
                            data-household="<?= htmlspecialchars($row['household_number']) ?>"
                            data-head="<?= htmlspecialchars($row['head_of_family']) ?>"
                        >Edit</button>

                        <button class="delete" data-id="<?= $row['rfid_id'] ?>">Delete</button>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="7" style="text-align:center;">
                        No RFID tags found
                    </td>
                </tr>
                <?php
                endif;

                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>

</div>

</main>

<div class="modal-overlay" id="modalOverlay"></div>

<!-- Add/Edit Resident Modal -->
<div class="resident-modal" id="residentModal">
    <div class="resident-modal-content">
        <div class="modal-header">
            <h3>Add / Edit Tag</h3>
            <span class="close-btn" id="closeModal">&times;</span>
        </div>

        <form id="addResidentForm">
            <input type="hidden" name="rfid_id" id="rfid_id">

            <div class="modal-body">
                <!-- RFID Number + Scan Button -->
                <div class="rfid-row">
        <label for="rfid_number">RFID Number</label>
        <div class="rfid-input-group">
            <input type="text" name="rfid_number" id="rfid_number" class="rfid-number" placeholder="RFID Number" required>
            <button type="button" class="rfid-btn" id="scanRfidBtn">
                <i class="fa-solid fa-id-card"></i>
            </button>
        </div>
    </div>

                <!-- Household Number -->
                <div class="modal-row">
                    <label for="household_number">Household Number</label>
                    <input type="text" name="household_number" id="household_number" placeholder="Household Number">
                </div>

                <!-- Head of Family -->
                <div class="modal-row">
                    <label for="head_of_family">Head Of Family</label>
                    <input type="text" name="head_of_family" id="head_of_family" placeholder="Head Of Family" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="save" id="submitBtn">Issue Tag</button>
            </div>
        </form>

        <!-- RFID overlay -->
        <div class="rfid-overlay" id="rfidOverlay">
            <div class="rfid-box">
                <i class="fa-solid fa-id-card"></i>
                <h3>Waiting for RFID Scan</h3>
                <p>Please tap the RFID card</p>
                <button id="cancelRfid">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Popup -->
<link rel="stylesheet" href="assets/popup/popup.css">

<div id="popup-container"></div>

<script>
fetch("assets/popup/popup.html")
    .then(res => res.text())
    .then(html => {
        document.getElementById("popup-container").innerHTML = html;
    });
</script>

<script src="assets/popup/popup.js" defer></script>

<script src="assets/js/rfid-tagss.js"></script>
<script src="includes/sidebarss.js" defer></script><?php include 'includes/sidebar.php'; ?>

</body>
</html>
