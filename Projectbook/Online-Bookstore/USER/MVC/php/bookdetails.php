<?php
session_start();
include("../Db/dbregister.php");

/* Check if id exists + numeric */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: booklist.php");
    exit();
}

$id = (int)$_GET['id'];

/* Procedural + prepared statement */
$stmt = mysqli_prepare($conn, "SELECT * FROM books WHERE id = ? AND status = 'available' LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$book = ($result && mysqli_num_rows($result) == 1)
        ? mysqli_fetch_assoc($result)
        : null;
mysqli_stmt_close($stmt);

if (!$book) {
    header("Location: booklist.php");
    exit();
}

/* Decide availability */
$isAvailable = (strtolower($book['status']) === "available");
$inStock = ((int)$book['quantity'] > 0);
$canBuy = $isAvailable && $inStock;

/* Is a logged-in regular customer (not admin/vendor/staff)? */
$loggedUser = $_SESSION['username'] ?? "";
$isCustomer = !empty($loggedUser)
    && !str_starts_with($loggedUser, "@admin")
    && !str_starts_with($loggedUser, "@vendor")
    && !str_starts_with($loggedUser, "@staff");

$reviewError = "";
$reviewSuccess = "";

/* Handle new review submission */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_review"])) {
    if (!$isCustomer) {
        $reviewError = "Please login as a customer to leave a review.";
    } else {
        $rating  = (int)($_POST["rating"] ?? 0);
        $comment = trim($_POST["comment"] ?? "");

        if ($rating < 1 || $rating > 5) {
            $reviewError = "Please select a rating between 1 and 5.";
        } elseif ($comment === "") {
            $reviewError = "Please write a short comment.";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO reviews (book_id, customer_username, rating, comment) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isis", $id, $loggedUser, $rating, $comment);
            if (mysqli_stmt_execute($stmt)) {
                $reviewSuccess = "Thanks! Your review has been submitted for approval.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

/* Fetch approved reviews for this book */
$revStmt = mysqli_prepare($conn, "SELECT customer_username, rating, comment, created_at FROM reviews WHERE book_id = ? AND approval_status = 'Approved' ORDER BY created_at DESC");
mysqli_stmt_bind_param($revStmt, "i", $id);
mysqli_stmt_execute($revStmt);
$reviews = mysqli_fetch_all(mysqli_stmt_get_result($revStmt), MYSQLI_ASSOC);
mysqli_stmt_close($revStmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($book['title']); ?> - Details</title>
    <link rel="stylesheet" href="../Css/theme.css">
    <link rel="stylesheet" href="../Css/bookdetails.css">
</head>
<body>

<a href="booklist.php" class="back-link">← Back to Book List</a>

<div class="details-container">

    <div class="book-image">
        <img src="../Picture/<?php echo htmlspecialchars($book['image']); ?>"
             alt="<?php echo htmlspecialchars($book['title']); ?>">
    </div>

    <div class="book-info">
        <h2><?php echo htmlspecialchars($book['title']); ?></h2>

        <p><b>Author:</b> <?php echo htmlspecialchars($book['author']); ?></p>

        <!-- Price -->
        <?php if ((float)$book['discount'] > 0) { ?>
            <p>
                <b>Original Price:</b>
                <span style="text-decoration: line-through; color:#888;">
                    ৳<?php echo number_format((float)$book['price'], 2); ?>
                </span>
            </p>

            <p><b>Discount:</b> <?php echo (int)$book['discount']; ?>%</p>

            <p class="final-price">
                <b>Final Price:</b>
                ৳<?php echo number_format((float)$book['final_price'], 2); ?>
            </p>
        <?php } else { ?>
            <p class="final-price">
                <b>Price:</b>
                ৳<?php echo number_format((float)$book['final_price'], 2); ?>
            </p>
        <?php } ?>

        <p><b>Status:</b> <?php echo htmlspecialchars($book['status']); ?></p>
        <p><b>Stock:</b> <?php echo (int)$book['quantity']; ?></p>

        <p class="description">
            <b>Description:</b><br>
            <?php echo nl2br(htmlspecialchars($book['description'])); ?>
        </p>

        <!-- ADD TO CART -->
        <form onsubmit="return addToCart(this);">
            <input type="hidden" name="id" value="<?php echo (int)$book['id']; ?>">
            <input type="hidden" name="title" value="<?php echo htmlspecialchars($book['title']); ?>">
            <input type="hidden" name="price" value="<?php echo (float)$book['final_price']; ?>">

            <button type="submit" class="cart-btn" <?php echo (!$canBuy ? "disabled" : ""); ?>>
                <?php echo $canBuy ? "Add to Cart" : "Out of Stock"; ?>
            </button>

            <p class="cartMsg" style="margin-top:6px; font-size:13px;"></p>
        </form>

    </div>
</div>

<!-- REVIEWS -->
<div class="details-container" style="margin-top:0;">
    <div style="width:100%;">
        <h3>Customer Reviews</h3>

        <?php if (empty($reviews)): ?>
            <p style="color:#6B7280;">No reviews yet — be the first to review this book.</p>
        <?php else: ?>
            <?php foreach ($reviews as $r): ?>
                <div style="border-bottom:1px solid #E3E6EC;padding:10px 0;">
                    <b><?php echo htmlspecialchars($r['customer_username']); ?></b>
                    — <?php echo str_repeat("★", (int)$r['rating']) . str_repeat("☆", 5 - (int)$r['rating']); ?>
                    <p style="margin:4px 0;"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h4 style="margin-top:20px;">Leave a Review</h4>

        <?php if ($reviewSuccess): ?>
            <p class="successmsg"><?php echo htmlspecialchars($reviewSuccess); ?></p>
        <?php elseif ($reviewError): ?>
            <p class="errormsg"><?php echo htmlspecialchars($reviewError); ?></p>
        <?php endif; ?>

        <?php if ($isCustomer): ?>
            <form method="post" style="max-width:400px;">
                <div class="form-group" style="text-align:left;">
                    <label>Rating</label>
                    <select name="rating" required>
                        <option value="">Select</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Very Poor</option>
                    </select>
                </div>
                <div class="form-group" style="text-align:left;">
                    <label>Comment</label>
                    <textarea name="comment" rows="3" style="width:100%;" required></textarea>
                </div>
                <button type="submit" name="submit_review" value="1">Submit Review</button>
            </form>
        <?php else: ?>
            <p style="color:#6B7280;"><a href="login.php">Login</a> as a customer to leave a review.</p>
        <?php endif; ?>
    </div>
</div>

<script src="../Js/booklistajax.js"></script>
</body>
</html>