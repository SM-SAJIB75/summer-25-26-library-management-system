<?php
session_start();
include("../../../USER/MVC/Db/dbregister.php");

if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@admin")) {
    header("Location: ../../USER/MVC/php/index.php");
    exit();
}

$action = $_POST['action'] ?? '';

function cleanStatus($status) {
    if ($status !== "Available" && $status !== "Unavailable") {
        return "Available";
    }
    return $status;
}

if ($action === "update") {
    $id = (int)$_POST['id'];

    $title       = $_POST['title'];
    $author      = $_POST['author'];
    $price       = $_POST['price'];
    $discount    = $_POST['discount'];
    $final       = $_POST['final_price'];
    $quantity    = $_POST['quantity'];
    $description = $_POST['description'];
    $category    = $_POST['category'];
    $status      = cleanStatus($_POST['status']);

    $sql = "UPDATE books SET title=?, author=?, price=?, discount=?, final_price=?, quantity=?, description=?, status=?, category_id=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdddissii", $title, $author, $price, $discount, $final, $quantity, $description, $status, $category, $id);
    echo mysqli_stmt_execute($stmt) ? "Updated" : "Failed";
    mysqli_stmt_close($stmt);
    exit();
}


if ($action === "delete") {
    $id = (int)$_POST['id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM books WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    echo mysqli_stmt_execute($stmt) ? "Deleted" : "Failed";
    mysqli_stmt_close($stmt);
    exit();
}

echo "Invalid action";
?>
