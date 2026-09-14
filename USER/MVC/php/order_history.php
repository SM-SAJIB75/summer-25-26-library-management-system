<?php
session_start();
include("../Db/dbregister.php");

/* Must login */
if (!isset($_SESSION['username'])) {
    $_SESSION['login_error'] = "Please login to view order history";
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

/* Fetch orders (procedural + prepared statement) */
$sql = "SELECT order_id, order_date, payment_method, total_amount
        FROM orders
        WHERE username = ?
        ORDER BY order_id DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Order History</title>
    <link rel="stylesheet" href="../Css/theme.css">
    <link rel="stylesheet" href="../Css/order_history.css?v=10">
</head>
<body>

<div class="container">
    <h2 class="page-title">My Order History</h2>

    <?php if ($result && mysqli_num_rows($result) > 0) { ?>

        <?php while($order = mysqli_fetch_assoc($result)) {
            $orderId = (int)$order['order_id'];

            $itemsSql = "
                SELECT 
                    COALESCE(oi.book_title, b.title, 'Book Removed') AS book_name,
                    oi.price,
                    oi.quantity
                FROM order_items oi
                LEFT JOIN books b ON oi.book_id = b.id
                WHERE oi.order_id = ?
            ";
            $itemsStmt = mysqli_prepare($conn, $itemsSql);
            mysqli_stmt_bind_param($itemsStmt, "i", $orderId);
            mysqli_stmt_execute($itemsStmt);
            $items = mysqli_stmt_get_result($itemsStmt);
        ?>

        <div class="order-card">
            <div class="order-head">
                <div>
                    <h3>Order #<?php echo $orderId; ?></h3>

                    <p><b>Date:</b> <?php echo htmlspecialchars($order['order_date']); ?></p>
                    <p><b>Payment:</b> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p><b>Total:</b> ৳<?php echo number_format((float)$order['total_amount'], 2); ?></p>
                </div>

                <button class="toggle-btn" id="btn-<?php echo $orderId; ?>"
                        onclick="toggleItems(<?php echo $orderId; ?>)">
                    View Items
                </button>
            </div>

            <div class="items-box" id="items-<?php echo $orderId; ?>" style="display:none;">
                <table class="items-table">
                    <tr>
                        <th>Book</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>

                    <?php if ($items && mysqli_num_rows($items) > 0) { ?>
                        <?php while($it = mysqli_fetch_assoc($items)) {
                            $p = (float)$it['price'];
                            $q = (int)$it['quantity'];
                            $sub = $p * $q;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($it['book_name']); ?></td>
                            <td>৳<?php echo number_format($p, 2); ?></td>
                            <td><?php echo $q; ?></td>
                            <td>৳<?php echo number_format($sub, 2); ?></td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4">No items found for this order.</td>
                        </tr>
                    <?php } ?>

                </table>
            </div>
        </div>

        <?php } ?>

    <?php } else { ?>

        <p class="empty">You have not placed any orders yet.</p>
        <div class="actions">
            <a class="btn" href="booklist.php">Browse Books</a>
        </div>

    <?php } ?>

    <div class="actions" style="margin-top:15px;">
        <a class="btn" href="index.php">Back to Home</a>
    </div>
</div>

<script>
function toggleItems(orderId) {
    var box = document.getElementById("items-" + orderId);
    var btn = document.getElementById("btn-" + orderId);

    if (box.style.display === "none") {
        box.style.display = "block";
        btn.innerHTML = "Hide Items";
    } else {
        box.style.display = "none";
        btn.innerHTML = "View Items";
    }
}
</script>

</body>
</html>
