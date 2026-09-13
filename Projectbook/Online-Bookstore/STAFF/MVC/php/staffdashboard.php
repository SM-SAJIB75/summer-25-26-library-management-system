<?php
session_start();
include "../../../USER/MVC/Db/dbregister.php";
include "../Model/StaffModel.php";

if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@staff")) {
    header("Location: ../../../USER/MVC/php/login.php");
    exit();
}

$username = $_SESSION["username"];

$stmt = mysqli_prepare($conn, "SELECT staff_name, designation FROM staff WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$staff = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

$openTickets   = count(array_filter(staff_get_tickets($conn), fn($t) => $t['status'] !== 'Resolved'));
$pendingShip   = count(array_filter(staff_get_shipments($conn), fn($s) => $s['tracking_status'] !== 'Delivered'));
$pendingReview = count(array_filter(staff_get_reviews($conn), fn($r) => $r['approval_status'] === 'Pending'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Dashboard</title>
<link rel="stylesheet" href="../Css/staff.css">
</head>
<body>

<div class="header">
    <div class="logo"><span>Staff Panel</span></div>
    <div class="nav">
        <span>Welcome, <?php echo htmlspecialchars($staff['staff_name'] ?? $username); ?></span>
        <a href="../../../USER/MVC/php/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard-cards">
    <a class="card" href="supporttickets.php">
        <h3>Customer Support Tickets</h3>
        <p><?php echo $openTickets; ?> open ticket(s)</p>
    </a>
    <a class="card" href="shipments.php">
        <h3>Shipment Tracking</h3>
        <p><?php echo $pendingShip; ?> shipment(s) in progress</p>
    </a>
    <a class="card" href="reviews.php">
        <h3>Review Moderation</h3>
        <p><?php echo $pendingReview; ?> review(s) pending</p>
    </a>
</div>

</body>
</html>
