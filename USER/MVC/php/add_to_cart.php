<?php
session_start();
include("../Db/dbregister.php"); // $conn

/* CHECK LOGIN */
if (!isset($_SESSION['username'])) {
    echo "login";
    exit();
}

/* CREATE CART */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo "invalid";
    exit();
}

/*Check stock + availability from DB */
$stmt = mysqli_prepare($conn, "SELECT title, final_price, status, quantity FROM books WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$book = ($res && mysqli_num_rows($res) == 1) ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$book) {
    echo "notfound";
    exit();
}

if (strtolower($book['status']) != "available") {
    echo "notavailable";
    exit();
}

if ((int)$book['quantity'] <= 0) {
    echo "outofstock";
    exit();
}

/*If already in cart, make sure cart qty won't exceed stock */
$found = false;

foreach ($_SESSION['cart'] as &$item) {
    if ((int)$item['id'] === $id) {

        if ((int)$item['qty'] >= (int)$book['quantity']) {
            echo "limit"; // can't add more than stock
            exit();
        }

        $item['qty']++;
        // also refresh title/price from DB (security)
        $item['title'] = $book['title'];
        $item['price'] = (float)$book['final_price'];

        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        "id"    => $id,
        "title" => $book['title'],
        "price" => (float)$book['final_price'],
        "qty"   => 1
    ];
}

echo "success";
exit();
?>
