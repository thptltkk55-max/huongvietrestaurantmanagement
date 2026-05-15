<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_helpers.php";
inventoryRequireAccess();

$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $status = 'Ngừng hợp tác';
    $stmt = mysqli_prepare($conn, "UPDATE suppliers SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    mysqli_stmt_execute($stmt);
}
header("Location: /huongviet/admin/inventory/suppliers.php");
exit;
?>
