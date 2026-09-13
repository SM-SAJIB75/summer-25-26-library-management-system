<?php
session_start();
include "../../../USER/MVC/Db/dbregister.php";

/* Check admin login */
if (!isset($_SESSION['username']) || !str_starts_with($_SESSION['username'], '@admin')) {
    header("Location: ../../USER/MVC/php/login.php");
    exit();
}


$sql = "SELECT username, password FROM registereduser";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - View Users</title>
    <link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
    <link rel="stylesheet" href="../Css/viewuser.css">
</head>
<body>
    <h2 class="title">Registered Users</h2>

    <table>
        <tr>
            <th>Username</th>
            <th>Hashed Password</th>
        </tr>

        <?php
        if ($result && mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td class='hash'>" . $row['password'] . "</td>";
                echo "</tr>";
            }

        } else {
            echo "<tr>";
            echo "<td colspan='2' class='no-data'>No users found</td>";
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>