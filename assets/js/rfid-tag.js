document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.querySelector(".add-tag");
    const modal = document.getElementById("residentModal");
    const overlay = document.getElementById("modalOverlay");
    const closeBtn = document.getElementById("closeModal");
    const form = document.getElementById("addResidentForm");

    if (!openBtn || !modal || !overlay || !closeBtn) {
        console.error("Modal elements missing");
        return;
    }

    // OPEN MODAL
    openBtn.addEventListener("click", () => {
        form.reset();
        modal.classList.add("show");
        overlay.classList.add("show");
    });

    // CLOSE MODAL (X button)
    closeBtn.addEventListener("click", closeModal);

    // CLOSE MODAL (overlay click)
    overlay.addEventListener("click", closeModal);

    function closeModal() {
        modal.classList.remove("show");
        overlay.classList.remove("show");
    }
});
