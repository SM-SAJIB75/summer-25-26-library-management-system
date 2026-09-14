<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="../Css/checkout.css">

    <script>
        function togglePayment(method) {
            document.getElementById("bkashBox").style.display = "none";
            document.getElementById("nagadBox").style.display = "none";

            if (method === "bkash") {
                document.getElementById("bkashBox").style.display = "block";
            }
            if (method === "nagad") {
                document.getElementById("nagadBox").style.display = "block";
            }
        }
    </script>
</head>
<body>
    
<div class="checkout-wrapper">

    <div class="checkout-card">
        <h2>Checkout</h2>

        <?php
        if (isset($_SESSION['checkout_error'])) {
            echo "<p class='errormsg'>".$_SESSION['checkout_error']."</p>";
            unset($_SESSION['checkout_error']);
        }
        ?>

        <form method="post" action="process_payment.php">

            <div class="form-group">
                <input type="text" name="name" placeholder="Full Name" required>
            </div>

            <div class="form-group">
                <input type="text" name="mobile" placeholder="Mobile Number" required pattern="\d{10,14}">
            </div>

            <div class="form-group">
                <textarea name="address" placeholder="Delivery Address" rows="4" required></textarea>
            </div>

            <h3>Payment Method</h3>

            <div class="payment-options">

                <label class="pay-card">
                    <input type="radio" name="payment_method" value="COD" required onclick="togglePayment('cod')">
                    <span> Cash on Delivery</span>
                </label>

                <label class="pay-card">
                    <input type="radio" name="payment_method" value="bKash" onclick="togglePayment('bkash')">
                    <span> bKash</span>
                </label>

                <label class="pay-card">
                    <input type="radio" name="payment_method" value="Nagad" onclick="togglePayment('nagad')">
                    <span>Nagad</span>
                </label>

            </div>

            <div id="bkashBox" class="pay-box">
                <input type="text" name="bkash_number" placeholder="bKash Number (01XXXXXXXXX)">
            </div>

            <div id="nagadBox" class="pay-box">
                <input type="text" name="nagad_number" placeholder="Nagad Number (01XXXXXXXXX)">
            </div>

            <button type="submit" class="confirm-btn">Confirm Order</button>

        </form>
    </div>

</div>

</body>
</html>
