<?php
session_start();

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
    <title>Household Management</title>

    <link rel="stylesheet" href="assets/css/households-management.css">
    <link rel="stylesheet" href="fontawesome/fontawesome/css/all.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>
<link rel="stylesheet" href="includes/sidebar.css">
<script src="includes/sidebar.js"></script>

<!-- NAVBAR -->
<nav class="rp-navbar">
    <a href="<?php echo $backLink; ?>" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <img src="assets/images/logos.png" alt="Barangay Logo">

    <div class="nav-text">
        <span class="page-title">Barangay Abangan Norte</span>
        <p>Household Data Management System</p>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="rp-dashboard">
    <div class="rp-card">

        <!-- HEADER -->
        <div class="rp-header">
            <div class="header-text">
                <h2>Registered Households</h2>
                <p>Manage household groups and their members</p>
            </div>

            <div class="rp-actions">
                <form method="GET" style="display:inline;"> 
                    <input type="text" name="search" placeholder="Search households..." value="<?php echo $_GET['search'] ?? ''; ?>"> 
                </form>
                <button class="add-household">
                    <i class="fa-solid fa-plus"></i> Add Household
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="rp-table">
            <table>
                <thead>
                    <tr>
                        <th>Household No.</th>
                        <th>Head of Family</th>
                        <th>Address</th>
                        <th>Members</th>
                        <th>RFID No.</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Connect to database
                $conn = mysqli_connect("localhost", "root", "Password", "barangay_db");
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Search
                $search = $_GET['search'] ?? '';
                if (!empty($search)) {
                    $search = mysqli_real_escape_string($conn, $search);
                    $sql = "SELECT * FROM registered_household
                            WHERE household_number LIKE '%$search%'
                            OR head_of_family LIKE '%$search%'
                            OR address LIKE '%$search%'
                            OR household_members LIKE '%$search%'
                            ORDER BY id DESC";
                } else {
                    $sql = "SELECT * FROM registered_household ORDER BY id DESC";
                }

                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {

                        $membersArray = array_filter(array_map('trim', explode(',', $row['household_members'])));
                        $membersCount = count($membersArray);

                        $membersData = htmlspecialchars($row['household_members'], ENT_QUOTES);

                        // display text
                        $displayMembers = $membersCount . " members";

                        echo "
                        <tr>
                            <td>{$row['household_number']}</td>
                            <td>{$row['head_of_family']}</td>
                            <td>{$row['address']}</td>
                            <td>
                                <span class='member-count' data-members='{$membersData}'>
                                    {$displayMembers}
                                </span>
                            </td>
                            <td>{$row['rfid']}</td>
                            <td>
                                <button class='edit'
                                    data-id='{$row['id']}'
                                    data-number='{$row['household_number']}'
                                    data-head='{$row['head_of_family']}'
                                    data-address='{$row['address']}'
                                    data-members='{$membersData}'
                                    data-rfid='{$row['rfid']}'>
                                    <i class='fa-solid fa-pen-to-square'></i>
                                </button>
                                <button class='delete' data-id='{$row['id']}'>
                                    <i class='fa-solid fa-trash'></i>
                                </button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No households found.</td></tr>";
                }

                mysqli_close($conn);
                ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<!-- MODAL -->
<div class="modal-overlay" id="modalOverlay"></div>

<div class="resident-modal" id="residentModal">
    <div class="resident-modal-content">

        <!-- HEADER -->
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa-solid fa-house" id="modalIcon"></i>
                <h3 id="modalTitle">Add New Household</h3>
            </div>
            <span class="close-btn" id="closeModal">&times;</span>
        </div>

        <!-- BODY -->
        <form id="addResidentForm">
            <input type="hidden" id="resident_id" name="resident_id">

            <!-- ROW 1 -->
            <div class="form-row two-col">
                <div class="form-group">
                    <label>Household Number</label>
                    <input type="text" name="household_number" readonly>
                </div>

                <div class="form-group head-picker">
                    <label>Head of Family</label>
                    <input type="text" name="head_of_family" id="headInput" autocomplete="off" required>

                    <!-- MINI TABLE OVERLAY -->
                    <div class="resident-picker" id="residentPicker">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = mysqli_connect("localhost", "root", "Password", "barangay_db");
                                $res = mysqli_query($conn, "
                                    SELECT first_name, middle_name, last_name, address
                                    FROM registered_resi
                                    ORDER BY last_name
                                ");

                                while ($r = mysqli_fetch_assoc($res)) {
                                    $fullName = trim(
                                        $r['first_name'] . ' ' .
                                        $r['middle_name'] . ' ' .
                                        $r['last_name']
                                    );
                                    $address = htmlspecialchars($r['address']);

                                    echo "
                                    <tr>
                                        <td>{$fullName}</td>
                                        <td>{$address}</td>
                                        <td>
                                            <button type='button'
                                                class='select-resident'
                                                data-name=\"{$fullName}\"
                                                data-address=\"{$address}\">
                                                Select
                                            </button>
                                        </td>
                                    </tr>";
                                }
                                mysqli_close($conn);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ROW 2 -->
            <div class="form-row">
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" required>
                </div>
            </div>

            <!-- ROW 3 -->
            <div class="form-group members-picker">
                <label>Household Members</label>
                <input type="text" name="household_members" id="membersInput" 
                    placeholder="Juan Dela Cruz, Maria Dela Cruz" >

                <small>Click Add to include members</small>

                <div class="members-table" id="membersTable">
                    <table>
                        <thead>
                            <tr class="search-row">
                                <th colspan="3">
                                    <input type="text" id="memberSearch" placeholder="Search resident...">
                                </th>
                            </tr>
                            <tr class="header-row">
                                <th>Name</th>
                                <th>Address</th>
                                <th style="width:90px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $conn = mysqli_connect("localhost", "root", "Password", "barangay_db");
                                $res = mysqli_query($conn, "
                                    SELECT first_name, middle_name, last_name, address
                                    FROM registered_resi
                                    ORDER BY last_name
                                ");

                                while ($r = mysqli_fetch_assoc($res)) {
                                    $fullName = trim(
                                        $r['first_name'] . ' ' .
                                        $r['middle_name'] . ' ' .
                                        $r['last_name']
                                    );
                                    $address = htmlspecialchars($r['address']);

                                    echo "
                                    <tr>
                                        <td>{$fullName}</td>
                                        <td>{$address}</td>
                                        <td>
                                            <button type='button'
                                                class='add-member'
                                                data-name=\"{$fullName}\">
                                                Add
                                            </button>
                                        </td>
                                    </tr>";
                                }
                                mysqli_close($conn);
                                ?>
                        </tbody>
                    </table>
                </div>
            </div>



            <!-- ROW 4 -->
            <div class="form-row">
                <div class="form-group">
                    <label>Assigned RFID</label>
                    <input type="text" name="rfid" required placeholder="Enter RFID Number">
                </div>
            </div>

            <button type="submit" id="saveHouseholdBtn">Save Household</button>
        </form>
    </div>
</div>


<div id="members-toast" class="toast">
    <p id="members-text"></p>
    <button id="close-toast">&times;</button>
</div>

<script src="assets/js/household-management.js"></script>
</body>
</html>
