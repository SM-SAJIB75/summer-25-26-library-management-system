function postData(url, data, callback) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            callback(this.responseText);
        }
    };
    xhttp.open("POST", url, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data);
}

function handleCartResponse(resText, id) {
    // expected: success|cartCount|total|removed
    var parts = resText.split("|");

    // safety check
    if (parts.length < 4) {
        console.log("Bad response:", resText);
        return;
    }

    var cartCount = parseInt(parts[1], 10);
    var total = parseFloat(parts[2]);
    var removed = (parts[3] === "1");

    document.getElementById("cartTotal").innerHTML = total.toFixed(2);

    if (removed) {
        var row = document.getElementById("row-" + id);
        if (row) row.remove();
    }

    if (cartCount === 0) {
        document.getElementById("emptyMsg").style.display = "block";
        document.getElementById("shopLink").style.display = "inline-block";
        var actions = document.getElementById("cartActions");
        if (actions) actions.style.display = "none";
    }
}

function updateQty(action, id) {
    var qtyEl = document.getElementById("qty-" + id);
    var priceEl = document.getElementById("price-" + id);
    var subEl = document.getElementById("sub-" + id);

    var currentQty = parseInt(qtyEl.innerHTML, 10);
    var price = parseFloat(priceEl.innerHTML);

    if (action === "increase") currentQty++;
    if (action === "decrease") currentQty--;

    // UI update first
    if (currentQty <= 0) {
        var row = document.getElementById("row-" + id);
        if (row) row.remove();
    } else {
        qtyEl.innerHTML = currentQty;
        subEl.innerHTML = "৳" + (price * currentQty).toFixed(2);
    }

    // server update
    postData("update_cart.php", "action=" + action + "&id=" + id, function (resText) {
        handleCartResponse(resText, id);
    });
}

function removeItem(id) {
    var row = document.getElementById("row-" + id);
    if (row) row.remove();

    postData("update_cart.php", "action=remove&id=" + id, function (resText) {
        handleCartResponse(resText, id);
    });
}

function cancelOrder() {
    var rows = document.querySelectorAll(".cart-card");
    for (var i = 0; i < rows.length; i++) rows[i].remove();

    postData("update_cart.php", "action=cancel", function (resText) {
        // expected: success|0|0|1
        var parts = resText.split("|");

        document.getElementById("cartTotal").innerHTML = "0.00";
        document.getElementById("emptyMsg").style.display = "block";
        document.getElementById("shopLink").style.display = "inline-block";

        var actions = document.getElementById("cartActions");
        if (actions) actions.style.display = "none";
    });
}