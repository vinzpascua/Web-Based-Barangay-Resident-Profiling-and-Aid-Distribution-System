document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.querySelector(".add-tag");
    const modal = document.getElementById("residentModal");
    const overlay = document.getElementById("modalOverlay");
    const closeBtn = document.getElementById("closeModal");
    const form = document.getElementById("addResidentForm");

    const rfidId = document.getElementById("rfid_id");
    const rfidNumber = document.getElementById("rfid_number");
    const householdNumber = document.getElementById("household_number");
    const headOfFamily = document.getElementById("head_of_family");

    if (!openBtn || !modal || !overlay || !closeBtn || !form || !rfidId) {
        console.error("Modal elements missing");
        return;
    }

    // OPEN MODAL FOR ADD
    openBtn.addEventListener("click", () => {
        form.reset();
        rfidId.value = ""; // clear hidden id
        form.querySelector("button").innerText = "Issue Tag";

        modal.classList.add("show");
        overlay.classList.add("show");
    });

    // CLOSE MODAL
    closeBtn.addEventListener("click", closeModal);
    overlay.addEventListener("click", closeModal);
    function closeModal() {
        modal.classList.remove("show");
        overlay.classList.remove("show");
    }

    // SUBMIT FORM (Add or Update)
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch("add_rfid_tags.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            data = data.trim();

            if (data === "success") {
                alert("RFID Tag saved successfully!");
                closeModal();
                form.reset();
                location.reload(); // refresh table to show new/updated data
            } else if (data === "rfid_exists") {
                alert("RFID already exists!");
            } else if (data === "missing") {
                alert("Please fill in required fields");
            } else {
                console.log(data);
                alert("Failed to save RFID tag");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Server error");
        });
    });

    // EDIT BUTTON
    document.addEventListener("click", function (e) {
        const editBtn = e.target.closest(".edit");
        if (!editBtn) return;

        rfidId.value = editBtn.dataset.id || "";
        rfidNumber.value = editBtn.dataset.rfid || "";
        householdNumber.value = editBtn.dataset.household || "";
        headOfFamily.value = editBtn.dataset.head || "";

        form.querySelector("button").innerText = "Update Tag";

        modal.classList.add("show");
        overlay.classList.add("show");
    });

    // DELETE BUTTON
    document.addEventListener("click", function (e) {
        const deleteBtn = e.target.closest(".delete");
        if (!deleteBtn) return;

        const rfidId = deleteBtn.dataset.id;
        if (!rfidId) return;

        if (!confirm("Are you sure you want to delete this RFID tag?")) return;

        fetch("delete_rfid_tag.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `rfid_id=${encodeURIComponent(rfidId)}`
        })
        .then(res => res.text())
        .then(data => {
            data = data.trim();
            if (data === "success") {
                // Remove the table row
                const row = deleteBtn.closest("tr");
                if (row) row.remove();
                alert("RFID tag deleted successfully!");
            } else {
                alert("Failed to delete RFID tag: " + data);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Server error");
        });
    });

    // ACTIVATE / DEACTIVATE BUTTONS
    document.addEventListener("click", function(e) {
        const activateBtn = e.target.closest(".activate-btn");
        const deactivateBtn = e.target.closest(".deactivate-btn");

        const btn = activateBtn || deactivateBtn;
        if (!btn) return;

        const rfidId = btn.dataset.id;
        if (!rfidId) return;

        const action = activateBtn ? "Active" : "Inactive";

        fetch("toggle_rfid_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `rfid_id=${encodeURIComponent(rfidId)}&status=${encodeURIComponent(action)}`
        })
        .then(res => res.text())
        .then(data => {
            data = data.trim();
            if (data === "success") {
                // Update the table row dynamically
                const row = btn.closest("tr");
                const statusSpan = row.querySelector(".status");
                const toggleCell = btn.closest("td");

                if (statusSpan) statusSpan.textContent = action;
                if (statusSpan) statusSpan.className = "status " + (action === "Active" ? "active" : "inactive");

                // Switch the button
                if (activateBtn) {
                    toggleCell.innerHTML = `<button class="deactivate-btn" data-id="${rfidId}">Deactivate</button>`;
                } else {
                    toggleCell.innerHTML = `<button class="activate-btn" data-id="${rfidId}">Activate</button>`;
                }

            } else {
                alert("Failed to update status: " + data);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Server error");
        });
    });

});
