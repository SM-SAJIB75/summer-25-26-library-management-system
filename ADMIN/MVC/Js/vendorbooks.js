const vendorBooksCard = document.getElementById("vendorbooks-card");

function goToVendorBooks() {
    window.location.href = "../php/adminvendorbooks.php";
}

vendorBooksCard.addEventListener("click", goToVendorBooks);
