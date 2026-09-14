function calcFinalAdd() {
    var price = parseFloat(document.getElementById('price').value) || 0;
    var discount = parseFloat(document.getElementById('discount').value) || 0;
 
    if (discount < 0) discount = 0;
    if (discount > 100) discount = 100;
 
    var finalPrice = price - (price * discount / 100);
    document.getElementById('final_price').value = finalPrice.toFixed(2);
}

function calcFinalUpdate(id) {
    var price = parseFloat(document.getElementById('price-' + id).value) || 0;
    var discount = parseFloat(document.getElementById('discount-' + id).value) || 0;
 
    if (discount < 0) discount = 0;
    if (discount > 100) discount = 100;
 
    var finalPrice = price - (price * discount / 100);
    document.getElementById('final-' + id).value = finalPrice.toFixed(2);
}

function updateBook(id) {
    var d = "action=update&id="+id+
        "&title="+encodeURIComponent(document.getElementById('title-'+id).value)+
        "&author="+encodeURIComponent(document.getElementById('author-'+id).value)+
        "&price="+encodeURIComponent(document.getElementById('price-'+id).value)+
        "&discount="+encodeURIComponent(document.getElementById('discount-'+id).value)+
        "&final_price="+encodeURIComponent(document.getElementById('final-'+id).value)+
        "&quantity="+encodeURIComponent(document.getElementById('quantity-'+id).value)+
        "&description="+encodeURIComponent(document.getElementById('desc-'+id).value)+
        "&category="+encodeURIComponent(document.getElementById('category-'+id).value)+
        "&status="+encodeURIComponent(document.getElementById('status-'+id).value);

    var x = new XMLHttpRequest();
    x.onreadystatechange = function() {
        if(this.readyState === 4 && this.status === 200) {
            alert(this.responseText);
            location.reload();
        }
    };
    x.open("POST","bookaction.php",true);
    x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    x.send(d);
}

function deleteBook(id) {
    if(!confirm("Delete?")) return;

    var x = new XMLHttpRequest();
    x.onreadystatechange = function() {
        if(this.readyState === 4 && this.status === 200) {
            alert(this.responseText);
            location.reload();
        }
    };
    x.open("POST","bookaction.php",true);
    x.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    x.send("action=delete&id="+id);
}