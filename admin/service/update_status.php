<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_bootstrap.php";

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$status = trim($_GET['status'] ?? $_POST['status'] ?? '');
$validStatuses = serviceValidStatuses();
$success = '';
$error = '';

if ($id <= 0 || !in_array($status, $validStatuses, true)) {
    $error = "Thao tác không hợp lệ với trạng thái phục vụ hiện tại.";
} else {
    $stmt = mysqli_prepare($conn, "SELECT id, table_id, order_id, service_status FROM service_assignments WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$task) {
        $error = "Không tìm thấy nhiệm vụ phục vụ.";
    } elseif (!serviceCanTransition($task['service_status'], $status)) {
        $error = "Thao tác không hợp lệ với trạng thái phục vụ hiện tại.";
    } else {
        mysqli_begin_transaction($conn);
        try {
            if ($status === 'Hoàn thành') {
                $stmt = mysqli_prepare($conn, "UPDATE service_assignments SET service_status = ?, completed_at = NOW() WHERE id = ?");
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE service_assignments SET service_status = ? WHERE id = ?");
            }
            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            mysqli_stmt_execute($stmt);

            $tableId = (int) ($task['table_id'] ?? 0);
            $orderId = (int) ($task['order_id'] ?? 0);

            if ($status === 'Đang phục vụ' && $tableId > 0) {
                serviceUpdateTableStatus($conn, $tableId, 'Đang sử dụng');
            }

            if (in_array($status, ['Hoàn thành', 'Đã hủy'], true) && serviceShouldFreeTable($conn, $tableId, $orderId)) {
                serviceUpdateTableStatus($conn, $tableId, 'Trống');
            }

            mysqli_commit($conn);
            $success = "Cập nhật trạng thái phục vụ thành công.";
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $error = "Không thể cập nhật trạng thái phục vụ.";
        }
    }
}

$query = $success !== '' ? '?success=' . urlencode($success) : '?error=' . urlencode($error);
header("Location: /huongviet/admin/service/index.php" . $query);
exit;
?>
