document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("mainContent");
    const icon = document.getElementById("sidebarIcon");

    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("expanded");
        sidebar.classList.toggle("collapsed");
        main.classList.toggle("expanded");

        icon.classList.toggle("fa-bars");
        icon.classList.toggle("fa-xmark");
    });

    /* ---------- LOGOUT CONFIRM ---------- */
    logoutBtn.addEventListener("click", () => {
        Popup.open({
            title: "Confirm Logout",
            message: "Are you sure you want to logout?",
            type: "warning",
            onOk: () => {
                window.location.href = "login.php";
            }
        });
    });
});
