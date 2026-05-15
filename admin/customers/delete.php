<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();
requireRoleGroup(['Quản lý']);

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $status = 'Ngừng theo dõi';
    $stmt = mysqli_prepare($conn, "UPDATE customers SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    mysqli_stmt_execute($stmt);
}

header("Location: /huongviet/admin/customers/index.php");
exit;
?>
