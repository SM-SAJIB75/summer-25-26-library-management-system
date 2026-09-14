function toggleItems(orderId){
    var box = document.getElementById("items-" + orderId);
    if(box.style.display === "none"){
        box.style.display = "block";
    }else{
        box.style.display = "none";
    }
}