<?php
session_start();
include("../../../USER/MVC/Db/dbregister.php");

if (!isset($_SESSION['username']) || !str_starts_with($_SESSION['username'], "@admin")) {
    header("Location: ../../USER/MVC/php/index.php");
    exit();
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {

    $from = trim($_GET['from'] ?? '');
    $to   = trim($_GET['to'] ?? '');

    $baseSql = "
    SELECT 
            o.order_id, 
            o.name, 
            o.order_date, 
            o.total_amount,
            i.book_title, 
            i.price, 
            i.quantity
        FROM orders o
        JOIN order_items i ON o.order_id = i.order_id
        LEFT JOIN books b ON i.book_id = b.id
        %s ORDER BY o.order_date DESC";

    if ($from !== '' && $to !== '') {
        $sql = sprintf($baseSql, "WHERE DATE(o.order_date) >= ? AND DATE(o.order_date) <= ?");
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $from, $to);
    } elseif ($from !== '') {
        $sql = sprintf($baseSql, "WHERE DATE(o.order_date) >= ?");
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $from);
    } elseif ($to !== '') {
        $sql = sprintf($baseSql, "WHERE DATE(o.order_date) <= ?");
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $to);
    } else {
        $sql = sprintf($baseSql, "");
        $stmt = mysqli_prepare($conn, $sql);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

     echo '
    <tr>
        <th>#</th>
        <th>Order ID</th>
        <th>Date</th>
        <th>Customer</th>
        <th>Book</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Item Total</th>
        <th>Order Total</th>
    </tr>';

    if (mysqli_num_rows($result) == 0) {
        echo '<tr><td colspan="9" style="text-align:center;">No sales found</td></tr>';
        exit;
    }

    $sl = 1;
    $grandTotal = 0;
    $lastOrder = null;

    while ($row = mysqli_fetch_assoc($result)) {
        $itemTotal = $row['price'] * $row['quantity'];

        if ($lastOrder !== $row['order_id']) {
            $grandTotal += $row['total_amount'];
            $lastOrder = $row['order_id'];
        }

         echo '<tr>
            <td>'.$sl++.'</td>
            <td>'.$row['order_id'].'</td>
            <td>'.date("d M Y", strtotime($row['order_date'])).'</td>
            <td>'.$row['name'].'</td>
            <td>'.$row['book_title'].'</td>
            <td>৳'.number_format($row['price'], 2).'</td>
            <td>'.$row['quantity'].'</td>
            <td>৳'.number_format($itemTotal, 2).'</td>
            <td>৳'.number_format($row['total_amount'], 2).'</td>
        </tr>';
    }
     
     echo '<tr>
        <td colspan="8"><b>Grand Total Sales</b></td>
        <td><b>৳'.number_format($grandTotal, 2).'</b></td>
    </tr>';

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
    <link rel="stylesheet" href="../Css/sales.css">
</head>
<body>

<h2 class="page-title">Sales Report</h2>

<div class="filter-box">
    From: <input type="date" id="from">
    To: <input type="date" id="to">
    <button onclick="filterSales()">Filter</button>
</div>
<table class="report-table" id="salesTable"></table>

<script src="../Ajax/salesajax.js"></script>
</body>
</html>