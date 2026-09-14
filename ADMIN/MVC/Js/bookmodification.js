const bookCard = document.getElementById("book-card");

function goToPage() {
    window.location.href = "../php/bookmodification.php";
}

bookCard.addEventListener("click", goToPage);