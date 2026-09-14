function filterSales() {
    var from = document.getElementById("from").value;
    var to   = document.getElementById("to").value;

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
        document.getElementById("salesTable").innerHTML = this.responseText;
    } else if (this.readyState === 4) {
        alert("Failed to load sales data");
    }
};

    xhttp.open(
        "GET",
        "../Php/sales.php?ajax=1&from=" + encodeURIComponent(from) + "&to=" + encodeURIComponent(to) + "&t=" + new Date().getTime(),
        true
    );
    xhttp.send();
}

document.addEventListener("DOMContentLoaded", filterSales);