<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="../Css/view_cart.css?v=999">
</head>
<body>

<div class="container">
    <h2 class="page-title">My Cart</h2>

    <!-- Empty message -->
    <p class="empty-cart" id="emptyMsg" style="<?php echo empty($_SESSION['cart']) ? '' : 'display:none;'; ?>">
        Your cart is empty
    </p>
    <a href="booklist.php" id="shopLink" style="<?php echo empty($_SESSION['cart']) ? '' : 'display:none;'; ?>">
        Go Shopping
    </a>

    <?php if (!empty($_SESSION['cart'])) { ?>

        <?php
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $id = (int)$item['id'];
            $price = (float)$item['price'];
            $qty = (int)$item['qty'];
            $subtotal = $price * $qty;
            $total += $subtotal;
        ?>
        <div class="cart-card" id="row-<?php echo $id; ?>">

            <div class="cart-left">
                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                <p>Price: ৳<?php echo number_format($price, 2); ?></p>

                <!-- hidden price for JS -->
                <span id="price-<?php echo $id; ?>" style="display:none;"><?php echo $price; ?></span>

                <div class="qty-box">
                    <button type="button" onclick="updateQty('decrease', <?php echo $id; ?>)">−</button>

                    <span class="qty" id="qty-<?php echo $id; ?>"><?php echo $qty; ?></span>

                    <button type="button" onclick="updateQty('increase', <?php echo $id; ?>)">+</button>
                </div>
            </div>

            <div class="cart-right">
                <p id="sub-<?php echo $id; ?>">৳<?php echo number_format($subtotal, 2); ?></p>
                <button type="button" onclick="removeItem(<?php echo $id; ?>)">Remove</button>
            </div>
        </div>
        <?php } ?>

        <h3>Total: ৳<span id="cartTotal"><?php echo number_format($total, 2); ?></span></h3>

        <div class="cart-actions" id="cartActions">
            <a href="booklist.php">Continue Shopping</a>
            <a href="checkout.php" class="checkout-btn">Checkout</a>
            <button type="button" onclick="cancelOrder()">Cancel Order</button>
        </div>

    <?php } ?>
</div>
<script src="../Js/view_cart.js"></script>

</body>
</html>
