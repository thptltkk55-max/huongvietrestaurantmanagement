<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_helpers.php";
inventoryRequireAccess();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: /huongviet/admin/inventory/suppliers.php"); exit; }
$stmt = mysqli_prepare($conn, "SELECT * FROM suppliers WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$form = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$form) { header("Location: /huongviet/admin/inventory/suppliers.php"); exit; }
$errors = [];
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    foreach (['supplier_name','phone','email','address','status'] as $key) $form[$key] = trim($_POST[$key] ?? '');
    if ($form['supplier_name'] === '') $errors[] = 'Tên nhà cung cấp không được rỗng.';
    if (!in_array($form['status'], ['Hoạt động', 'Ngừng hợp tác'], true)) $form['status'] = 'Hoạt động';
    if (!$errors) {
        $stmt = mysqli_prepare($conn, "UPDATE suppliers SET supplier_name=?, phone=?, email=?, address=?, status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $form['supplier_name'], $form['phone'], $form['email'], $form['address'], $form['status'], $id);
        if (mysqli_stmt_execute($stmt)) { header("Location: /huongviet/admin/inventory/suppliers.php"); exit; }
        $errors[] = "Không thể cập nhật nhà cung cấp: " . mysqli_error($conn);
    }
}
inventoryHeader("Sửa nhà cung cấp");
?>
<main class="container py-4"><div class="card border-0 shadow-sm"><div class="card-body p-4">
<div class="d-flex justify-content-between mb-3"><h1 class="h3 mb-0">Sửa nhà cung cấp</h1><a class="btn btn-outline-secondary" href="/huongviet/admin/inventory/suppliers.php">Quay lại</a></div>
<?php if ($errors) { ?><div class="alert alert-danger"><?php foreach ($errors as $error) echo '<div>' . e($error) . '</div>'; ?></div><?php } ?>
<form method="POST" class="row g-3">
    <div class="col-md-6"><label class="form-label">Tên nhà cung cấp</label><input class="form-control" name="supplier_name" value="<?php echo e($form['supplier_name']); ?>" required></div>
    <div class="col-md-6"><label class="form-label">Số điện thoại</label><input class="form-control" name="phone" value="<?php echo e($form['phone']); ?>"></div>
    <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo e($form['email']); ?>"></div>
    <div class="col-md-6"><label class="form-label">Trạng thái</label><select class="form-select" name="status"><option value="Hoạt động" <?php echo $form['status'] === 'Hoạt động' ? 'selected' : ''; ?>>Hoạt động</option><option value="Ngừng hợp tác" <?php echo $form['status'] === 'Ngừng hợp tác' ? 'selected' : ''; ?>>Ngừng hợp tác</option></select></div>
    <div class="col-12"><label class="form-label">Địa chỉ</label><textarea class="form-control" name="address"><?php echo e($form['address']); ?></textarea></div>
    <div class="col-12"><button class="btn btn-primary">Cập nhật</button></div>
</form></div></div></main>
<?php inventoryFooter(); ?>
