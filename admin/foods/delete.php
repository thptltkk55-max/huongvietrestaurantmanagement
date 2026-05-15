<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();
requireRoleGroup(['Quản lý']);

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "UPDATE foods SET status = 'Ngừng bán' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: /huongviet/admin/foods/index.php");
exit;
?>
