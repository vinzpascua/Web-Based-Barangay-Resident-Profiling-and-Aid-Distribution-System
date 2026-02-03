document.addEventListener("DOMContentLoaded", () => {
    const logoutBtn = document.getElementById('logoutBtn');
    const toast = document.getElementById('logout-toast');
    const confirmBtn = document.getElementById('confirm-logout');
    const cancelBtn = document.getElementById('cancel-logout');

    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("mainContent");
    const icon = document.getElementById("sidebarIcon");

    const navLeft = document.getElementById("navLeft");
    const navRight = document.getElementById("navRight");

    const sidebarContent = document.getElementById("sidebarContent");
    const navbar = document.querySelector(".navbar");

    // ---------- DEFAULT STATE ----------
    if (sidebar.classList.contains("collapsed")) {
        main.classList.remove("expanded");

        icon.classList.remove("fa-xmark");
        icon.classList.add("fa-bars");

        navbar.prepend(navLeft);
        navbar.appendChild(navRight);

    } else {
        main.classList.add("expanded");

        icon.classList.remove("fa-bars");
        icon.classList.add("fa-xmark");

        sidebarContent.appendChild(navLeft);
        sidebarContent.appendChild(navRight);
    }

    // ---------- TOGGLE SIDEBAR ----------
    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        sidebar.classList.toggle("expanded");

        if (sidebar.classList.contains("collapsed")) {
            main.classList.remove("expanded");

            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars");

            navbar.prepend(navLeft);
            navbar.appendChild(navRight);

        } else {
            main.classList.add("expanded");

            icon.classList.remove("fa-bars");
            icon.classList.add("fa-xmark");

            sidebarContent.appendChild(navLeft);
            sidebarContent.appendChild(navRight);
        }
    });

    // ---------- LOGOUT TOAST ----------
    logoutBtn.addEventListener('click', () => {
        toast.style.display = 'block';
    });

    confirmBtn.addEventListener('click', () => {
        window.location.href = "login.php";
    });

    cancelBtn.addEventListener('click', () => {
        toast.style.display = 'none';
    });
});
