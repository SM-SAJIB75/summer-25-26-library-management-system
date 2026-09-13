<?php 
session_start();
include "../Db/dbregister.php";

/* Latest Arrivals (last 6 books) */
$latestBooks = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT 6");

/* Discounted Books (discount > 0) */
$discountBooks = mysqli_query($conn, "SELECT * FROM books WHERE discount > 0 ORDER BY discount DESC LIMIT 6");

/* Popular Books (based on orders, NOT sold_count) */
$popularBooks = mysqli_query($conn, "
    SELECT 
        b.id,
        b.title,
        b.price,
        b.image,
        COUNT(oi.book_id) AS total_sold
    FROM books b
    LEFT JOIN order_items oi ON oi.book_id = b.id
    GROUP BY b.id
    ORDER BY total_sold DESC
    LIMIT 6
");

$loggedUser = "";
if (isset($_SESSION["username"])) {
    $loggedUser = $_SESSION["username"];
} elseif (isset($_COOKIE["username"])) {
    $loggedUser = $_COOKIE["username"];
}

// Redirect admin trying to access user dashboard
if (!empty($loggedUser) && str_starts_with($loggedUser, "@admin")) {
    header("Location: ../../../ADMIN/MVC/php/admindashboard.php");
    exit();
}

// Redirect vendor trying to access user dashboard
if (!empty($loggedUser) && str_starts_with($loggedUser, "@vendor")) {
    header("Location: ../../../VENDOR/MVC/php/vendordashboard.php");
    exit();
}

// Redirect staff trying to access user dashboard
if (!empty($loggedUser) && str_starts_with($loggedUser, "@staff")) {
    header("Location: ../../../STAFF/MVC/php/staffdashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore Management System</title>
    <link rel="stylesheet" href="../Css/theme.css">
    <link rel="stylesheet" href="../Css/dashboard.css?v=1.3">
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="logo">
        <img src="../Picture/logo.jpg" alt="BookZone Logo">
        <span>BookZone</span>
    </div>

    <?php if (!empty($loggedUser)) { ?>
        <h2 class="welcome-text">
            Welcome, <?php echo $loggedUser; ?>!!
        </h2>
    <?php } ?>

    <nav class="nav">
    <a href="../php/index.php">Home</a>
    <a href="../php/booklist.php">Books</a>

    <?php if (empty($loggedUser)) { ?>
        <a href="../php/login.php">Login</a>
    <?php } else { ?>

        <!-- Show only for normal users -->
        <?php if (!str_starts_with($loggedUser, "@admin")) { ?>
            <a href="../php/order_history.php">Order History</a>
        <?php } ?>

        <a href="../php/logout.php">Logout</a>
    <?php } ?>

    <a href="contact_support.php">Contact</a>
</nav>
</header>

<!-- Banner -->
<section class="banner">
    <div class="banner-content">
        <h1>Discover Your Next Favorite Book</h1>
        <p>Explore new arrivals and special discounts every week!</p>
        <a href="../php/booklist.php" class="banner-btn">Buy Now</a>
    </div>
</section>

<?php
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}
?>

<!-- Search Bar -->
<form method="get" action="booklist.php" class="search-section">
    <div class="search-container">
        <input 
            type="text"
            name="search"
            placeholder="Search Books"
            class="search-bar"
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit" class="search-btn">
            <img src="../Picture/search.png" alt="Search" class="search-icon">
        </button>
    </div>
</form>



<!-- Latest Arrivals -->
<section class="book-section">
    <h2> Latest Arrivals</h2>
    <div class="book-grid-container">
        <div class="book-grid">
            <?php while ($book = mysqli_fetch_assoc($latestBooks)) { ?>
            <div class="book-card">
                <img src="../Picture/<?php echo $book['image']; ?>" alt="">
                <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                <p>৳<?php echo number_format($book['price'],2); ?></p>
                <a href="bookdetails.php?id=<?php echo $book['id']; ?>" class="btn">View</a>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Popular Books -->
<section class="book-section">
    <h2> Popular Books</h2>
    <div class="book-grid-container">
        <div class="book-grid">
            <?php while ($book = mysqli_fetch_assoc($popularBooks)) { ?>
            <div class="book-card">
                <img src="../Picture/<?php echo $book['image']; ?>" alt="">
                <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                <p>৳<?php echo number_format($book['price'],2); ?></p>
                <small>Sold: <?php echo $book['total_sold']; ?> times</small>
                <a href="bookdetails.php?id=<?php echo $book['id']; ?>" class="btn">View</a>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Discount Books -->
<section class="book-section">
    <h2> Discount Books</h2>
    <div class="book-grid-container">
        <div class="book-grid">
            <?php while ($book = mysqli_fetch_assoc($discountBooks)) { 
                $finalPrice = $book['price'] - ($book['price'] * $book['discount'] / 100);
            ?>
            <div class="book-card">
                <span class="badge"><?php echo $book['discount']; ?>% OFF</span>
                <img src="../Picture/<?php echo $book['image']; ?>" alt="">
                <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                <p class="old-price">৳<?php echo number_format($book['price'],2); ?></p>
                <p class="new-price">৳<?php echo number_format($finalPrice,2); ?></p>
                <a href="bookdetails.php?id=<?php echo $book['id']; ?>" class="btn">View</a>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer id="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>BookMart</h3>
            <p>© 2025 BookMart</p>
            <p>All Rights Reserved</p>
        </div>
        <div class="footer-section">
            <h4>Contact Info</h4>
            <p>Email: <a href="mailto:info@bookmart.com">info@bookmart.com</a></p>
            <p>Phone: +880 1234-567890</p>
            <p>Address: Dhaka, Bangladesh</p>
        </div>
        <div class="footer-section">
            <h4>Quick Links</h4>
            <p><a href="#">Privacy Policy</a></p>
            <p><a href="#">Terms & Conditions</a></p>
        </div>
    </div>
</footer>

<script src="../Js/dashboard.js"></script>
</body>
</html>
