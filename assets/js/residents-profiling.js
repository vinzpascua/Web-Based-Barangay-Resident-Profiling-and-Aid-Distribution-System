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

        // ✅ ALWAYS recompute age
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

// Submit form (Add or Update)
form.addEventListener("submit", function(e) {
    e.preventDefault();

    // ✅ Ensure age is correct before submit
    form.age.value = calculateAge(form.birthdate.value);

    // CONTACT VALIDATION
    if (!form.contact.value.trim()) {
        form.contact.value = "N/A";
    } else if (form.contact.value !== "N/A") {
        const phoneRegex = /^[0-9]{11}$/;
        if (!phoneRegex.test(form.contact.value)) {
            alert("Contact number must be 11 digits or 'N/A'");
            return;
        }
    }

    // DEFAULT VALUE FOR VOTERS REGISTRATION NUMBER
    if (!form.voters_registration_no.value.trim()) {
        form.voters_registration_no.value = "Not Registered";
    }

    const formData = new FormData(this);
    const url = isEdit ? "update_resident.php" : "add_resident.php";

    fetch(url, {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data === "success") {
            alert(isEdit ? "Resident updated successfully!" : "Resident added successfully!");
            location.reload();
        } else {
            alert("Action failed");
        }
    });
});

// Delete resident
document.querySelectorAll(".delete").forEach(btn => {
    btn.addEventListener("click", () => {
        if(!confirm("Are you sure you want to delete this resident?")) return;

        fetch("delete_resident.php", {
            method: "POST",
            headers: {"Content-Type":"application/x-www-form-urlencoded"},
            body: "id=" + btn.dataset.id
        })
        .then(res => res.text())
        .then(data => {
            if(data === "success") {
                alert("Resident deleted successfully!");
                location.reload();
            } else {
                alert("Delete failed");
            }
        });
    });
});
