<?php
session_start();
include "../Db/dbregister.php";

// Must be logged in as a regular customer (not admin/vendor/staff)
if (!isset($_SESSION["username"])
    || str_starts_with($_SESSION["username"], "@admin")
    || str_starts_with($_SESSION["username"], "@vendor")
    || str_starts_with($_SESSION["username"], "@staff")) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];
$subject = $message = "";
$subjectError = $messageError = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($subject === "") {
        $subjectError = "Subject is required";
    }
    if ($message === "") {
        $messageError = "Message is required";
    }

    if (empty($subjectError) && empty($messageError)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO support_tickets (customer_username, subject, message) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $username, $subject, $message);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Your message has been sent! Our support team will get back to you soon.";
            $subject = $message = "";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Support</title>
    <link rel="stylesheet" href="../Css/theme.css">
</head>
<body>

<div class="container" style="max-width:500px;margin:60px auto;padding:30px;">
    <h1>Contact Support</h1>
    <p style="color:#6B7280;font-size:14px;">Have an issue with an order or a book? Send us a message.</p>

    <?php if ($success): ?>
        <p class="successmsg"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="post" id="contactForm" novalidate>
        <div class="form-group" style="text-align:left;">
            <label>Subject</label>
            <input type="text" name="subject" id="subject" style="width:100%;" value="<?php echo htmlspecialchars($subject); ?>">
            <span class="error"><?php echo $subjectError; ?></span>
        </div>

        <div class="form-group" style="text-align:left;">
            <label>Message</label>
            <textarea name="message" id="message" rows="5" style="width:100%;"><?php echo htmlspecialchars($message); ?></textarea>
            <span class="error"><?php echo $messageError; ?></span>
        </div>

        <button type="submit">Send Message</button>
    </form>

    <p style="margin-top:14px;"><a href="index.php">&larr; Back to Home</a></p>
</div>

</body>
</html>
