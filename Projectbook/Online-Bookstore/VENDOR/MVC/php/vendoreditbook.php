<?php
session_start();
include "../../../USER/MVC/Db/dbregister.php";

if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@vendor")) {
    header("Location: ../../../USER/MVC/php/login.php");
    exit();
}
$username = $_SESSION["username"];

$vStmt = mysqli_prepare($conn, "SELECT vendor_id FROM vendors WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($vStmt, "s", $username);
mysqli_stmt_execute($vStmt);
$vendor_id = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($vStmt))['vendor_id'];
mysqli_stmt_close($vStmt);

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Ownership check: this book must belong to this vendor
$check = mysqli_prepare($conn, "SELECT * FROM books WHERE id = ? AND vendor_id = ? LIMIT 1");
mysqli_stmt_bind_param($check, "ii", $id, $vendor_id);
mysqli_stmt_execute($check);
$book = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
mysqli_stmt_close($check);

if (!$book) {
    header("Location: vendordashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = trim($_POST['title']);
    $author      = trim($_POST['author']);
    $price       = (float)$_POST['price'];
    $quantity    = (int)$_POST['quantity'];
    $description = trim($_POST['description']);

    if ($title === "" || $author === "" || $price <= 0 || $quantity < 0) {
        $error = "Please fill all fields correctly.";
    } else {
        // Editing sends it back for admin re-approval
        $stmt = mysqli_prepare($conn, "UPDATE books SET title=?, author=?, price=?, final_price=?, quantity=?, description=?, approval_status='pending' WHERE id=? AND vendor_id=?");
        mysqli_stmt_bind_param($stmt, "ssddisii", $title, $author, $price, $price, $quantity, $description, $id, $vendor_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: vendordashboard.php?updated=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Book</title>
<link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
</head>
<body>

<div class="content" style="max-width:500px;margin:40px auto;padding:20px;">
    <h2>Edit Book</h2>

    <?php if ($error): ?><p class="errormsg"><?php echo $error; ?></p><?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" style="width:100%;">
        </div>
        <div class="form-group">
            <label>Author</label>
            <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" style="width:100%;">
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="<?php echo $book['price']; ?>" style="width:100%;">
        </div>
        <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="quantity" value="<?php echo $book['quantity']; ?>" style="width:100%;">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" style="width:100%;"><?php echo htmlspecialchars($book['description']); ?></textarea>
        </div>
        <p style="font-size:13px;color:#6B7280;">Note: editing sends this book back to Admin for re-approval.</p>
        <button type="submit">Save Changes</button>
        <a href="vendordashboard.php" style="margin-left:10px;">Cancel</a>
    </form>
</div>

</body>
</html>
