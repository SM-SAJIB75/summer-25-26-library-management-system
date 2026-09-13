<?php
session_start();
include "../../../USER/MVC/Db/dbregister.php";
//Oishe
if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@vendor")) {
    header("Location: ../../../USER/MVC/php/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_SESSION["username"];

    // get vendor_id from username
    $vStmt = mysqli_prepare($conn, "SELECT vendor_id FROM vendors WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($vStmt, "s", $username);
    mysqli_stmt_execute($vStmt);
    $vRes = mysqli_stmt_get_result($vStmt);
    $vendorRow = mysqli_fetch_assoc($vRes);
    $vendor_id = (int)$vendorRow["vendor_id"];
    mysqli_stmt_close($vStmt);

    $category_id = (int)$_POST["category_id"];
    $title       = trim($_POST["title"]);
    $author      = trim($_POST["author"]);
    $price       = (float)$_POST["price"];
    $quantity    = (int)$_POST["quantity"];
    $description = trim($_POST["description"]);
    $final_price = $price; // no discount by default on vendor-added books

    // handle image upload (optional) - saved into USER/MVC/Picture/
    // (same folder booklist.php / bookdetails.php already read images from)
    $image_name = "no_image.jpg"; // fallback if no image uploaded
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], "../../../USER/MVC/Picture/" . $image_name);
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO books 
        (category_id, title, author, price, discount, final_price, quantity, description, image, status, vendor_id, approval_status) 
        VALUES (?, ?, ?, ?, 0, ?, ?, ?, ?, 'available', ?, 'pending')");

    // 9 placeholders -> 9 type chars:
    // i(category_id) s(title) s(author) d(price) d(final_price) i(quantity) s(description) s(image) i(vendor_id)
    mysqli_stmt_bind_param($stmt, "issddissi",
        $category_id, $title, $author, $price, $final_price, $quantity, $description, $image_name, $vendor_id
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: vendordashboard.php?added=1");
    exit();
}
?>
