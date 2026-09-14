function addToCart(form) {
    var id    = form.querySelector('input[name="id"]').value;
    var title = form.querySelector('input[name="title"]').value;
    var price = form.querySelector('input[name="price"]').value;

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {

            var res = this.responseText.trim();

            // success → cart page
            if (res === "success") {
                window.location.href = "view_cart.php";
            }

            // not logged in → login page
            if (res === "login") {
                window.location.href = "login.php";
            }
        }
    };

    var data =
        "id=" + encodeURIComponent(id) +
        "&title=" + encodeURIComponent(title) +
        "&price=" + encodeURIComponent(price);

    xhttp.open("POST", "add_to_cart.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data);

    return false; // stop page reload
}
