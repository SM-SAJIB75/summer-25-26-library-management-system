// Live AJAX search + category filter for booklist.php
(function () {
    var grid = document.getElementById("bookGrid");
    var searchInput = document.getElementById("liveSearchInput");
    var currentCategory = (window.INITIAL_CATEGORY || "");
    var debounceTimer = null;

    function escapeHtml(str) {
        var div = document.createElement("div");
        div.textContent = str;
        return div.innerHTML;
    }

    function renderBooks(books) {
        if (!books || books.length === 0) {
            grid.innerHTML = '<p style="text-align:center; width:100%;">No books found.</p>';
            return;
        }

        var html = "";
        books.forEach(function (book) {
            var title = escapeHtml(book.title);
            var author = escapeHtml(book.author);
            var image = escapeHtml(book.image);
            var price = parseFloat(book.final_price).toFixed(2);

            html += ''
                + '<div class="book">'
                +   '<img src="../Picture/' + image + '" alt="book">'
                +   '<h3><a href="bookdetails.php?id=' + book.id + '">' + title + '</a></h3>'
                +   '<p><b>Author:</b> ' + author + '</p>'
                +   '<p class="price">৳' + price + '</p>'
                +   '<p class="status">' + escapeHtml(book.status) + '</p>'
                +   '<form onsubmit="return addToCart(this);">'
                +     '<input type="hidden" name="id" value="' + book.id + '">'
                +     '<input type="hidden" name="title" value="' + title + '">'
                +     '<input type="hidden" name="price" value="' + price + '">'
                +     '<button type="submit" class="cart-btn">Add to Cart</button>'
                +     '<p class="cartMsg" style="margin-top:6px; font-size:13px;"></p>'
                +   '</form>'
                + '</div>';
        });
        grid.innerHTML = html;
    }

    function loadBooks(search, category) {
        var url = "booksearch_ajax.php?search=" + encodeURIComponent(search) + "&category=" + encodeURIComponent(category);

        fetch(url)
            .then(function (res) { return res.json(); })
            .then(renderBooks)
            .catch(function () {
                grid.innerHTML = '<p style="text-align:center; width:100%;">Something went wrong.</p>';
            });
    }

    // Typing -> debounced live search (300ms after user stops typing)
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                loadBooks(searchInput.value.trim(), currentCategory);
            }, 300);
        });
    }

    // Category buttons -> AJAX filter, no page reload
    document.querySelectorAll(".category-filter").forEach(function (btn) {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            document.querySelectorAll(".category-filter").forEach(function (b) {
                b.classList.remove("active");
            });
            btn.classList.add("active");

            currentCategory = btn.getAttribute("data-category") || "";
            loadBooks(searchInput ? searchInput.value.trim() : "", currentCategory);
        });

        if (btn.getAttribute("data-active") === "1") {
            btn.classList.add("active");
        }
    });

    // Initial load (keeps working if page opened with ?search=... or ?category=... in URL)
    loadBooks(window.INITIAL_SEARCH || "", currentCategory);
})();
