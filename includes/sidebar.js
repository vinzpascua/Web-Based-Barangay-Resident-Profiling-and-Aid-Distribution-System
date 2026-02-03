document.addEventListener("DOMContentLoaded", () => {
    const logoutBtn = document.getElementById('logoutBtn');
    const toast = document.getElementById('logout-toast');
    const confirmBtn = document.getElementById('confirm-logout');
    const cancelBtn = document.getElementById('cancel-logout');

    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const icon = document.getElementById("sidebarIcon");

    const navLeft = document.getElementById("navLeft");
    const navRight = document.getElementById("navRight");
    const sidebarContent = document.getElementById("sidebarContent");

    // Default state
    if (sidebar.classList.contains("collapsed")) {
        sidebarContent.appendChild(navLeft);
        sidebarContent.appendChild(navRight);
    }

    // Toggle sidebar
    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        sidebar.classList.toggle("expanded");

        if (sidebar.classList.contains("collapsed")) {
            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars");
            sidebarContent.appendChild(navLeft);
            sidebarContent.appendChild(navRight);
        } else {
            icon.classList.remove("fa-bars");
            icon.classList.add("fa-xmark");
            sidebarContent.appendChild(navLeft);
            sidebarContent.appendChild(navRight);
        }
    });

    // Logout toast
    logoutBtn.addEventListener('click', () => toast.style.display = 'block');
    confirmBtn.addEventListener('click', () => window.location.href = "login.php");
    cancelBtn.addEventListener('click', () => toast.style.display = 'none');
});
