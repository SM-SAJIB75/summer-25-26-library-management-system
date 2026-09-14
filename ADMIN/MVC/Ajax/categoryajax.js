function addCategory() {
    var name = document.getElementById("cat_name").value.trim();

    if (name === "") {
        alert("Category name cannot be empty!");
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            if (this.responseText === "success") {
                alert("Category added!");
                location.reload();
            } else {
                alert(this.responseText);
            }
        }
    };
    xhttp.open("POST", "categoryaction.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("action=add&name=" + encodeURIComponent(name));
}

function updateCategory(id) {
    var name = document.getElementById("name-" + id).value.trim();

    if (name === "") {
        alert("Category name cannot be empty!");
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            if (this.responseText === "Updated") {
                alert("Category updated!");
            } else {
                alert(this.responseText);
            }
        }
    };
    xhttp.open("POST", "categoryaction.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("action=update&id=" + id + "&name=" + encodeURIComponent(name));
}

function deleteCategory(id) {
    if (!confirm("Are you sure to delete this category?")) return;

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            if (this.responseText === "Deleted") {
                alert("Category deleted!");
                document.getElementById("row-" + id).remove();
            } else {
                alert(this.responseText);
            }
        }
    };
    xhttp.open("POST", "categoryaction.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("action=delete&id=" + id);
}