document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.querySelector('.add-household');
    const modal = document.getElementById('residentModal');
    const overlay = document.getElementById('modalOverlay');
    const closeBtn = document.getElementById('closeModal');
    const form = document.getElementById('addResidentForm');
    const householdId = document.getElementById('resident_id');
    const headInput = document.getElementById("headInput");
    const addressInput = document.querySelector("input[name='address']");
    const residentPicker = document.getElementById("residentPicker");
    const rfidInput = document.querySelector("input[name='rfid']");
    const modalTitle = document.getElementById("modalTitle");
    const modalIcon = document.getElementById("modalIcon");
    const saveBtn = document.getElementById("saveHouseholdBtn");
    const membersInput = document.getElementById("membersInput");
    const memberSearch = document.getElementById("memberSearch");

    // --- HELPER FUNCTIONS ---
    const togglePicker = (show) => {
        if (show) residentPicker.classList.add("show");
        else residentPicker.classList.remove("show");
    };

    const addMemberToInput = (name) => {
        let current = membersInput.value
            .split(',')
            .map(m => m.trim())
            .filter(Boolean);

        if (!current.includes(name)) {
            current.push(name);
            membersInput.value = current.join(', ');
        }
    };

    const removeMemberFromInput = (name) => {
        let current = membersInput.value
            .split(',')
            .map(m => m.trim())
            .filter(Boolean)
            .filter(m => m !== name);
        membersInput.value = current.join(', ');
    };

    // --- OPEN PICKER FOR MEMBERS ---
    membersInput.addEventListener("focus", () => togglePicker(true));

    // --- SELECT MEMBER FROM PICKER ---
    document.addEventListener("click", (e) => {
        // Add member button clicked
        if (e.target.classList.contains("select-member")) {
            const name = e.target.dataset.name;
            addMemberToInput(name);
        }

        // Close picker if clicked outside
        if (!e.target.closest(".head-picker") && !e.target.closest("#residentPicker") && e.target !== membersInput) {
            togglePicker(false);
        }
    });

    // --- SEARCH FILTER ---
    memberSearch.addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#residentPicker tbody tr").forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // --- REMOVE MEMBER BY CLICKING INSIDE INPUT (OPTIONAL) ---
    membersInput.addEventListener("click", () => {
        const current = membersInput.value.split(',').map(m => m.trim()).filter(Boolean);
        if (current.length === 0) return;

        const nameToRemove = prompt(`Current members:\n${current.join(', ')}\nEnter a name to remove:`);
        if (nameToRemove) removeMemberFromInput(nameToRemove);
    });

    // --- HEAD INPUT PICKER ---
    headInput.addEventListener("focus", () => togglePicker(true));
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("select-resident")) {
            headInput.value = e.target.dataset.name;
            addressInput.value = e.target.dataset.address;
            togglePicker(false);
        }
    });

    // --- MODAL OPEN / CLOSE ---
    addBtn.addEventListener('click', () => {
        form.reset();
        householdId.value = "";
        saveBtn.innerText = "Save Household";

        modalTitle.innerText = "Add New Household";
        modalIcon.className = "fa-solid fa-house";

        fetch('get_next_household_number.php')
            .then(res => res.text())
            .then(num => {
                form.household_number.value = num;
            });

        modal.classList.add('show');
        overlay.classList.add('show');
    });

    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);

    function closeModal() {
        modal.classList.remove('show');
        overlay.classList.remove('show');
    }

    // --- FORM SUBMIT ---
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Clean members
        const cleanedMembers = form.household_members.value
            .split(',')
            .map(m => m.trim())
            .filter(Boolean)
            .join(', ');
        form.household_members.value = cleanedMembers;

        fetch('add_household.php', { method: 'POST', body: new FormData(form) })
            .then(res => res.text())
            .then(data => {
                data = data.trim();
                if (data === 'success') {
                    if (householdId.value) {
                        const row = document.querySelector(`tr[data-id='${householdId.value}']`);
                        if (row) {
                            row.querySelector('.member-count').dataset.members = cleanedMembers;
                            row.querySelector('.member-count').innerText = `${cleanedMembers.split(',').length} members`;
                        }
                    } else location.reload();

                    closeModal();
                    const toast = document.getElementById('members-toast');
                    const toastText = document.getElementById('members-text');
                    toastText.innerText = "Household saved successfully!!";
                    toast.classList.add('show');
                } else alert('Failed to save household: ' + data);
            }).catch(err => alert('Server error: ' + err));
    });

    // --- EDIT & DELETE ---
    document.addEventListener('click', function (e) {
        // Edit
        if (e.target.closest('.edit')) {
            const btn = e.target.closest('.edit');
            householdId.value = btn.dataset.id || "";
            form.querySelector("input[name='household_number']").value = btn.dataset.number || "";
            form.querySelector("input[name='head_of_family']").value = btn.dataset.head || "";
            form.querySelector("input[name='address']").value = btn.dataset.address || "";
            form.querySelector("input[name='household_members']").value = btn.dataset.members || "";
            saveBtn.innerText = "Updated Household";
            modalTitle.innerText = "Edit Household";
            modalIcon.className = "fa-solid fa-pen-to-square";
            modal.classList.add('show');
            overlay.classList.add('show');
        }

        // Delete
        if (e.target.closest('.delete')) {
            const btn = e.target.closest('.delete');
            const id = btn.dataset.id;
            if (confirm("Are you sure you want to delete this household?")) {
                fetch('delete_household.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${encodeURIComponent(id)}`
                })
                    .then(res => res.text())
                    .then(data => {
                        if (data.trim() === 'success') btn.closest('tr')?.remove();
                        else alert("Failed to delete: " + data);
                    }).catch(err => alert("Server error: " + err));
            }
        }
    });

    // --- SHOW MEMBERS TOAST ---
    const toast = document.getElementById('members-toast');
    const toastText = document.getElementById('members-text');
    const closeToastBtn = document.getElementById('close-toast');

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('member-count')) {
            e.stopPropagation();
            toastText.innerText = "Household Members:\n" + e.target.dataset.members;
            toast.classList.add('show');
        }
    });

    closeToastBtn.addEventListener('click', () => toast.classList.remove('show'));
});
