<?php
require 'auth_check.php';

$backLink = "login.php"; // default fallback if no login session
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
    <title>Account Management</title>
    <link rel="stylesheet" href="assets/css/account-man.css">
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
    <?php
    $status = strtolower($_GET['status'] ?? 'active');
    $search = $_GET['search'] ?? '';
    ?>

    <div class="rp-card">

        <!-- UPPER PART -->
        <div class="rp-header">
            <div class="header-text">
                <h2>User Account</h2>
                <p>Create and manage user accounts</p>

                <div class="tabs-container">
                    

                    <a href="?status=active&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" 
                    class="tab <?php echo ($status == 'active') ? 'active' : ''; ?>">
                    Active
                    </a>

                    <a href="?status=inactive&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" 
                    class="tab <?php echo ($status == 'inactive') ? 'active' : ''; ?>">
                    Inactive
                    </a>

                    <a href="?status=all&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" 
                    class="tab <?php echo ($status == 'all') ? 'active' : ''; ?>">
                    All
                    </a>
                </div>
            </div>
            
            <div class="rp-actions">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    placeholder="Search users..."
                    value="<?php echo $_GET['search'] ?? ''; ?>">

                <button class="add-resident" id="openModalBtn">
                    <i class="fa-solid fa-plus"></i> Add Account
                </button>
            </div>
        </div>

        <!-- LOWER PART: TABLE -->
        <div class="rp-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

               <tbody id="residentTableBody">

                <?php
                $conn = mysqli_connect("localhost", "root", "Password", "barangay_db");

                $search = $_GET['search'] ?? '';
                $status = strtolower($_GET['status'] ?? 'active');

                $limit = 8;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
                $offset = ($page - 1) * $limit;

                // =====================
                // WHERE CONDITIONS
                // =====================
                $conditions = [];

                if ($search !== '') {
                    $search_safe = mysqli_real_escape_string($conn, $search);

                    $conditions[] = "(first_name LIKE '%$search_safe%'
                                    OR last_name LIKE '%$search_safe%'
                                    OR username LIKE '%$search_safe%'
                                    OR role LIKE '%$search_safe%')";
                }

                if ($status !== 'all') {
                    $status_safe = mysqli_real_escape_string($conn, strtolower($status));
                    $conditions[] = "status = '$status_safe'";
                }

                $whereSQL = '';
                if (!empty($conditions)) {
                    $whereSQL = "WHERE " . implode(" AND ", $conditions);
                }

                // =====================
                // COUNT QUERY
                // =====================
                $count_sql = "SELECT COUNT(*) as total FROM users $whereSQL";
                $count_result = mysqli_query($conn, $count_sql);
                $total_records = mysqli_fetch_assoc($count_result)['total'];
                $total_pages = ceil($total_records / $limit);

                // =====================
                // MAIN QUERY
                // =====================
                $sql = "
                    SELECT * FROM users
                    $whereSQL
                    ORDER BY id DESC
                    LIMIT $limit OFFSET $offset
                ";

                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($result)) {

                    $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                    $username = htmlspecialchars($row['username']);
                    $role = htmlspecialchars($row['role']);
                    $statusRow = $row['status'] ?? 'active';

                    $statusBadge = ($statusRow === 'active')
                        ? "<span class='badge active'>Active</span>"
                        : "<span class='badge inactive'>Inactive</span>";

                    echo "<tr>
                        <td>{$fullName}</td>
                        <td>{$username}</td>
                        <td>{$role}</td>
                        <td>{$statusBadge}</td>
                        <td>";

                    // Toggle button
                    if ($statusRow === 'active') {
                        echo "<button class='deactivate' data-id='{$row['id']}'>
                                Deactivate
                            </button>";
                    } else {
                        echo "<button class='activate' data-id='{$row['id']}'>
                                Activate
                            </button>";
                    }

                    echo "</td>
                    </tr>";
                }
                ?>

                <tr id="noResultRow" style="display:none;">
                    <td colspan="5" style="text-align:center; padding:20px; color:#777;">
                        No matching users found
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <?php if ($total_records >= 8): ?>
        <div class="pagination">

            <?php
            $max_pages_to_show = 5;
            $start = max(1, $page - 2);
            $end = min($total_pages, $start + $max_pages_to_show - 1);

            if ($end - $start < $max_pages_to_show - 1) {
                $start = max(1, $end - $max_pages_to_show + 1);
            }
            ?>

            <?php if ($page > 1): ?>
                <a href="?search=<?= $search ?>&page=<?= $page - 1 ?>">&lt;</a>
            <?php else: ?>
                <span class="disabled">&lt;</span>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?search=<?= $search ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?search=<?= $search ?>&page=<?= $page + 1 ?>">&gt;</a>
            <?php else: ?>
                <span class="disabled">&gt;</span>
            <?php endif; ?>

        </div>
        <?php endif; ?>

    </div>

</main>

<!-- MODAL OVERLAY -->
<div class="modal-overlay" id="modalOverlay"></div>

<!-- ADD STAFF MODAL -->
<div class="resident-modal" id="residentModal">
    <div class="resident-modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa-solid fa-user"></i>
                <h3>Add Account</h3>
            </div>
            <span class="close-btn" id="closeModal">&times;</span>
        </div>

        <form id="addStaffForm" class="resident-form-grid">
            
            <div class="form-row three">
                <div class="form-field">
                    <label>First Name</label>
                    <input type="text" name="first_name" required>
                </div>

                <div class="form-field">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required>
                </div>

                <div class="form-field">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
            </div>

            <div class="form-row three">
                <div class="form-field">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-field">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="form-field">
                    <label>Status</label>
                    <select name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit">Save Account</button>
        </form>
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


<script src="assets/js/account-man.js"></script>
<script src="includes/sidebarss.js" defer></script><?php include 'includes/sidebar.php'; ?>

</body>
</html>
