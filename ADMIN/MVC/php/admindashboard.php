<?php
session_start();
$loggedUser = "";

if (isset($_SESSION["username"])) {
    $loggedUser = $_SESSION["username"];
} elseif (isset($_COOKIE["username"])) {
    $loggedUser = $_COOKIE["username"];
}

if (empty($loggedUser) || !str_starts_with($loggedUser, "@admin")) {
    header("Location: ../../USER/MVC/php/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
    <link rel="stylesheet" href="../Css/admindashboard.css">
</head>
<body>
    <div class="container">

    <!-- LEFT MENU -->
    <div class="sidebar">
        <h2>Admin Menu</h2>
        <hr>
        <a href="../php/viewuser.php">View Registered Users</a>
        <a href="../php/order.php">View Customer Orders</a>
        <a href="../php/bookmodification.php">Book Modification</a>
        <a href="../php/sales.php">Generate Sales Report</a>
        <a href="../php/category.php">Category Management</a>
        <a href="../php/adminvendorbooks.php">Vendor Books</a>
    </div>
 
    <!-- RIGHT CONTENT -->
    <div class="content">
        <?php if (!empty($loggedUser)) { ?>
            <h2 class="welcome-text">
                Welcome, <?php echo $loggedUser; ?>!!
            </h2>
        <?php } ?>

        <h1>Admin Dashboard</h1>
        <a class="logout" href="../../../USER/MVC/php/logout.php">Logout</a>
    
        <hr>

 <div class="cards">

            <div class="card" id="user-card">
                <h3>Registered Users</h3>
                <p>See all users who registered on the website.</p>
            </div>

        <div class="card" id="order-card">
                <h3>Customer Orders</h3>
                <p>View all orders placed by customers.</p>
            </div>

         <div class="card" id="book-card">
                <h3>Book Modification</h3>
                <p>Add,Update & delete books.Check books that are running out of stock.</p>
            </div>

           <div class="card" id="sales-card">
                <h3>Sales Report</h3>
                <p>View daily, monthly and yearly sales.</p>
            </div>

             <div class="card" id="category-card">
                <h3>Category Management</h3>
                <p>Add, edit or delete book categories.</p>
            </div>

             <div class="card" id="vendorbooks-card">
                <h3>Vendor Books</h3>
                <p>Approve or reject books submitted by vendors.</p>
            </div>

        </div>
    </div>

</div>

  <script src="../Js/viewuserr.js"></script>
  <script src="../Js/orderr.js"></script>
  <script src="../Js/bookmodification.js"></script>
  <script src="../Js/sales.js"></script>
  <script src="../Js/category.js"></script>
  <script src="../Js/vendorbooks.js"></script>

</body>
</html>
    
</body>
</html>