const taglines = [
    "Making Services Easier for Residents and Staff",
    "Fast, Accessible, and Transparent Barangay Services",
    "Connecting the Community Through Efficient Governance"
];

let index = 0;
const taglineElement = document.getElementById("tagline");

function typeText(text, i = 0) {
    taglineElement.textContent = text.substring(0, i);

    if (i < text.length) {
        setTimeout(() => typeText(text, i + 1), 80);
    }
}

function changeTagline() {
    taglineElement.style.opacity = 0;

    setTimeout(() => {
        index = (index + 1) % taglines.length;
        typeText(taglines[index]);
        taglineElement.style.opacity = 1;
    }, 500);
}

// wait until page is fully loaded
document.addEventListener("DOMContentLoaded", () => {
    typeText(taglines[0]);
    setInterval(changeTagline, 5000);
});