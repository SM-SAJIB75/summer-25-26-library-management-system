const salesCard = document.getElementById("sales-card");

function goToPage() {
    window.location.href = "../php/sales.php";
}

salesCard.addEventListener("click", goToPage);