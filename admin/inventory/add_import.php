<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_helpers.php";
inventoryRequireAccess();

$errors = [];
$form = ['supplier_id' => '0', 'import_date' => date('Y-m-d'), 'note' => ''];

function fetchInventoryOptions(mysqli $conn, string $table, string $order)
{
    $result = mysqli_query($conn, "SELECT * FROM $table $order");
    if (!$result) die("Lỗi truy vấn: " . mysqli_error($conn));
    return $result;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $form['supplier_id'] = (int) ($_POST['supplier_id'] ?? 0);
    $form['import_date'] = trim($_POST['import_date'] ?? '');
    $form['note'] = trim($_POST['note'] ?? '');
    $ingredientIds = $_POST['ingredient_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $unitPrices = $_POST['unit_price'] ?? [];
    $details = [];
    $totalAmount = 0;

    if ($form['import_date'] === '') $errors[] = 'Ngày nhập không được rỗng.';
    for ($i = 0; $i < count($ingredientIds); $i++) {
        $ingredientId = (int) ($ingredientIds[$i] ?? 0);
        if ($ingredientId <= 0) continue;
        $quantity = (float) ($quantities[$i] ?? 0);
        $unitPrice = (float) ($unitPrices[$i] ?? 0);
        if ($quantity <= 0) { $errors[] = 'Số lượng nhập phải > 0.'; continue; }
        if ($unitPrice < 0) { $errors[] = 'Đơn giá phải >= 0.'; continue; }
        $lineTotal = $quantity * $unitPrice;
        $details[] = compact('ingredientId', 'quantity', 'unitPrice', 'lineTotal');
        $totalAmount += $lineTotal;
    }
    if (!$details) $errors[] = 'Phải có ít nhất 1 nguyên liệu hợp lệ.';

    if (!$errors) {
        mysqli_begin_transaction($conn);
        try {
            $supplierId = $form['supplier_id'] > 0 ? (int) $form['supplier_id'] : null;
            $current = currentUser();
            $createdBy = $current ? (int) $current['id'] : null;
            $stmt = mysqli_prepare($conn, "INSERT INTO import_receipts (supplier_id, import_date, total_amount, note, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "isdsi", $supplierId, $form['import_date'], $totalAmount, $form['note'], $createdBy);
            mysqli_stmt_execute($stmt);
            $importId = mysqli_insert_id($conn);
            foreach ($details as $detail) {
                $stmt = mysqli_prepare($conn, "INSERT INTO import_details (import_id, ingredient_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iiddd", $importId, $detail['ingredientId'], $detail['quantity'], $detail['unitPrice'], $detail['lineTotal']);
                mysqli_stmt_execute($stmt);
                $stmt = mysqli_prepare($conn, "UPDATE ingredients SET quantity = quantity + ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "di", $detail['quantity'], $detail['ingredientId']);
                mysqli_stmt_execute($stmt);
            }
            mysqli_commit($conn);
            header("Location: /huongviet/admin/inventory/view_import.php?id=" . $importId);
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $errors[] = 'Không thể tạo phiếu nhập: ' . $e->getMessage();
        }
    }
}

$suppliers = fetchInventoryOptions($conn, 'suppliers', "WHERE status = 'Hoạt động' ORDER BY supplier_name ASC");
$ingredients = fetchInventoryOptions($conn, 'ingredients', "WHERE status = 'Đang dùng' ORDER BY ingredient_name ASC");
$ingredientRows = [];
while ($row = mysqli_fetch_assoc($ingredients)) $ingredientRows[] = $row;

inventoryHeader("Tạo phiếu nhập kho");
?>
<main class="container py-4"><div class="card border-0 shadow-sm"><div class="card-body p-4">
<div class="d-flex justify-content-between mb-3"><h1 class="h3 mb-0">Tạo phiếu nhập kho</h1><a class="btn btn-outline-secondary" href="/huongviet/admin/inventory/imports.php">Lịch sử nhập</a></div>
<?php if ($errors) { ?><div class="alert alert-danger"><?php foreach ($errors as $error) echo '<div>' . e($error) . '</div>'; ?></div><?php } ?>
<form method="POST">
    <div class="row g-3 mb-4"><div class="col-md-4"><label class="form-label">Nhà cung cấp</label><select class="form-select" name="supplier_id"><option value="0">-- Không chọn --</option><?php while ($supplier = mysqli_fetch_assoc($suppliers)) { ?><option value="<?php echo (int) $supplier['id']; ?>"><?php echo e($supplier['supplier_name']); ?></option><?php } ?></select></div><div class="col-md-4"><label class="form-label">Ngày nhập</label><input type="date" class="form-control" name="import_date" value="<?php echo e($form['import_date']); ?>" required></div><div class="col-md-4"><label class="form-label">Ghi chú</label><input class="form-control" name="note" value="<?php echo e($form['note']); ?>"></div></div>
    <h2 class="h5">Chi tiết nguyên liệu</h2>
    <div class="table-responsive"><table class="table align-middle"><thead class="table-light"><tr><th>Nguyên liệu</th><th>Số lượng</th><th>Đơn giá</th></tr></thead><tbody>
        <?php for ($i = 0; $i < 5; $i++) { ?><tr><td><select class="form-select" name="ingredient_id[]"><option value="0">-- Chọn nguyên liệu --</option><?php foreach ($ingredientRows as $ingredient) { ?><option value="<?php echo (int) $ingredient['id']; ?>"><?php echo e($ingredient['ingredient_name'] . ' (' . $ingredient['unit'] . ')'); ?></option><?php } ?></select></td><td><input type="number" step="0.01" min="0" class="form-control" name="quantity[]"></td><td><input type="number" step="1000" min="0" class="form-control" name="unit_price[]"></td></tr><?php } ?>
    </tbody></table></div>
    <button class="btn btn-danger">Lưu phiếu nhập</button>
</form></div></div></main>
<?php inventoryFooter(); ?>
