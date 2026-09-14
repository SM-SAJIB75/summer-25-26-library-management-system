<?php
/*
 * StaffModel.php — MODEL layer for the Staff role.
 * Holds ONLY database access (procedural mysqli, prepared statements).
 * No HTML here. Controllers (the files in STAFF/MVC/php/) call these
 * functions and decide what to render.
 */

// ---------- Support Tickets ----------

function staff_get_tickets($conn, $search = "") {
    if ($search !== "") {
        $like = "%$search%";
        $stmt = mysqli_prepare($conn, "SELECT * FROM support_tickets WHERE subject LIKE ? OR customer_username LIKE ? ORDER BY created_at DESC");
        mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM support_tickets ORDER BY created_at DESC");
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function staff_create_ticket($conn, $customer, $subject, $message) {
    $stmt = mysqli_prepare($conn, "INSERT INTO support_tickets (customer_username, subject, message) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $customer, $subject, $message);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function staff_update_ticket($conn, $ticket_id, $status, $handled_by) {
    $stmt = mysqli_prepare($conn, "UPDATE support_tickets SET status = ?, handled_by = ? WHERE ticket_id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $status, $handled_by, $ticket_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function staff_delete_ticket($conn, $ticket_id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM support_tickets WHERE ticket_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $ticket_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

// ---------- Shipments ----------

function staff_get_shipments($conn, $search = "") {
    if ($search !== "") {
        $like = "%$search%";
        $stmt = mysqli_prepare($conn, "SELECT * FROM shipments WHERE courier_name LIKE ? OR tracking_status LIKE ? ORDER BY updated_at DESC");
        mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM shipments ORDER BY updated_at DESC");
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function staff_create_shipment($conn, $order_id, $courier_name, $status) {
    $stmt = mysqli_prepare($conn, "INSERT INTO shipments (order_id, courier_name, tracking_status) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iss", $order_id, $courier_name, $status);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function staff_update_shipment($conn, $shipment_id, $status, $updated_by) {
    $stmt = mysqli_prepare($conn, "UPDATE shipments SET tracking_status = ?, updated_by = ? WHERE shipment_id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $status, $updated_by, $shipment_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function staff_delete_shipment($conn, $shipment_id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM shipments WHERE shipment_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $shipment_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

// ---------- Review moderation ----------

function staff_get_reviews($conn, $search = "") {
    if ($search !== "") {
        $like = "%$search%";
        $stmt = mysqli_prepare($conn, "SELECT * FROM reviews WHERE customer_username LIKE ? OR comment LIKE ? ORDER BY created_at DESC");
        mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM reviews ORDER BY created_at DESC");
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

function staff_moderate_review($conn, $review_id, $approval_status, $moderated_by) {
    $stmt = mysqli_prepare($conn, "UPDATE reviews SET approval_status = ?, moderated_by = ? WHERE review_id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $approval_status, $moderated_by, $review_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}

function staff_delete_review($conn, $review_id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM reviews WHERE review_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $review_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}
