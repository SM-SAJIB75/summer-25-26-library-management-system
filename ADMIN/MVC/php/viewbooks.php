<?php
session_start();
include("../../../USER/MVC/Db/dbregister.php");

if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@admin")) {
    header("Location: ../../USER/MVC/php/index.php");
    exit();
}

$categoryList = [];
$catSql = "SELECT id, name FROM categories ORDER BY name ASC";
$catResult = mysqli_query($conn, $catSql);

if ($catResult && mysqli_num_rows($catResult) > 0) {
    while ($c = mysqli_fetch_assoc($catResult)) {
        $categoryList[$c['id']] = $c['name'];
    }
}

$bookSql = "SELECT * FROM books ORDER BY id DESC";
$books = mysqli_query($conn, $bookSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book List</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
    <link rel="stylesheet" href="../Css/viewbooks.css">
</head>

<body>

<h2 class="book-list-title">Book List</h2>
<a href="admindashboard.php" class="back-dashboard">Back to Dashboard</a><br><br>
<a href="bookmodification.php" class="btn-add-book">Add New Book</a><br><br>

<div class="table-container">
<table >
<tr>
    <th>Image</th>
    <th>Title</th>
    <th>Author</th>
    <th>Price</th>
    <th>Discount</th>
    <th>Final</th>
    <th>Qty</th>
    <th>Category</th>
    <th>Description</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php
if ($books && mysqli_num_rows($books) > 0) {
    while ($row = mysqli_fetch_assoc($books)) {
        $id = (int)$row['id'];
        ?>
        <tr id="row-<?php echo $id; ?>">

        <td>
            <img src="../../../USER/MVC/Picture/<?php echo $row['image']; ?>" width="70">
        </td>

        <td>
            <input type="text" id="title-<?php echo $id; ?>"
                   value="<?php echo $row['title']; ?>">
        </td>

        <td>
            <input type="text" id="author-<?php echo $id; ?>"
                   value="<?php echo $row['author']; ?>">
        </td>

        <td>
            <input type="text" id="price-<?php echo $id; ?>"
                   value="<?php echo $row['price']; ?>"
                   oninput="calcFinalUpdate(<?php echo $id; ?>)">
        </td>

        <td>
            <input type="text" id="discount-<?php echo $id; ?>"
                   value="<?php echo $row['discount']; ?>"
                   oninput="calcFinalUpdate(<?php echo $id; ?>)">
        </td>

        <td>
            <input type="text" id="final-<?php echo $id; ?>"
                   value="<?php echo $row['final_price']; ?>" readonly>
        </td>

       <td>
  
     <?php 
            $qty = (int)$row['quantity'];
            $lowStock = $qty <= 2;
        ?>
            <input type="text" id="quantity-<?php echo $id; ?>"
                value="<?php echo $qty; ?>"
                style="<?php echo $lowStock ? 'border: 2px solid red; color: red;' : ''; ?>">
            <?php if ($lowStock) { ?>
                <span style="color: red; font-weight: bold; margin-left: 5px;">Low Stock!</span>
            <?php } ?>
        </td>

        <td>
            <select id="category-<?php echo $id; ?>">
                <?php
                foreach ($categoryList as $cid => $cname) {
                    ?>
                    <option value="<?php echo $cid; ?>"
                        <?php
                        if ($row['category_id'] == $cid) {
                            echo "selected";
                        }
                        ?>>
                        <?php echo $cname; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </td>

        <td>
            <textarea id="desc-<?php echo $id; ?>"> <?php echo $row['description']; ?></textarea>
        </td>

        <td>
            <select id="status-<?php echo $id; ?>">
                <option value="Available"
                    <?php if ($row['status'] == 'Available') { echo "selected"; } ?>>
                    Available
                </option>

                <option value="Unavailable"
                    <?php if ($row['status'] == 'Unavailable') { echo "selected"; } ?>>
                    Unavailable
                </option>
            </select>
        </td>

        <td>
            <button class="update-btn"
                    onclick="updateBook(<?php echo $id; ?>)">Update</button>

            <button class="delete-btn"
                    onclick="deleteBook(<?php echo $id; ?>)">Delete</button>
        </td>

        </tr>
        <?php
    }
}
?>

</table>
</div>

<script src="../Ajax/bookajax.js"></script>

</body>
</html>


