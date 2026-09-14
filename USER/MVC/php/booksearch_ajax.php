<?php
session_start();
include("../Db/dbregister.php");

header("Content-Type: application/json");

$search   = isset($_GET['search']) ? trim($_GET['search']) : "";
$category = isset($_GET['category']) ? trim($_GET['category']) : "";

$baseQuery = "SELECT id, title, author, final_price, status, image FROM books WHERE status='available' AND quantity > 0 AND approval_status='approved'";

if ($search !== "" && $category !== "") {
    $stmt = mysqli_prepare($conn, $baseQuery . " AND (title LIKE ? OR author LIKE ?) AND category_id = ? ORDER BY id DESC");
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt, "ssi", $like, $like, $category);
} elseif ($search !== "") {
    $stmt = mysqli_prepare($conn, $baseQuery . " AND (title LIKE ? OR author LIKE ?) ORDER BY id DESC");
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
} elseif ($category !== "") {
    $stmt = mysqli_prepare($conn, $baseQuery . " AND category_id = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "i", $category);
} else {
    $stmt = mysqli_prepare($conn, $baseQuery . " ORDER BY id DESC");
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$books = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

echo json_encode($books);
exit();
