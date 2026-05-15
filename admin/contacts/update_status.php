<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
requireRoleGroup(['Quản lý', 'Nhân viên']);

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        email VARCHAR(255),
        address VARCHAR(255),
        subject VARCHAR(255),
        message TEXT,
        status VARCHAR(50) DEFAULT 'Chưa xử lý',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
$statusColumn = mysqli_query($conn, "SHOW COLUMNS FROM contact_messages LIKE 'status'");
if ($statusColumn && mysqli_num_rows($statusColumn) === 0) {
    mysqli_query($conn, "ALTER TABLE contact_messages ADD COLUMN status VARCHAR(50) DEFAULT 'Chưa xử lý'");
}

$id = (int) ($_REQUEST['id'] ?? 0);
$status = $_REQUEST['status'] ?? '';
$redirect = $_REQUEST['redirect'] ?? '';
$validStatuses = ['Chưa xử lý', 'Đang xử lý', 'Đã xử lý'];
$success = "";
$error = "";

if ($id <= 0 || !in_array($status, $validStatuses, true)) {
    $error = "Thao tác không hợp lệ với trạng thái hiện tại.";
} else {
    $stmt = mysqli_prepare($conn, "SELECT status FROM contact_messages WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $contact = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$contact) {
        $error = "Không tìm thấy liên hệ.";
    } elseif ($contact['status'] === 'Đã xử lý') {
        $error = "Liên hệ đã xử lý, không thể cập nhật trạng thái.";
    } else {
        $currentStatus = $contact['status'];
        $allowed = false;

        if ($currentStatus === 'Chưa xử lý' && in_array($status, ['Đang xử lý', 'Đã xử lý'], true)) {
            $allowed = true;
        }

        if ($currentStatus === 'Đang xử lý' && $status === 'Đã xử lý') {
            $allowed = true;
        }

        if ($allowed) {
            $stmt = mysqli_prepare($conn, "UPDATE contact_messages SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            mysqli_stmt_execute($stmt);
            $success = "Cập nhật trạng thái liên hệ thành công.";
        } else {
            $error = "Thao tác không hợp lệ với trạng thái hiện tại.";
        }
    }
}

if ($redirect === 'view' && $id > 0 && $success !== "") {
    header("Location: /huongviet/admin/contacts/view.php?id=" . $id);
    exit;
}

$query = $success !== "" ? "?success=" . urlencode($success) : "?error=" . urlencode($error);
header("Location: /huongviet/admin/contacts/index.php" . $query);
exit;
?>
