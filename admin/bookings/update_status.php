<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
$status = trim($_POST['status'] ?? $_GET['status'] ?? '');
$validStatuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đã hủy', 'Đã hoàn thành'];
$success = "";
$error = "";

if ($id <= 0 || !in_array($status, $validStatuses, true)) {
    $error = "Thao tác không hợp lệ với trạng thái hiện tại.";
} else {
    $stmt = mysqli_prepare($conn, "SELECT status FROM bookings WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$booking) {
        $error = "Không tìm thấy lịch đặt bàn.";
    } else {
        $currentStatus = $booking['status'];
        $allowed = false;

        if ($currentStatus === 'Chờ xác nhận' && in_array($status, ['Đã xác nhận', 'Đã hủy'], true)) {
            $allowed = true;
        }

        if ($currentStatus === 'Đã xác nhận' && $status === 'Đã hoàn thành') {
            $allowed = true;
        }

        if ($allowed) {
            $stmt = mysqli_prepare($conn, "UPDATE bookings SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            mysqli_stmt_execute($stmt);
            $success = "Cập nhật trạng thái đặt bàn thành công.";
        } else {
            $error = "Thao tác không hợp lệ với trạng thái hiện tại.";
        }
    }
}

$query = $success !== "" ? "?success=" . urlencode($success) : "?error=" . urlencode($error);
header("Location: /huongviet/admin/bookings/index.php" . $query);
exit;
?>
