<?php
include "../../../USER/MVC/Db/dbregister.php";

$rawUsername = $password = $staff_name = "";
$usernameError = $passwordError = $nameError = "";
$success = $error = "";

function test_input($data) {
    return trim($data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $staff_name = test_input($_POST["staff_name"] ?? "");
    if (empty($staff_name)) {
        $nameError = "Full name is required";
    }

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

    if (empty($usernameError) && empty($passwordError) && empty($nameError)) {

        // Prefix so login.php routes it to the staff table (same pattern as @admin / @vendor)
        $finalUsername = "@staff_" . $rawUsername;

        $check = mysqli_prepare($conn, "SELECT staff_id FROM staff WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $finalUsername);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "This staff username is already taken.";
        } else {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO staff (username, password, staff_name) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $finalUsername, $hashPassword, $staff_name);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration complete! Your login username is: " . $finalUsername . " . Redirecting to login...";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Registration</title>
<link rel="stylesheet" href="../Css/staff.css">
</head>
<body>

<div class="container">
    <h1>Staff Registration</h1>

    <form method="post" id="staffRegForm" novalidate>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="staff_name" id="staff_name" value="<?php echo htmlspecialchars($staff_name); ?>">
            <span class="error" id="nameError"><?php echo $nameError; ?></span>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" id="staff_username" value="<?php echo htmlspecialchars($rawUsername); ?>">
            <span class="error" id="usernameError"><?php echo $usernameError; ?></span>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="staff_password">
            <span class="error" id="passwordError"><?php echo $passwordError; ?></span>
        </div>

        <button type="submit">Register</button>
    </form>

    <?php if (!empty($success)): ?>
        <p class="successmsg" id="success-msg"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="errormsg"><?php echo $error; ?></p>
    <?php endif; ?>

    <p class="register-text">
        Already registered? <a href="../../../USER/MVC/php/login.php">Login here</a>
    </p>
</div>

<script>
window.onload = function () {
    var successMsg = document.getElementById("success-msg");
    if (successMsg && successMsg.textContent.trim() !== "") {
        setTimeout(function () {
            window.location.href = "../../../USER/MVC/php/login.php";
        }, 2000);
    }
};
</script>
<script src="../Js/staff.js"></script>
</body>
</html>
