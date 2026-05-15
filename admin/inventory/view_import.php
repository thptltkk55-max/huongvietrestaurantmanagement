<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_helpers.php";
inventoryRequireAccess();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: /huongviet/admin/inventory/imports.php"); exit; }
$sql = "SELECT ir.*, s.supplier_name, u.full_name AS created_by_name FROM import_receipts ir LEFT JOIN suppliers s ON ir.supplier_id=s.id LEFT JOIN users u ON ir.created_by=u.id WHERE ir.id=? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$receipt = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$receipt) { header("Location: /huongviet/admin/inventory/imports.php"); exit; }
$sql = "SELECT d.*, i.ingredient_name, i.unit FROM import_details d LEFT JOIN ingredients i ON d.ingredient_id=i.id WHERE d.import_id=? ORDER BY d.id ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$details = mysqli_stmt_get_result($stmt);

inventoryHeader("Chi tiết phiếu nhập");
?>
<main class="container py-4">
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><h1 class="h3 mb-0">Phiếu nhập #<?php echo (int) $receipt['id']; ?></h1><div><a class="btn btn-outline-secondary" href="/huongviet/admin/inventory/imports.php">Lịch sử nhập</a> <a class="btn btn-outline-primary" href="/huongviet/admin/inventory/index.php">Quản lý kho</a></div></div>
<div class="card border-0 shadow-sm mb-3"><div class="card-body row g-3"><div class="col-md-4"><strong>Nhà cung cấp:</strong> <?php echo e($receipt['supplier_name']); ?></div><div class="col-md-4"><strong>Ngày nhập:</strong> <?php echo e($receipt['import_date']); ?></div><div class="col-md-4"><strong>Người tạo:</strong> <?php echo e($receipt['created_by_name']); ?></div><div class="col-md-4"><strong>Tổng tiền:</strong> <?php echo inventoryMoney($receipt['total_amount']); ?></div><div class="col-md-8"><strong>Ghi chú:</strong> <?php echo e($receipt['note']); ?></div></div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>STT</th><th>Nguyên liệu</th><th>Đơn vị</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead><tbody>
<?php $stt = 1; while ($detail = mysqli_fetch_assoc($details)) { ?><tr><td><?php echo $stt++; ?></td><td><?php echo e($detail['ingredient_name']); ?></td><td><?php echo e($detail['unit']); ?></td><td><?php echo number_format((float) $detail['quantity'], 2, ',', '.'); ?></td><td><?php echo inventoryMoney($detail['unit_price']); ?></td><td><?php echo inventoryMoney($detail['total_price']); ?></td></tr><?php } ?>
</tbody></table></div></div>
</main>
<?php inventoryFooter(); ?>
