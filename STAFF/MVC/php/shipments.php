<?php
session_start();
include "../../../USER/MVC/Db/dbregister.php";
include "../Model/StaffModel.php";

if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@staff")) {
    header("Location: ../../../USER/MVC/php/login.php");
    exit();
}
$staffUser = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["action"] ?? "") == "create") {
    staff_create_shipment($conn, (int)$_POST["order_id"], trim($_POST["courier_name"]), $_POST["tracking_status"]);
    header("Location: shipments.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["action"] ?? "") == "update") {
    staff_update_shipment($conn, (int)$_POST["shipment_id"], $_POST["tracking_status"], $staffUser);
    header("Location: shipments.php");
    exit();
}

if (isset($_GET["delete"])) {
    staff_delete_shipment($conn, (int)$_GET["delete"]);
    header("Location: shipments.php");
    exit();
}

$search = isset($_GET["q"]) ? trim($_GET["q"]) : "";
$shipments = staff_get_shipments($conn, $search);
$statuses = ["Pending","Shipped","In Transit","Delivered","Returned"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Shipment Tracking</title>
<link rel="stylesheet" href="../Css/staff.css">
</head>
<body>

<div class="header">
    <div class="logo"><span>Staff Panel</span></div>
    <div class="nav">
        <a href="staffdashboard.php">Dashboard</a>
        <a href="../../../USER/MVC/php/logout.php">Logout</a>
    </div>
</div>

<div class="content">
    <h2>Shipment Tracking</h2>

    <form method="get" class="search-bar">
        <input type="text" name="q" placeholder="Search by courier or status..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <details class="create-box">
        <summary>+ Add shipment record</summary>
        <form method="post">
            <input type="hidden" name="action" value="create">
            <input type="number" name="order_id" placeholder="Order ID" required>
            <input type="text" name="courier_name" placeholder="Courier name" required>
            <select name="tracking_status">
                <?php foreach ($statuses as $s): ?>
                <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Add</button>
        </form>
    </details>

    <table>
        <tr><th>ID</th><th>Order ID</th><th>Courier</th><th>Status</th><th>Updated By</th><th>Updated At</th><th>Actions</th></tr>
        <?php if (empty($shipments)): ?>
        <tr><td colspan="7">No shipments found.</td></tr>
        <?php endif; ?>
        <?php foreach ($shipments as $s): ?>
        <tr>
            <td><?php echo $s['shipment_id']; ?></td>
            <td><?php echo $s['order_id']; ?></td>
            <td><?php echo htmlspecialchars($s['courier_name']); ?></td>
            <td>
                <form method="post" class="inline-form">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="shipment_id" value="<?php echo $s['shipment_id']; ?>">
                    <select name="tracking_status" onchange="this.form.submit()">
                        <?php foreach ($statuses as $st): ?>
                        <option value="<?php echo $st; ?>" <?php echo $s['tracking_status'] == $st ? "selected" : ""; ?>><?php echo $st; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td><?php echo htmlspecialchars($s['updated_by'] ?? '-'); ?></td>
            <td><?php echo $s['updated_at']; ?></td>
            <td><a href="?delete=<?php echo $s['shipment_id']; ?>" onclick="return confirm('Delete this record?')">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script src="../Js/staff.js"></script>
</body>
</html>
