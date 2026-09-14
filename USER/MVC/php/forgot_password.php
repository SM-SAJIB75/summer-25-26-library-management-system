<?php
session_start();
include "../Db/dbregister.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? "");
    $newPass  = trim($_POST['new_password'] ?? "");

    if ($username == "" || $newPass == "") {
        $msg = "All fields are required!";
    } else {

        // Decide table the same way login.php does
        if (str_starts_with($username, "@admin")) {
            $table = "admin";
        } elseif (str_starts_with($username, "@vendor")) {
            $table = "vendors";
        } elseif (str_starts_with($username, "@staff")) {
            $table = "staff";
        } else {
            $table = "registereduser";
        }

        // Check user exists (procedural + prepared statement)
        $check = mysqli_prepare($conn, "SELECT username FROM `$table` WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        $res = mysqli_stmt_get_result($check);

        if ($res && mysqli_num_rows($res) == 1) {

            // All roles now use hashed passwords (see login.php)
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);

            $update = mysqli_prepare($conn, "UPDATE `$table` SET password = ? WHERE username = ?");
            mysqli_stmt_bind_param($update, "ss", $hashed, $username);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);

            $msg = "Password updated successfully!";
        } else {
            $msg = "Username not found!";
        }
        mysqli_stmt_close($check);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../Css/theme.css">
    <link rel="stylesheet" href="../Css/login.css">
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>

    <form method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="new_password" placeholder="New Password">
        <button type="submit">Reset Password</button>
    </form>

    <p><?php echo $msg; ?></p>
    <p><a href="login.php">Back to Login</a></p>
</div>
</body>
</html>
