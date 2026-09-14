<?php
session_start();
include "../../../USER/MVC/Db/dbregister.php";

// Protect page - must be logged in as vendor
if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@vendor")) {
    header("Location: ../../../USER/MVC/php/login.php");
    exit();
}

$username = $_SESSION["username"];

// Get vendor info
$vStmt = mysqli_prepare($conn, "SELECT vendor_id, vendor_name FROM vendors WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($vStmt, "s", $username);
mysqli_stmt_execute($vStmt);
$vRes = mysqli_stmt_get_result($vStmt);
$vendor = mysqli_fetch_assoc($vRes);
$vendor_id   = $vendor["vendor_id"];
$vendor_name = $vendor["vendor_name"];
mysqli_stmt_close($vStmt);

// Delete own book (ownership enforced via vendor_id in WHERE)
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $delStmt = mysqli_prepare($conn, "DELETE FROM books WHERE id = ? AND vendor_id = ?");
    mysqli_stmt_bind_param($delStmt, "ii", $delId, $vendor_id);
    mysqli_stmt_execute($delStmt);
    mysqli_stmt_close($delStmt);
    header("Location: vendordashboard.php");
    exit();
}

// Get categories for the add-book dropdown
$cats = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");

// Get this vendor's own submitted books (with search)
$search = isset($_GET['q']) ? trim($_GET['q']) : "";

if ($search !== "") {
    $like = "%$search%";
    $bStmt = mysqli_prepare($conn, "SELECT id, title, author, price, quantity, approval_status FROM books WHERE vendor_id = ? AND (title LIKE ? OR author LIKE ?) ORDER BY id DESC");
    mysqli_stmt_bind_param($bStmt, "iss", $vendor_id, $like, $like);
} else {
    $bStmt = mysqli_prepare($conn, "SELECT id, title, author, price, quantity, approval_status FROM books WHERE vendor_id = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($bStmt, "i", $vendor_id);
}
mysqli_stmt_execute($bStmt);
$myBooks = mysqli_stmt_get_result($bStmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Vendor Dashboard</title>
<link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
<link rel="stylesheet" href="../../../USER/MVC/Css/dashboard.css">
<link rel="stylesheet" href="../../../USER/MVC/Css/booklist.css">
</head>
<body>

<div class="header">
    <div class="logo"><span>Vendor Panel</span></div>
    <div class="nav">
        <a href="vendordashboard.php">Dashboard</a>
        <a href="../../../USER/MVC/php/logout.php">Logout</a>
    </div>
</div>

<div class="welcome-text">Welcome, <?php echo htmlspecialchars($vendor_name); ?></div>

<?php if (isset($_GET['added'])): ?>
    <p style="text-align:center;color:green;font-weight:bold;">Book submitted for admin approval!</p>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
    <p style="text-align:center;color:green;font-weight:bold;">Book updated — sent for admin re-approval!</p>
<?php endif; ?>

<!-- ADD NEW BOOK FORM -->
<div style="max-width:600px;margin:20px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom:15px;">Add New Book</h2>
    <form method="POST" action="vendoraddbook.php" enctype="multipart/form-data">
        <label>Title</label><br>
        <input type="text" name="title" required style="width:100%;padding:8px;margin-bottom:10px;"><br>

        <label>Author</label><br>
        <input type="text" name="author" required style="width:100%;padding:8px;margin-bottom:10px;"><br>

        <label>Category</label><br>
        <select name="category_id" required style="width:100%;padding:8px;margin-bottom:10px;">
            <?php while ($cat = mysqli_fetch_assoc($cats)) { ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php } ?>
        </select><br>

        <label>Price</label><br>
        <input type="number" step="0.01" name="price" required style="width:100%;padding:8px;margin-bottom:10px;"><br>

        <label>Quantity</label><br>
        <input type="number" name="quantity" required style="width:100%;padding:8px;margin-bottom:10px;"><br>

        <label>Description</label><br>
        <textarea name="description" style="width:100%;padding:8px;margin-bottom:10px;"></textarea><br>

        <label>Book Image</label><br>
        <input type="file" name="image" accept="image/*" style="margin-bottom:15px;"><br>

        <button type="submit" class="cart-btn" style="width:100%;">Submit for Approval</button>
    </form>
</div>

<!-- MY SUBMITTED BOOKS -->
<h2 class="page-title">My Books</h2>

<form method="get" class="search-bar" style="max-width:500px;margin:0 auto 20px;">
    <input type="text" name="q" placeholder="Search my books by title/author..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<div class="book-grid">
    <?php if (mysqli_num_rows($myBooks) === 0): ?>
        <p style="text-align:center;width:100%;">No books found.</p>
    <?php endif; ?>

    <?php while ($book = mysqli_fetch_assoc($myBooks)): ?>
        <div class="book">
            <h3><?php echo htmlspecialchars($book["title"]); ?></h3>
            <p><?php echo htmlspecialchars($book["author"]); ?></p>
            <p class="price">৳<?php echo number_format($book["price"], 2); ?></p>
            <p>Qty: <?php echo $book["quantity"]; ?></p>
            <p class="status" style="
                color: <?php
                    echo $book["approval_status"] === "approved" ? "green" :
                         ($book["approval_status"] === "rejected" ? "red" : "orange");
                ?>;
            ">
                <?php echo ucfirst($book["approval_status"]); ?>
            </p>
            <div style="display:flex;gap:10px;justify-content:center;margin-top:8px;">
                <a href="vendoreditbook.php?id=<?php echo $book['id']; ?>">Edit</a>
                <a href="vendordashboard.php?delete=<?php echo $book['id']; ?>" onclick="return confirm('Delete this book?')" style="color:#C0392B;">Delete</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
