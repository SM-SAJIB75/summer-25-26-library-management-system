<?php
session_start();
include("../../../USER/MVC/Db/dbregister.php");

if (!isset($_SESSION["username"]) || !str_starts_with($_SESSION["username"], "@admin")) {
    header("Location: ../../USER/MVC/php/index.php");
    exit();
}

$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Category Management</title>
    <link rel="stylesheet" href="../../../USER/MVC/Css/theme.css">
    <link rel="stylesheet" href="../Css/category.css">
</head>
<body>

<div style="text-align:center;">
    <h2>Category Management</h2>
</div>

<div class="box">
    <h3>Add Category</h3>
    <input type="text" id="cat_name" placeholder="Category Name">
    <button onclick="addCategory()">Add Category</button>
</div>

<hr>

<div class="table-container">
    <table>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Action</th>
        </tr>
<?php while ($row = mysqli_fetch_assoc($cats)) { ?>
        <tr id="row-<?php echo $row['id']; ?>">
            <td><?php echo $row['id']; ?></td>
            <td>
                <input type="text"
                       id="name-<?php echo $row['id']; ?>"
                       value="<?php echo $row['name']; ?>">
            </td>
           <td>
                <div class="action-btns">
                    <button class="update-btn"
                            onclick="updateCategory(<?php echo $row['id']; ?>)">
                        Update
                    </button>
                    <button class="delete-btn"
                            onclick="deleteCategory(<?php echo $row['id']; ?>)">
                        Delete
                    </button>
                </div>
            </td>
        </tr>
        <?php } ?>

    </table>
</div>

<script src="../Ajax/categoryajax.js"></script>
</body>
</html>