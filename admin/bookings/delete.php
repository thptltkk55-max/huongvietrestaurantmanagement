<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$success = "";
$error = "";

if ($id <= 0) {
    $error = "Không tìm thấy lịch đặt bàn.";
} else {
    $stmt = mysqli_prepare($conn, "SELECT status FROM bookings WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$booking) {
        $error = "Không tìm thấy lịch đặt bàn.";
    } elseif ($booking['status'] !== 'Chờ xác nhận') {
        $error = "Chỉ có thể hủy lịch đặt bàn đang chờ xác nhận.";
    } else {
        $status = 'Đã hủy';
        $stmt = mysqli_prepare($conn, "UPDATE bookings SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        mysqli_stmt_execute($stmt);
        $success = "Hủy lịch đặt bàn thành công.";
    }
}

$query = $success !== "" ? "?success=" . urlencode($success) : "?error=" . urlencode($error);
header("Location: /huongviet/admin/bookings/index.php" . $query);
exit;
?>
