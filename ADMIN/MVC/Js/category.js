const categoryCard = document.getElementById("category-card");

function goToPage() {
    window.location.href = "../php/category.php";
}

categoryCard.addEventListener("click", goToPage);