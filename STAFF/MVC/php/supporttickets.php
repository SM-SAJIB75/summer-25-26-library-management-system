<?php
session_start();
include "../../../USER/MVC/Db/dbregister.php";
include "../Model/StaffModel.php";

if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@staff")) {
    header("Location: ../../../USER/MVC/php/login.php");
    exit();
}
$staffUser = $_SESSION["username"];

// CREATE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "create") {
    staff_create_ticket($conn, trim($_POST["customer_username"]), trim($_POST["subject"]), trim($_POST["message"]));
    header("Location: supporttickets.php");
    exit();
}

// UPDATE (status)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update") {
    staff_update_ticket($conn, (int)$_POST["ticket_id"], $_POST["status"], $staffUser);
    header("Location: supporttickets.php");
    exit();
}

// DELETE
if (isset($_GET["delete"])) {
    staff_delete_ticket($conn, (int)$_GET["delete"]);
    header("Location: supporttickets.php");
    exit();
}

// READ + SEARCH
$search = isset($_GET["q"]) ? trim($_GET["q"]) : "";
$tickets = staff_get_tickets($conn, $search);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Support Tickets</title>
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
    <h2>Customer Support Tickets</h2>

    <form method="get" class="search-bar" id="ticketSearchForm">
        <input type="text" name="q" id="ticketSearch" placeholder="Search by subject or customer..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <details class="create-box">
        <summary>+ Log a new ticket</summary>
        <form method="post">
            <input type="hidden" name="action" value="create">
            <input type="text" name="customer_username" placeholder="Customer username" required>
            <input type="text" name="subject" placeholder="Subject" required>
            <textarea name="message" placeholder="Message" required></textarea>
            <button type="submit">Add Ticket</button>
        </form>
    </details>

    <table>
        <tr>
            <th>ID</th><th>Customer</th><th>Subject</th><th>Message</th><th>Status</th><th>Handled By</th><th>Created</th><th>Actions</th>
        </tr>
        <?php if (empty($tickets)): ?>
        <tr><td colspan="8">No tickets found.</td></tr>
        <?php endif; ?>
        <?php foreach ($tickets as $t): ?>
        <tr>
            <td><?php echo $t['ticket_id']; ?></td>
            <td><?php echo htmlspecialchars($t['customer_username']); ?></td>
            <td><?php echo htmlspecialchars($t['subject']); ?></td>
            <td><?php echo htmlspecialchars($t['message']); ?></td>
            <td>
                <form method="post" class="inline-form">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="ticket_id" value="<?php echo $t['ticket_id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                        <?php foreach (["Open","In Progress","Resolved"] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $t['status'] == $s ? "selected" : ""; ?>><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td><?php echo htmlspecialchars($t['handled_by'] ?? '-'); ?></td>
            <td><?php echo $t['created_at']; ?></td>
            <td><a href="?delete=<?php echo $t['ticket_id']; ?>" onclick="return confirm('Delete this ticket?')">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script src="../Js/staff.js"></script>
</body>
</html>
