<?php
include "../Db/dbregister.php";
/** @var mysqli $conn */

$username = $password = "";
$usernameError = $passwordError = "";
$success = $error = "";
$valid_username = "";

function test_input($data) {
    return trim($data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (empty($_POST["username"])) {
        $usernameError = "Username is required";
    } else {
        $username = test_input($_POST["username"]);

        if (!preg_match("/^[a-zA-Z]/", $username)) {
            $usernameError = "Username must start with a letter";
        } elseif (!preg_match("/^[a-zA-Z .\-]+$/", $username)) {
            $usernameError = "Only letters, dot, dash allowed";
        } elseif (str_word_count($username) < 2) {
            $usernameError = "Username must contain at least two words";
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

    if (empty($usernameError) && empty($passwordError)) {

        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO registereduser (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $hashPassword);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Registration complete. Redirecting to login page...";
            $valid_username = $username;
            $username = $password = "";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="../Css/theme.css">
    <link rel="stylesheet" href="../Css/register.css">
</head>
<body>

<h1 align="center">Registration</h1>

<form method="post" id="registerForm" novalidate>
    <p>
        Username:<br>
        <input type="text" name="username" id="reg_username" value="<?php echo $username; ?>">
        <span class="error" id="reg_usernameError"><?php echo $usernameError; ?></span>
    </p>

    <p>
        Password:<br>
        <input type="password" name="password" id="reg_password">
        <span class="error" id="reg_passwordError"><?php echo $passwordError; ?></span>
    </p>

    <input type="submit" value="Register Now">
</form>

<?php
if (!empty($valid_username)) {
    echo '<div class="output">';
    echo "<h3>Your Input:</h3>";
    echo "Username: " . $valid_username . "<br>";
    echo '</div>';
}
?>

<p class="success" id="success-msg"><?php echo $success; ?></p>
<p class="error"><?php echo $error; ?></p>

<script>
window.onload = function() {
    var successMsg = document.getElementById("success-msg");
    if (successMsg && successMsg.textContent.trim() !== "") {
        setTimeout(function() {
            window.location.href = '../php/login.php';
        }, 2000);
    }
};
</script>
<script src="../Js/register.js"></script>

</body>
</html>
