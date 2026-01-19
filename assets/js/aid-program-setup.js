document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("residentModal");
    const overlay = document.getElementById("modalOverlay");
    const closeBtn = document.querySelector(".close-btn");
    const form = document.getElementById("addResidentForm");
    const programId = document.getElementById("program_id");

    // Open modal for adding new program
    const addBtn = document.querySelector(".add-resident");
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            form.reset();
            programId.value = "";
            form.querySelector("button").innerText = "Save Program";
            modal.classList.add("show");
            overlay.classList.add("show");
        });
    }

    // Close modal
    function closeModal() {
        modal.classList.remove("show");
        overlay.classList.remove("show");
    }
    closeBtn.addEventListener("click", closeModal);
    overlay.addEventListener("click", closeModal);

    // Open modal for editing
    document.addEventListener("click", function (e) {
        const editBtn = e.target.closest(".edit");
        if (!editBtn) return;

        programId.value = editBtn.dataset.id || "";
        form.program_name.value = editBtn.dataset.name || "";
        form.aid_type.value = editBtn.dataset.type || "";
        form.date_scheduled.value = editBtn.dataset.date || "";
        form.beneficiaries.value = editBtn.dataset.beneficiaries || "";
        form.status.value = editBtn.dataset.status || "";

        form.querySelector("button").innerText = "Update Program";

        modal.classList.add("show");
        overlay.classList.add("show");
    });

    // Submit form (Add or Update)
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch("add_aid_program.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            data = data.trim();
            if (data === "success") {
                // Close modal and reload table
                closeModal();
                location.reload(); // or update table dynamically without reload
            } else {
                alert("Error: " + data);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Server error");
        });
    });


    // DELETE FUNCTION
    document.addEventListener("click", function(e) {
        const deleteBtn = e.target.closest(".delete");
        if (!deleteBtn) return;

        const id = deleteBtn.dataset.id;
        if (!confirm("Are you sure you want to delete this record?")) return;

        fetch("delete_aid_program.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + encodeURIComponent(id)
        })
        .then(res => res.text())
        .then(data => {
            data = data.trim();
            if (data === "success") {
                // Remove the row from table
                const row = deleteBtn.closest("tr");
                row.parentNode.removeChild(row);
            } else {
                alert("Failed to delete: " + data);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Server error");
        });
    });

});
