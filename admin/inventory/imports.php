<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_helpers.php";
inventoryRequireAccess();

$fromDate = trim($_GET['from_date'] ?? '');
$toDate = trim($_GET['to_date'] ?? '');
$supplierId = (int) ($_GET['supplier_id'] ?? 0);
$suppliers = mysqli_query($conn, "SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC");

$where = [];
$params = [];
$types = '';
if ($fromDate !== '') { $where[] = 'ir.import_date >= ?'; $params[] = $fromDate; $types .= 's'; }
if ($toDate !== '') { $where[] = 'ir.import_date <= ?'; $params[] = $toDate; $types .= 's'; }
if ($supplierId > 0) { $where[] = 'ir.supplier_id = ?'; $params[] = $supplierId; $types .= 'i'; }

$sql = "SELECT ir.id, ir.import_date, ir.total_amount, ir.note, ir.created_at, s.supplier_name, u.full_name AS created_by_name
        FROM import_receipts ir
        LEFT JOIN suppliers s ON ir.supplier_id = s.id
        LEFT JOIN users u ON ir.created_by = u.id";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY ir.id DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$imports = mysqli_stmt_get_result($stmt);

inventoryHeader("Lịch sử nhập kho");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><h1 class="h3 mb-0">Lịch sử nhập kho</h1><div class="d-flex gap-2"><a class="btn btn-outline-secondary" href="/huongviet/admin/inventory/index.php">Quay lại kho</a><a class="btn btn-danger" href="/huongviet/admin/inventory/add_import.php">Tạo phiếu nhập</a></div></div>
    <div class="card border-0 shadow-sm mb-3"><div class="card-body"><form class="row g-3"><div class="col-md-3"><label class="form-label">Từ ngày</label><input type="date" class="form-control" name="from_date" value="<?php echo e($fromDate); ?>"></div><div class="col-md-3"><label class="form-label">Đến ngày</label><input type="date" class="form-control" name="to_date" value="<?php echo e($toDate); ?>"></div><div class="col-md-4"><label class="form-label">Nhà cung cấp</label><select class="form-select" name="supplier_id"><option value="0">Tất cả</option><?php while ($s = mysqli_fetch_assoc($suppliers)) { ?><option value="<?php echo (int) $s['id']; ?>" <?php echo $supplierId === (int) $s['id'] ? 'selected' : ''; ?>><?php echo e($s['supplier_name']); ?></option><?php } ?></select></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-danger w-100">Lọc</button></div></form></div></div>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Mã phiếu</th><th>Nhà cung cấp</th><th>Ngày nhập</th><th>Tổng tiền</th><th>Ghi chú</th><th>Người tạo</th><th>Ngày tạo</th><th class="text-end">Hành động</th></tr></thead><tbody>
    <?php while ($row = mysqli_fetch_assoc($imports)) { ?><tr><td>#<?php echo (int) $row['id']; ?></td><td><?php echo e($row['supplier_name']); ?></td><td><?php echo e($row['import_date']); ?></td><td><?php echo inventoryMoney($row['total_amount']); ?></td><td><?php echo e($row['note']); ?></td><td><?php echo e($row['created_by_name']); ?></td><td><?php echo e($row['created_at']); ?></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="/huongviet/admin/inventory/view_import.php?id=<?php echo (int) $row['id']; ?>">Xem chi tiết</a></td></tr><?php } ?>
    </tbody></table></div></div>
</main>
<?php inventoryFooter(); ?>
