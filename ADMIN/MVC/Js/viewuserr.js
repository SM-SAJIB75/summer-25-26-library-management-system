const userCard = document.getElementById("user-card");

function goToPage() {
    window.location.href = "../php/viewuser.php";
}

userCard.addEventListener('click', goToPage);
