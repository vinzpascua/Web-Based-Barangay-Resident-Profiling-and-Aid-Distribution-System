const addBtn = document.querySelector('.add-resident');
const modal = document.getElementById('residentModal');
const overlay = document.getElementById('modalOverlay');
const closeBtn = document.querySelector('.close-btn');
const form = document.getElementById("addResidentForm");

const birthdateInput = form.birthdate;
const ageInput = form.age;

let isEdit = false;

// ===== AGE CALCULATION =====
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

// Auto-calculate when birthdate changes
birthdateInput.addEventListener("change", () => {
    ageInput.value = calculateAge(birthdateInput.value);
});

// Open Add Resident Modal
addBtn.addEventListener('click', () => {
    isEdit = false;
    form.reset();
    form.querySelector("button").innerText = "Save Resident";
    form.resident_id.value = "";
    ageInput.value = "";
    modal.classList.add('show');
    overlay.classList.add('show');
});

// Close modal
closeBtn.addEventListener('click', closeModal);
overlay.addEventListener('click', closeModal);

function closeModal() {
    modal.classList.remove('show');
    overlay.classList.remove('show');
}

// Edit resident
document.querySelectorAll(".edit").forEach(btn => {
    btn.addEventListener("click", () => {
        isEdit = true;

        form.resident_id.value = btn.dataset.id;
        form.first_name.value = btn.dataset.first;
        form.middle_name.value = btn.dataset.middle;
        form.last_name.value = btn.dataset.last;
        form.address.value = btn.dataset.address;
        form.birthdate.value = btn.dataset.birthdate;

        // Always recompute age
        form.age.value = calculateAge(btn.dataset.birthdate);

        form.gender.value = btn.dataset.gender;
        form.civil_status.value = btn.dataset.civil;
        form.occupation.value = btn.dataset.occupation;
        form.voters_registration_no.value = btn.dataset.voters;
        form.contact.value = btn.dataset.contact;

        form.querySelector("button").innerText = "Update Resident";

        modal.classList.add('show');
        overlay.classList.add('show');
    });
});

// Submit form (Add or Update) with confirmation
form.addEventListener("submit", function (e) {
    e.preventDefault(); // prevent default submit

    // First, confirm with the user
    const action = isEdit ? "update this resident" : "add this resident";
    Popup.open({
        title: "Confirm",
        message: `Are you sure you want to ${action}?`,
        type: "warning",
        onOk: () => {
            // Proceed with submission after confirmation
            submitResidentForm();
        }
    });
});

// Function to actually submit the form
function submitResidentForm() {
    form.age.value = calculateAge(form.birthdate.value);

    // CONTACT VALIDATION
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

    // DEFAULT VALUE FOR VOTERS REGISTRATION NUMBER
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


// Delete resident
document.querySelectorAll(".delete").forEach(btn => {
    btn.addEventListener("click", () => {

        Popup.open({
            title: "Confirm Delete",
            message: "Are you sure you want to delete this resident? This action cannot be undone. Click OK to proceed.",
            type: "danger",
            onOk: () => {
                fetch("delete_resident.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + btn.dataset.id
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

    });
});
