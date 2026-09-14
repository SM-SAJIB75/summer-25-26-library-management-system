<?php
session_start();
include("../../../USER/MVC/Db/dbregister.php");

if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@admin")) {
    header("Location: ../../../USER/MVC/php/login.php");
    exit();
}

// Handle approve/reject action
if (isset($_GET["action"]) && isset($_GET["id"])) {
    $book_id = (int)$_GET["id"];
    $action  = $_GET["action"];

    if ($action === "approve" || $action === "reject") {
        $newStatus = $action === "approve" ? "approved" : "rejected";
        $stmt = mysqli_prepare($conn, "UPDATE books SET approval_status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $newStatus, $book_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: adminvendorbooks.php");
    exit();
}

$sql = "SELECT b.id, b.title, b.author, b.price, b.quantity, b.approval_status, v.vendor_name 
        FROM books b 
        JOIN vendors v ON b.vendor_id = v.vendor_id 
        ORDER BY (b.approval_status = 'pending') DESC, b.id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vendor Books</title>
    <link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
    <link rel="stylesheet" href="../../../USER/MVC/Css/order_history.css">
</head>
<body>

<div class="container">
    <h2 class="page-title">Vendor Book Submissions</h2>
    <p style="text-align:center;margin-bottom:20px;"><a href="admindashboard.php">Back to Dashboard</a></p>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <p class="empty">No vendor-submitted books yet.</p>
    <?php endif; ?>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="order-card">
            <div class="order-head">
                <div>
                    <h3><?php echo htmlspecialchars($row["title"]); ?> by <?php echo htmlspecialchars($row["author"]); ?></h3>
                    <p><b>Vendor:</b> <?php echo htmlspecialchars($row["vendor_name"]); ?> &nbsp; | &nbsp;
                       <b>Price:</b> ৳<?php echo number_format($row["price"], 2); ?> &nbsp; | &nbsp;
                       <b>Qty:</b> <?php echo $row["quantity"]; ?></p>
                    <p><b>Status:</b>
                        <strong style="color:
                            <?php
                                echo $row["approval_status"] === "approved" ? "green" :
                                     ($row["approval_status"] === "rejected" ? "red" : "orange");
                            ?>;">
                            <?php echo ucfirst($row["approval_status"]); ?>
                        </strong>
                    </p>
                </div>

                <?php if ($row["approval_status"] === "pending"): ?>
                    <div style="display:flex;gap:10px;">
                        <a class="toggle-btn" style="background:#27ae60;" href="adminvendorbooks.php?action=approve&id=<?php echo $row['id']; ?>">Approve</a>
                        <a class="toggle-btn" style="background:#e74c3c;" href="adminvendorbooks.php?action=reject&id=<?php echo $row['id']; ?>">Reject</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
