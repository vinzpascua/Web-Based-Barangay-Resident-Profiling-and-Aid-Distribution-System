document.addEventListener("DOMContentLoaded", () => {

    const addBtn = document.querySelector('.add-resident');
    const modal = document.getElementById('residentModal');
    const overlay = document.getElementById('modalOverlay');
    const closeBtn = document.querySelector('.close-btn');
    const form = document.getElementById("addResidentForm");

    if (!modal || !overlay || !form) return;

    const birthdateInput = form.birthdate;
    const ageInput = form.age;

    let isEdit = false;

    /* ================= SEARCH (SAFE) ================= */
    const searchInput = document.getElementById("searchInput");
    const tableBody = document.getElementById("residentTableBody");
    const rows = tableBody.querySelectorAll("tr:not(#noResultRow)");
    const noResultRow = document.getElementById("noResultRow");
    const pagination = document.querySelector(".pagination");

    if (!searchInput) return;

    searchInput.addEventListener("keyup", () => {
        const query = searchInput.value.toLowerCase().trim();
        let hasMatch = false;

        rows.forEach(row => {
            const rowText = row.innerText.toLowerCase();

            if (rowText.includes(query)) {
                row.style.display = "";
                hasMatch = true;
            } else {
                row.style.display = "none";
            }
        });

        // Show / hide "no result" message
        noResultRow.style.display = hasMatch ? "none" : "";

        // Hide pagination while searching
        if (pagination) {
            pagination.style.display = query ? "none" : "flex";
        }
    });
    /* ================= AGE CALCULATION ================= */
    function calculateAge(birthdate) {
        if (!birthdate) return "";

        const today = new Date();
        const birth = new Date(birthdate);

        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        return age >= 0 ? age : "";
    }

    if (birthdateInput) {
        birthdateInput.addEventListener("change", () => {
            ageInput.value = calculateAge(birthdateInput.value);
        });
    }

    /* ================= OPEN MODAL ================= */
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            isEdit = false;
            form.reset();
            form.resident_id.value = "";
            ageInput.value = "";
            form.querySelector("button").innerText = "Save Resident";
            modal.classList.add("show");
            overlay.classList.add("show");
        });
    }

    /* ================= CLOSE MODAL ================= */
    function closeModal() {
        modal.classList.remove("show");
        overlay.classList.remove("show");
    }

    if (closeBtn) closeBtn.addEventListener("click", closeModal);
    overlay.addEventListener("click", closeModal);

    /* ================= EDIT & DELETE (EVENT DELEGATION) ================= */
    document.addEventListener("click", (e) => {

        /* ---------- EDIT ---------- */
        const editBtn = e.target.closest(".edit");
        if (editBtn) {
            isEdit = true;

            form.resident_id.value = editBtn.dataset.id;
            form.first_name.value = editBtn.dataset.first;
            form.middle_name.value = editBtn.dataset.middle;
            form.last_name.value = editBtn.dataset.last;
            form.address.value = editBtn.dataset.address;
            form.birthdate.value = editBtn.dataset.birthdate;
            form.age.value = calculateAge(editBtn.dataset.birthdate);
            form.gender.value = editBtn.dataset.gender;
            form.civil_status.value = editBtn.dataset.civil;
            form.occupation.value = editBtn.dataset.occupation;
            form.voters_registration_no.value = editBtn.dataset.voters;
            form.contact.value = editBtn.dataset.contact;

            form.querySelector("button").innerText = "Update Resident";

            modal.classList.add("show");
            overlay.classList.add("show");
            return;
        }

        /* ---------- DELETE ---------- */
        const deleteBtn = e.target.closest(".delete");
        if (deleteBtn) {
            Popup.open({
                title: "Confirm Delete",
                message: "Are you sure you want to delete this resident? This action cannot be undone.",
                type: "danger",
                onOk: () => {
                    fetch("delete_resident.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "id=" + deleteBtn.dataset.id
                    })
                    .then(res => res.text())
                    .then(data => {
                        if (data === "success") {
                            Popup.open({
                                title: "Deleted",
                                message: "Resident deleted successfully.",
                                type: "success",
                                onOk: () => location.reload()
                            });
                        } else {
                            Popup.open({
                                title: "Error",
                                message: "Delete failed. Please try again.",
                                type: "danger"
                            });
                        }
                    });
                }
            });
        }
    });

    /* ================= FORM SUBMIT ================= */
    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const action = isEdit ? "update this resident" : "add this resident";

        Popup.open({
            title: "Confirm",
            message: `Are you sure you want to ${action}?`,
            type: "warning",
            onOk: submitResidentForm
        });
    });

    function submitResidentForm() {
        form.age.value = calculateAge(form.birthdate.value);

        if (!form.contact.value.trim()) {
            form.contact.value = "N/A";
        } else if (form.contact.value !== "N/A") {
            const phoneRegex = /^[0-9]{11}$/;
            if (!phoneRegex.test(form.contact.value)) {
                Popup.open({
                    title: "Invalid Contact",
                    message: "Contact number must be 11 digits or 'N/A'.",
                    type: "warning"
                });
                return;
            }
        }

        if (!form.voters_registration_no.value.trim()) {
            form.voters_registration_no.value = "Not Registered";
        }

        const formData = new FormData(form);
        const url = isEdit ? "update_resident.php" : "add_resident.php";

        fetch(url, {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if (data === "success") {
                Popup.open({
                    title: "Success",
                    message: isEdit
                        ? "Resident updated successfully."
                        : "Resident added successfully.",
                    type: "success",
                    onOk: () => location.reload()
                });
            } else {
                Popup.open({
                    title: "Error",
                    message: "Action failed. Please try again.",
                    type: "danger"
                });
            }
        });
    }

});
