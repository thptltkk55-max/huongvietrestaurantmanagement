<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_helpers.php";
inventoryRequireAccess();

$keyword = trim($_GET['keyword'] ?? '');
$sql = "SELECT * FROM suppliers";
$params = [];
$types = '';
if ($keyword !== '') {
    $sql .= " WHERE supplier_name LIKE ? OR phone LIKE ? OR email LIKE ?";
    $like = '%' . $keyword . '%';
    $params = [$like, $like, $like];
    $types = 'sss';
}
$sql .= " ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$suppliers = mysqli_stmt_get_result($stmt);

inventoryHeader("Nhà cung cấp");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <h1 class="h3 mb-0">Quản lý nhà cung cấp</h1>
        <div class="d-flex gap-2"><a class="btn btn-outline-secondary" href="/huongviet/admin/inventory/index.php">Quay lại kho</a><a class="btn btn-primary" href="/huongviet/admin/inventory/add_supplier.php">Thêm nhà cung cấp</a></div>
    </div>
    <div class="card border-0 shadow-sm mb-3"><div class="card-body"><form class="row g-3"><div class="col-md-10"><input class="form-control" name="keyword" value="<?php echo e($keyword); ?>" placeholder="Tìm tên, số điện thoại, email..."></div><div class="col-md-2"><button class="btn btn-danger w-100">Tìm</button></div></form></div></div>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>STT</th><th>Tên nhà cung cấp</th><th>Số điện thoại</th><th>Email</th><th>Địa chỉ</th><th>Trạng thái</th><th>Ngày tạo</th><th class="text-end">Hành động</th></tr></thead>
        <tbody>
            <?php $stt = 1; while ($supplier = mysqli_fetch_assoc($suppliers)) { ?>
                <tr>
                    <td><?php echo $stt++; ?></td><td class="fw-semibold"><?php echo e($supplier['supplier_name']); ?></td><td><?php echo e($supplier['phone']); ?></td><td><?php echo e($supplier['email']); ?></td><td><?php echo e($supplier['address']); ?></td>
                    <td><span class="badge <?php echo $supplier['status'] === 'Hoạt động' ? 'text-bg-success' : 'text-bg-secondary'; ?>"><?php echo e($supplier['status']); ?></span></td><td><?php echo e($supplier['created_at']); ?></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="/huongviet/admin/inventory/edit_supplier.php?id=<?php echo (int) $supplier['id']; ?>">Sửa</a> <a class="btn btn-sm btn-outline-danger" href="/huongviet/admin/inventory/delete_supplier.php?id=<?php echo (int) $supplier['id']; ?>" onclick="return confirm('Bạn có chắc muốn ngừng hợp tác nhà cung cấp này không?');">Ngừng hợp tác</a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table></div></div>
</main>
<?php inventoryFooter(); ?>
