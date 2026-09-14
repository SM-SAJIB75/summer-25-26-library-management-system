<<<<<<< HEAD
=======
<?php
session_start();
include "../../../USER/MVC/Db/dbregister.php";
include "../Model/StaffModel.php";
//Farhan
if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@staff")) {
    header("Location: ../../../USER/MVC/php/login.php");
    exit();
}
$staffUser = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["action"] ?? "") == "moderate") {
    staff_moderate_review($conn, (int)$_POST["review_id"], $_POST["approval_status"], $staffUser);
    header("Location: reviews.php");
    exit();
}

if (isset($_GET["delete"])) {
    staff_delete_review($conn, (int)$_GET["delete"]);
    header("Location: reviews.php");
    exit();
}

$search = isset($_GET["q"]) ? trim($_GET["q"]) : "";
$reviews = staff_get_reviews($conn, $search);
$statuses = ["Pending","Approved","Rejected"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Review Moderation</title>
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
    <h2>Book Review Moderation</h2>

    <form method="get" class="search-bar">
        <input type="text" name="q" placeholder="Search by customer or comment..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr><th>ID</th><th>Book ID</th><th>Customer</th><th>Rating</th><th>Comment</th><th>Status</th><th>Moderated By</th><th>Actions</th></tr>
        <?php if (empty($reviews)): ?>
        <tr><td colspan="8">No reviews found.</td></tr>
        <?php endif; ?>
        <?php foreach ($reviews as $r): ?>
        <tr>
            <td><?php echo $r['review_id']; ?></td>
            <td><?php echo $r['book_id']; ?></td>
            <td><?php echo htmlspecialchars($r['customer_username']); ?></td>
            <td><?php echo $r['rating']; ?>/5</td>
            <td><?php echo htmlspecialchars($r['comment']); ?></td>
            <td>
                <form method="post" class="inline-form">
                    <input type="hidden" name="action" value="moderate">
                    <input type="hidden" name="review_id" value="<?php echo $r['review_id']; ?>">
                    <select name="approval_status" onchange="this.form.submit()">
                        <?php foreach ($statuses as $st): ?>
                        <option value="<?php echo $st; ?>" <?php echo $r['approval_status'] == $st ? "selected" : ""; ?>><?php echo $st; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td><?php echo htmlspecialchars($r['moderated_by'] ?? '-'); ?></td>
            <td><a href="?delete=<?php echo $r['review_id']; ?>" onclick="return confirm('Delete this review?')">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script src="../Js/staff.js"></script>
</body>
</html>
>>>>>>> a57efd6d448d004353295a51670f4b004131158f
