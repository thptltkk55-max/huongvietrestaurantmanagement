<?php
require_once __DIR__ . "/../auth.php";
requireRoleGroup('Quản lý');

$id = (int) ($_GET['id'] ?? 0);
$currentUser = currentUser();

if ($id > 0 && $id !== (int) $currentUser['id']) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET status = 'Khóa' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: /huongviet/admin/users/index.php");
exit;
?>
