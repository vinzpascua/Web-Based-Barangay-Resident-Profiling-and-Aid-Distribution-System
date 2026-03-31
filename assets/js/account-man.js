// ===============================
// TOGGLE USER STATUS
// ===============================
document.addEventListener("click", function (e) {
    if (e.target.closest(".deactivate") || e.target.closest(".activate")) {

        let btn = e.target.closest("button");
        let id = btn.getAttribute("data-id");

        fetch("toggle_user_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + id
        })
        .then(res => res.text())
        .then(() => {
            location.reload();
        });
    }
});


// ===============================
// MODAL + FORM FUNCTIONALITY
// ===============================
const openBtn = document.getElementById("openModalBtn");
const modal = document.getElementById("residentModal");
const overlay = document.getElementById("modalOverlay");
const closeBtn = document.getElementById("closeModal");
const form = document.getElementById("addStaffForm");

// OPEN MODAL
if (openBtn && modal && overlay && form) {
    openBtn.addEventListener("click", () => {
        form.reset();
        modal.classList.add("show");
        overlay.classList.add("show");
    });
}

// CLOSE MODAL
if (closeBtn) {
    closeBtn.addEventListener("click", closeModal);
}

if (overlay) {
    overlay.addEventListener("click", closeModal);
}

// ESC KEY CLOSE
document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") {
        closeModal();
    }
});

function closeModal() {
    if (modal && overlay) {
        modal.classList.remove("show");
        overlay.classList.remove("show");
    }
}


// ===============================
// SUBMIT FORM (SAVE TO DATABASE)
// ===============================
if (form) {
    form.addEventListener("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(form);

        fetch("add_user.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(response => {

            if (response === "success") {
                alert("User added successfully!");
                closeModal();
                location.reload();
            } 
            else if (response === "exists") {
                alert("Username already exists!");
            } 
            else {
                alert("Error saving user.");
                console.log(response);
            }

        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
}