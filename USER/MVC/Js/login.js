// Client-side validation for login form (mirrors login.php server-side rules)
document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("loginForm");
    if (!form) return;

    var username = document.getElementById("username");
    var password = document.getElementById("password");
    var usernameError = document.getElementById("usernameError");
    var passwordError = document.getElementById("passwordError");

    form.addEventListener("submit", function (e) {
        var valid = true;

        usernameError.textContent = "";
        passwordError.textContent = "";

        if (username.value.trim() === "") {
            usernameError.textContent = "Username is required";
            valid = false;
        }

        if (password.value.trim() === "") {
            passwordError.textContent = "Password is required";
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});
