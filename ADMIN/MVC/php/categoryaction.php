<?php
session_start();
include("../../../USER/MVC/Db/dbregister.php");

if (!isset($_SESSION['username']) || !str_starts_with($_SESSION['username'], '@admin')) {
    header("Location: ../../USER/MVC/php/login.php");
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === "add") {
    $name = trim($_POST['name'] ?? '');

    if ($name === "") {
        exit("Category name required");
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO categories(name) VALUES(?)");
    mysqli_stmt_bind_param($stmt, "s", $name);
    echo mysqli_stmt_execute($stmt) ? "success" : "Failed";
    mysqli_stmt_close($stmt);
    exit();
}

if ($action === "update") {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($id <= 0 || $name === "") exit("Invalid data");

    $stmt = mysqli_prepare($conn, "UPDATE categories SET name=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $name, $id);
    echo mysqli_stmt_execute($stmt) ? "Updated" : "Failed";
    mysqli_stmt_close($stmt);
    exit();
}

if ($action === "delete") {
    $id = (int)($_POST['id'] ?? 0);

    $stmt1 = mysqli_prepare($conn, "DELETE FROM books WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt1, "i", $id);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    $stmt2 = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt2, "i", $id);
    $ok = mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    echo $ok ? "Deleted" : "Error";
    exit();
}

echo "Invalid action";
?>
