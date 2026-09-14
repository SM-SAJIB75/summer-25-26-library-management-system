<?php
session_start();

$loginRedirectMsg = "";

if (isset($_SESSION['login_error'])) {
    $loginRedirectMsg = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if (isset($_SESSION["username"])) {
    if (str_starts_with($_SESSION["username"], "@admin")) {
        header("Location: ../../../ADMIN/MVC/php/admindashboard.php");
    } elseif (str_starts_with($_SESSION["username"], "@vendor")) {
        header("Location: ../../../VENDOR/MVC/php/vendordashboard.php");
    } elseif (str_starts_with($_SESSION["username"], "@staff")) {
        header("Location: ../../../STAFF/MVC/php/staffdashboard.php");
    } else {
        header("Location: ../php/index.php");
    }
    exit();
}

include "../Db/dbregister.php";
/** @var mysqli $conn (procedural mysqli connection from dbregister.php) */

$username = "";
$usernameError = $passwordError = "";
$successMessage = $errorMessage = "";

function test_input($data) {
    return trim($data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["username"])) {
        $usernameError = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordError = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }

    if (empty($usernameError) && empty($passwordError)) {

        // Which table/role does this username belong to?
        if (str_starts_with($username, "@admin")) {
            $table = "admin";
            $redirect = "../../../ADMIN/MVC/php/admindashboard.php";
            $roleLabel = "Admin";
        } elseif (str_starts_with($username, "@vendor")) {
            $table = "vendors";
            $redirect = "../../../VENDOR/MVC/php/vendordashboard.php";
            $roleLabel = "Vendor";
        } elseif (str_starts_with($username, "@staff")) {
            $table = "staff";
            $redirect = "../../../STAFF/MVC/php/staffdashboard.php";
            $roleLabel = "Staff";
        } else {
            $table = "registereduser";
            $redirect = "../php/index.php";
            $roleLabel = "User";
        }

        // Procedural mysqli + prepared statement -> no SQL injection possible
        $sql = "SELECT * FROM `$table` WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // All roles (including admin) now use hashed passwords
            if (password_verify($password, $row['password'])) {

                $_SESSION["username"] = $username;
                setcookie("username", $username, time() + 86400, "/");

                $successMessage = "$roleLabel login successful! Redirecting...";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = '$redirect';
                    }, 1000);
                </script>";

            } else {
                $errorMessage = "Invalid password";
            }
        } else {
            $errorMessage = "Username not found";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../Css/theme.css">
    <link rel="stylesheet" href="../Css/login.css">
</head>
<body>

<div class="container">
    <h1>Login</h1>

    <?php
    if (!empty($loginRedirectMsg)) {
    echo "<p class='errormsg'>$loginRedirectMsg</p>";}
    ?>

    <form method="post" action="" id="loginForm" novalidate>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>">
            <span class="error" id="usernameError"><?php echo $usernameError; ?></span>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password">
            <span class="error" id="passwordError"><?php echo $passwordError; ?></span>
        </div>

        <button type="submit">Login</button>
    </form>

    <?php
    if (!empty($successMessage)) {
        echo "<p class='successmsg'>$successMessage</p>";
    }

    if (!empty($errorMessage)) {
        echo "<p class='errormsg'>$errorMessage</p>";
    }
    ?>

    <p style="margin-top:8px;">
        <a href="forgot_password.php">Forgot Password?</a>
    </p>

    <p class="register-text">
        Not registered yet? <a href="../php/register.php">Register here</a>
    </p>

    <p class="register-text">
        Are you a vendor? <a href="../../../VENDOR/MVC/php/vendorregister.php">Register as Vendor</a>
    </p>

    <p class="register-text">
        Are you staff? <a href="../../../STAFF/MVC/php/staffregister.php">Register as Staff</a>
    </p>
</div>

<script src="../Js/login.js"></script>
</body>
</html>
