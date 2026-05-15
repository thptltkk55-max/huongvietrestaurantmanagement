<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_helpers.php";
inventoryRequireAccess();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: /huongviet/admin/inventory/index.php"); exit; }

function ingredientNameExists(mysqli $conn, string $name, int $ignoreId = 0)
{
    $stmt = mysqli_prepare($conn, "SELECT id FROM ingredients WHERE ingredient_name = ? AND status = 'Đang dùng' AND id <> ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "si", $name, $ignoreId);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) !== null;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM ingredients WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$form = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$form) { header("Location: /huongviet/admin/inventory/index.php"); exit; }

$errors = [];
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $form['ingredient_name'] = trim($_POST['ingredient_name'] ?? '');
    $form['unit'] = trim($_POST['unit'] ?? '');
    $form['quantity'] = trim($_POST['quantity'] ?? '0');
    $form['min_quantity'] = trim($_POST['min_quantity'] ?? '0');
    $form['status'] = $_POST['status'] ?? 'Đang dùng';
    if ($form['ingredient_name'] === '') $errors[] = 'Tên nguyên liệu không được rỗng.';
    if ($form['unit'] === '') $errors[] = 'Đơn vị không được rỗng.';
    if (!is_numeric($form['quantity']) || (float) $form['quantity'] < 0) $errors[] = 'Số lượng tồn phải >= 0.';
    if (!is_numeric($form['min_quantity']) || (float) $form['min_quantity'] < 0) $errors[] = 'Tồn tối thiểu phải >= 0.';
    if (!in_array($form['status'], ['Đang dùng', 'Ngừng dùng'], true)) $form['status'] = 'Đang dùng';
    if ($form['ingredient_name'] !== '' && ingredientNameExists($conn, $form['ingredient_name'], $id)) $errors[] = 'Tên nguyên liệu đang dùng đã tồn tại.';
    if (!$errors) {
        $quantity = (float) $form['quantity'];
        $minQuantity = (float) $form['min_quantity'];
        $stmt = mysqli_prepare($conn, "UPDATE ingredients SET ingredient_name=?, unit=?, quantity=?, min_quantity=?, status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssddsi", $form['ingredient_name'], $form['unit'], $quantity, $minQuantity, $form['status'], $id);
        if (mysqli_stmt_execute($stmt)) { header("Location: /huongviet/admin/inventory/index.php"); exit; }
        $errors[] = "Không thể cập nhật nguyên liệu: " . mysqli_error($conn);
    }
}

inventoryHeader("Sửa nguyên liệu");
?>
<main class="container py-4">
    <div class="card border-0 shadow-sm"><div class="card-body p-4">
        <div class="d-flex justify-content-between mb-3"><h1 class="h3 mb-0">Sửa nguyên liệu</h1><a class="btn btn-outline-secondary" href="/huongviet/admin/inventory/index.php">Quay lại</a></div>
        <?php if ($errors) { ?><div class="alert alert-danger"><?php foreach ($errors as $error) echo '<div>' . e($error) . '</div>'; ?></div><?php } ?>
        <form method="POST" class="row g-3">
            <div class="col-md-6"><label class="form-label">Tên nguyên liệu</label><input class="form-control" name="ingredient_name" value="<?php echo e($form['ingredient_name']); ?>" required></div>
            <div class="col-md-6"><label class="form-label">Đơn vị</label><input class="form-control" name="unit" value="<?php echo e($form['unit']); ?>" required></div>
            <div class="col-md-4"><label class="form-label">Số lượng tồn</label><input type="number" step="0.01" min="0" class="form-control" name="quantity" value="<?php echo e($form['quantity']); ?>"></div>
            <div class="col-md-4"><label class="form-label">Tồn tối thiểu</label><input type="number" step="0.01" min="0" class="form-control" name="min_quantity" value="<?php echo e($form['min_quantity']); ?>"></div>
            <div class="col-md-4"><label class="form-label">Trạng thái</label><select class="form-select" name="status"><option value="Đang dùng" <?php echo $form['status'] === 'Đang dùng' ? 'selected' : ''; ?>>Đang dùng</option><option value="Ngừng dùng" <?php echo $form['status'] === 'Ngừng dùng' ? 'selected' : ''; ?>>Ngừng dùng</option></select></div>
            <div class="col-12"><button class="btn btn-primary">Cập nhật</button></div>
        </form>
    </div></div>
</main>
<?php inventoryFooter(); ?>
