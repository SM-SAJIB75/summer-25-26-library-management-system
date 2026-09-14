// Client-side validation for register form (mirrors register.php server-side rules)
document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("registerForm");
    if (!form) return;

    var username = document.getElementById("reg_username");
    var password = document.getElementById("reg_password");
    var usernameError = document.getElementById("reg_usernameError");
    var passwordError = document.getElementById("reg_passwordError");

    form.addEventListener("submit", function (e) {
        var valid = true;
        usernameError.textContent = "";
        passwordError.textContent = "";

        var u = username.value.trim();
        var p = password.value.trim();

        if (u === "") {
            usernameError.textContent = "Username is required";
            valid = false;
        } else if (!/^[a-zA-Z]/.test(u)) {
            usernameError.textContent = "Username must start with a letter";
            valid = false;
        } else if (!/^[a-zA-Z .\-]+$/.test(u)) {
            usernameError.textContent = "Only letters, dot, dash allowed";
            valid = false;
        } else if (u.split(/\s+/).filter(Boolean).length < 2) {
            usernameError.textContent = "Username must contain at least two words";
            valid = false;
        }

        if (p === "") {
            passwordError.textContent = "Password is required";
            valid = false;
        } else if (p.length < 6) {
            passwordError.textContent = "Password must be at least 6 characters";
            valid = false;
        } else if (!/[A-Z]/.test(p)) {
            passwordError.textContent = "Must contain one uppercase letter";
            valid = false;
        } else if (!/[a-z]/.test(p)) {
            passwordError.textContent = "Must contain one lowercase letter";
            valid = false;
        } else if (!/[0-9]/.test(p)) {
            passwordError.textContent = "Must contain one number";
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
});
