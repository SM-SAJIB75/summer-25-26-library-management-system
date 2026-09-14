// Client-side validation for staff registration form
document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("staffRegForm");
    if (!form) return;

    var name = document.getElementById("staff_name");
    var username = document.getElementById("staff_username");
    var password = document.getElementById("staff_password");
    var nameError = document.getElementById("nameError");
    var usernameError = document.getElementById("usernameError");
    var passwordError = document.getElementById("passwordError");

    form.addEventListener("submit", function (e) {
        var valid = true;
        nameError.textContent = "";
        usernameError.textContent = "";
        passwordError.textContent = "";

        if (name.value.trim() === "") {
            nameError.textContent = "Full name is required";
            valid = false;
        }

        var u = username.value.trim();
        if (u === "") {
            usernameError.textContent = "Username is required";
            valid = false;
        } else if (!/^[a-zA-Z0-9_]+$/.test(u)) {
            usernameError.textContent = "Only letters, numbers, underscore allowed";
            valid = false;
        }

        var p = password.value.trim();
        if (p === "") {
            passwordError.textContent = "Password is required";
            valid = false;
        } else if (p.length < 6 || !/[A-Z]/.test(p) || !/[a-z]/.test(p) || !/[0-9]/.test(p)) {
            passwordError.textContent = "Min 6 chars, with upper, lower & a number";
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
});
