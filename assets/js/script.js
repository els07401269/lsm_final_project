function openModal(id) {
    document.getElementById(id).classList.add("show");
}

function closeModal(id) {
    document.getElementById(id).classList.remove("show");
}

// click outside modal to close
window.onclick = function (e) {
    document.querySelectorAll(".modal").forEach(modal => {
        if (e.target === modal) {
            modal.classList.remove("show");
        }
    });
};



// FADE MESSAGES 

document.addEventListener("DOMContentLoaded", function () {
    const msg = document.querySelector(".msg");
    const error = document.querySelector(".error");

    if (msg) {
        setTimeout(() => {
            msg.style.opacity = "0";
        }, 3000);
    }

    if (error) {
        setTimeout(() => {
            error.style.opacity = "0";
        }, 3000);
    }
});


// para sa palit profile and log out
function toggleMenu() {
    const menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
}

// close dropdown kapag nag-click sa labas
document.addEventListener("click", function(event) {
    const dropdown = document.querySelector(".profile-dropdown");

    if (!dropdown.contains(event.target)) {
        document.getElementById("dropdownMenu").style.display = "none";
    }
});