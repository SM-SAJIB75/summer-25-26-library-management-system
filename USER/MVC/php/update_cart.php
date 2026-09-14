<?php
session_start();

$action = $_POST['action'] ?? '';
$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$removed = 0; // 0=no, 1=yes

// CANCEL (clear all)
if ($action === "cancel") {
    $_SESSION['cart'] = [];
    echo "success|0|0|1";
    exit();
}

// UPDATE / REMOVE
for ($i = 0; $i < count($_SESSION['cart']); $i++) {

    if ((int)$_SESSION['cart'][$i]['id'] === $id) {

        if ($action === "increase") {
            $_SESSION['cart'][$i]['qty']++;
        }
        else if ($action === "decrease") {
            $_SESSION['cart'][$i]['qty']--;
            if ($_SESSION['cart'][$i]['qty'] <= 0) {
                array_splice($_SESSION['cart'], $i, 1);
                $removed = 1;
            }
        }
        else if ($action === "remove") {
            array_splice($_SESSION['cart'], $i, 1);
            $removed = 1;
        }

        break;
    }
}

// TOTAL
$total = 0;
for ($i = 0; $i < count($_SESSION['cart']); $i++) {
    $total += ((float)$_SESSION['cart'][$i]['price'] * (int)$_SESSION['cart'][$i]['qty']);
}

$cartCount = count($_SESSION['cart']);
$total = round($total, 2);

// plain text: success|cartCount|total|removed
echo "success|$cartCount|$total|$removed";
exit();
?>
