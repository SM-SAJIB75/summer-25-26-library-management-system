const buyNowBtn = document.getElementById('buyNowBtn');

function goToBooksPage() {
    window.location.href = '../php/books.php'; 
}
buyNowBtn.addEventListener('click', goToBooksPage);
