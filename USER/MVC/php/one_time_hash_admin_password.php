<?php
/*
 * ONE-TIME UTILITY — run this once in your browser after updating login.php,
 * then DELETE this file.
 *
 * Why: login.php now checks ALL roles (including admin) with password_verify(),
 * but your existing admin row likely has a PLAIN-TEXT password. This script
 * re-hashes it in place so admin login keeps working securely.
 *
 * Usage: open http://localhost/.../USER/MVC/php/one_time_hash_admin_password.php
 * in the browser once, confirm it says "done", then delete this file.
 */
include "../Db/dbregister.php";

$adminUsername = "@admin"; // change this if your admin username is different

$stmt = mysqli_prepare($conn, "SELECT password FROM admin WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $adminUsername);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    die("No admin row found for username '$adminUsername'. Edit \$adminUsername in this file and retry.");
}

$current = $row['password'];

// If it already looks like a bcrypt hash, don't double-hash it.
if (password_get_info($current)['algo'] !== null) {
    die("Admin password already hashed — nothing to do. You can delete this file.");
}

$newHash = password_hash($current, PASSWORD_DEFAULT);

$update = mysqli_prepare($conn, "UPDATE admin SET password = ? WHERE username = ?");
mysqli_stmt_bind_param($update, "ss", $newHash, $adminUsername);
mysqli_stmt_execute($update);
mysqli_stmt_close($update);

echo "done — admin password hashed. Delete this file now.";
