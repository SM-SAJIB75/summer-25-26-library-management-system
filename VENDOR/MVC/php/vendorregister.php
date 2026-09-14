<?php
include "../../../USER/MVC/Db/dbregister.php";

$rawUsername = $password = "";
$usernameError = $passwordError = "";
$success = $error = "";

function test_input($data) {
    return trim($data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $vendor_name = test_input($_POST["vendor_name"] ?? "");
    $contact     = test_input($_POST["contact_number"] ?? "");
    $address     = test_input($_POST["address"] ?? "");

    if (empty($_POST["username"])) {
        $usernameError = "Username is required";
    } else {
        $rawUsername = test_input($_POST["username"]);

        if (!preg_match("/^[a-zA-Z0-9_]+$/", $rawUsername)) {
            $usernameError = "Only letters, numbers, underscore allowed";
        }
    }

    if (empty($_POST["password"])) {
        $passwordError = "Password is required";
    } else {
        $password = test_input($_POST["password"]);

        if (strlen($password) < 6) {
            $passwordError = "Password must be at least 6 characters";
        } elseif (!preg_match("/[A-Z]/", $password)) {
            $passwordError = "Must contain one uppercase letter";
        } elseif (!preg_match("/[a-z]/", $password)) {
            $passwordError = "Must contain one lowercase letter";
        } elseif (!preg_match("/[0-9]/", $password)) {
            $passwordError = "Must contain one number";
        }
    }

    if (empty($vendor_name)) {
        $error = "Vendor / Business name is required";
    }

    if (empty($usernameError) && empty($passwordError) && empty($error)) {

        // Final username always prefixed with @vendor_ so your existing
        // login.php can route it correctly (same idea as @admin)
        $finalUsername = "@vendor_" . $rawUsername;

        // check duplicate
        $check = mysqli_prepare($conn, "SELECT vendor_id FROM vendors WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $finalUsername);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "This vendor username is already taken.";
        } else {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO vendors (username, password, vendor_name, contact_number, address) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $finalUsername, $hashPassword, $vendor_name, $contact, $address);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration complete! Your login username is: " . $finalUsername . " . Redirecting to login...";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vendor Registration</title>
    <link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
    <link rel="stylesheet" href="../../../USER/MVC/Css/register.css">
</head>
<body>

<h1 align="center">Vendor Registration</h1>

<form method="post">
    <p>
        Vendor / Business Name:<br>
        <input type="text" name="vendor_name" value="<?php echo htmlspecialchars($vendor_name ?? ''); ?>">
    </p>

    <p>
        Choose a Username: (this will become <b>@vendor_yourname</b>)<br>
        <input type="text" name="username" value="<?php echo htmlspecialchars($rawUsername); ?>">
        <span class="error"><?php echo $usernameError; ?></span>
    </p>

    <p>
        Password:<br>
        <input type="password" name="password">
        <span class="error"><?php echo $passwordError; ?></span>
    </p>

    <p>
        Contact Number:<br>
        <input type="text" name="contact_number" value="<?php echo htmlspecialchars($contact ?? ''); ?>">
    </p>

    <p>
        Address:<br>
        <input type="text" name="address" value="<?php echo htmlspecialchars($address ?? ''); ?>">
    </p>

    <input type="submit" value="Register as Vendor">
</form>

<p class="success" id="success-msg"><?php echo $success; ?></p>
<p class="error"><?php echo $error; ?></p>

<p style="text-align:center;margin-top:15px;">
    <a href="../../../USER/MVC/php/login.php">Back to Login</a>
</p>

<script>
window.onload = function() {
    var successMsg = document.getElementById("success-msg");
    if (successMsg && successMsg.textContent.trim() !== "") {
        setTimeout(function() {
            window.location.href = '../../../USER/MVC/php/login.php';
        }, 2500);
    }
};
</script>

</body>
</html>
