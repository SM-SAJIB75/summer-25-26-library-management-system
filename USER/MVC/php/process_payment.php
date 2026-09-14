<?php
session_start();
include "../Db/dbregister.php";

/* Security checks */
if (!isset($_SESSION['username']) || !isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: checkout.php");
    exit();
}

/* Get form data */
$username = $_SESSION['username'];
$name     = trim($_POST['name'] ?? "");
$mobile   = trim($_POST['mobile'] ?? "");
$address  = trim($_POST['address'] ?? "");
$method   = $_POST['payment_method'] ?? "";

$payment_number = "";

/* Payment validation */
if ($method == "bKash") {
    if (empty($_POST['bkash_number'])) {
        $_SESSION['checkout_error'] = "bKash number is required";
        header("Location: checkout.php");
        exit();
    }
    $payment_number = $_POST['bkash_number'];
}

if ($method == "Nagad") {
    if (empty($_POST['nagad_number'])) {
        $_SESSION['checkout_error'] = "Nagad number is required";
        header("Location: checkout.php");
        exit();
    }
    $payment_number = $_POST['nagad_number'];
}

/* STEP 1: Stock check + real price/title from DB */
$total = 0;
$verifiedCart = []; // will store final title/price for each item

foreach ($_SESSION['cart'] as $item) {

    $book_id = (int)($item['id'] ?? 0);
    $qty     = (int)($item['qty'] ?? 0);

    if ($book_id <= 0 || $qty <= 0) {
        $_SESSION['checkout_error'] = "Invalid cart item found!";
        header("Location: checkout.php");
        exit();
    }

    $stmt = mysqli_prepare($conn, "SELECT title, final_price, quantity, status FROM books WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);

    $b = ($r && mysqli_num_rows($r) == 1) ? mysqli_fetch_assoc($r) : null;
    mysqli_stmt_close($stmt);

    if (!$b || strtolower($b['status']) != "available") {
        $_SESSION['checkout_error'] = "One or more books are not available!";
        header("Location: checkout.php");
        exit();
    }

    if ((int)$b['quantity'] < $qty) {
        $_SESSION['checkout_error'] = "Stock not enough for: " . $b['title'];
        header("Location: checkout.php");
        exit();
    }

    $price = (float)$b['final_price'];
    $title = $b['title'];

    $total += ($price * $qty);

    // save verified values for step 3
    $verifiedCart[] = [
        "book_id" => $book_id,
        "qty"     => $qty,
        "price"   => $price,
        "title"   => $title
    ];
}

$total = round($total, 2);

/* STEP 2: Insert order */
$stmt = mysqli_prepare($conn, "INSERT INTO orders 
        (username, name, mobile, address, payment_method, payment_number, total_amount)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssssd", $username, $name, $mobile, $address, $method, $payment_number, $total);

if (!mysqli_stmt_execute($stmt)) {
    die("Order insert failed: " . mysqli_error($conn));
}

$order_id = (int)mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

/* STEP 3: Insert order_items + decrease stock (AND save book_title!) */
foreach ($verifiedCart as $v) {

    $book_id = (int)$v['book_id'];
    $qty     = (int)$v['qty'];
    $price   = (float)$v['price'];
    $title   = $v['title'];

    // decrease stock FIRST and verify
    $stmtStock = mysqli_prepare($conn, "UPDATE books SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
    mysqli_stmt_bind_param($stmtStock, "iii", $qty, $book_id, $qty);
    mysqli_stmt_execute($stmtStock);

    if (mysqli_stmt_affected_rows($stmtStock) != 1) {
        mysqli_stmt_close($stmtStock);
        $_SESSION['checkout_error'] = "Stock issue detected. Please try again.";
        header("Location: checkout.php");
        exit();
    }
    mysqli_stmt_close($stmtStock);

    // insert order item INCLUDING book_title
    $stmtItem = mysqli_prepare($conn, "INSERT INTO order_items (order_id, book_id, book_title, quantity, price) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmtItem, "iisid", $order_id, $book_id, $title, $qty, $price);
    mysqli_stmt_execute($stmtItem);
    mysqli_stmt_close($stmtItem);
}

/* Clear cart */
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Successful</title>
    <link rel="stylesheet" href="../Css/theme.css">
    <link rel="stylesheet" href="../Css/process_payment.css">
</head>
<body>
<div class="success-card">
    <div class="check">Checked Completed.</div>
    <h2>Order Placed Successfully!</h2>
    <p>Thank you, <b><?php echo htmlspecialchars($name); ?></b></p>

    <div class="details">
        <center><p><span>Order ID:</span> <?php echo $order_id; ?></p></center>
        <center><p><span>Payment Method:</span> <?php echo htmlspecialchars($method); ?></p></center>
        <center><p><span>Total Amount:</span> ৳<?php echo number_format($total, 2); ?></p></center>
    </div>

    <a href="../php/index.php" class="btn">Back to Home</a>
</div>
</body>
</html>
