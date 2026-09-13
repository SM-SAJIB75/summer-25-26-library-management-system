<?php
session_start();
include("../Db/dbregister.php"); // $conn = mysqli_connect(...)

$search = "";
$category = "";

/* Fetch categories (procedural) */
$catResult = mysqli_query($conn, "SELECT * FROM categories");

if (isset($_GET['search']) && trim($_GET['search']) != "") {
    $search = trim($_GET['search']);
}
if (isset($_GET['category']) && trim($_GET['category']) != "") {
    $category = trim($_GET['category']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book List - BookZone</title>
    <link rel="stylesheet" href="../Css/theme.css">
    <link rel="stylesheet" href="../Css/booklist.css">
</head>
<body>

<h2 class="page-title">All Books</h2>

<!-- SEARCH -->
<form method="get" class="search-section" id="searchForm" onsubmit="return false;">
    <div class="search-container">
        <input type="text" name="search" id="liveSearchInput" class="search-bar"
               placeholder="Search books..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="search-btn">
            <img src="../Picture/search.png" alt="search">
        </button>
    </div>
</form>
//Pushpita
<!-- CATEGORY SECTION -->
<div class="category-section">
    <a href="#" class="category-btn category-filter" data-category="" data-active="<?php echo $category === '' ? '1' : '0'; ?>">All</a>

    <?php if ($catResult && mysqli_num_rows($catResult) > 0) { ?>
        <?php while ($cat = mysqli_fetch_assoc($catResult)) { ?>
            <a href="#" class="category-btn category-filter"
               data-category="<?php echo (int)$cat['id']; ?>"
               data-active="<?php echo ((string)$category === (string)$cat['id']) ? '1' : '0'; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php } ?>
    <?php } ?>
</div>

<!-- BOOK LIST -->
<div class="book-grid" id="bookGrid"></div>

<script>
window.INITIAL_SEARCH = <?php echo json_encode($search); ?>;
window.INITIAL_CATEGORY = <?php echo json_encode((string)$category); ?>;
</script>
<script src="../Js/booklive.js"></script>
<script src="../Js/booklistajax.js"></script>
</body>
</html>
