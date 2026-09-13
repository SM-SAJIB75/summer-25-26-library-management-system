<?php
$host = "localhost";
$registereduser = "root";
$pass = "";
$dbname = "Bookstore";

$conn = mysqli_connect($host, $registereduser, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
