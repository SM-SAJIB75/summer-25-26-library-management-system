const orderCard = document.getElementById("order-card");

function goToPage() {
    window.location.href = "../php/order.php";
}

orderCard.addEventListener("click", goToPage);