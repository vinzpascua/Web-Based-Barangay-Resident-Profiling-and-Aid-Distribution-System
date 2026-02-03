

<div class="sidebar collapsed" id="sidebar">
    <button class="toggle-btn" id="toggleSidebar">
        <i class="fa-solid fa-bars" id="sidebarIcon"></i>
    </button>

    <div class="sidebar-content" id="sidebarContent">
        <!-- Top: nav-left -->
        <div class="nav-left" id="navLeft">
            <img src="assets/images/logos.png" alt="Barangay Logo">
            <div class="nav-text">
                <span>Barangay Abangan Norte</span>
                <p>Household Data Management</p>
            </div>
        </div>
    </div>

    <!-- Middle: menu -->
    <ul class="sidebar-menu">
        <li><a href="admin-dashboard.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a></li>
        <li><a href="resident-profiling.php"><i class="fa-solid fa-users"></i> <span>Residents</span></a></li>
        <li><a href="household-management.php"><i class="fa-solid fa-house"></i> <span>Households</span></a></li>
        <li><a href="aid-program-setup.php"><i class="fa-solid fa-hand-holding-heart"></i> <span>Aid Programs</span></a></li>
        <li><a href="reports-logs.html"><i class="fa-solid fa-file-lines"></i> <span>Reports</span></a></li>
    </ul>

    <!-- Bottom: nav-right/logout -->
    <div class="nav-right" id="navRight">
        <button class="logout" id="logoutBtn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </button>
    </div>
</div>

<!-- Logout toast -->
<div id="logout-toast" class="toast">
    <p>Are you sure you want to logout?</p>
    <div class="toast-buttons">
        <button id="confirm-logout">Yes</button>
        <button id="cancel-logout">No</button>
    </div>
</div>

