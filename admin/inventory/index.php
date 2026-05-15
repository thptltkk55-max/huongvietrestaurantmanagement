<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_helpers.php";
inventoryRequireAccess();

$keyword = trim($_GET['keyword'] ?? '');
$status = trim($_GET['status'] ?? '');
$lowStock = isset($_GET['low_stock']) && $_GET['low_stock'] === '1';

if (!in_array($status, ['Đang dùng', 'Ngừng dùng'], true)) {
    $status = '';
}

$statsResult = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS total,
        SUM(quantity <= min_quantity) AS low_count,
        SUM(status = 'Đang dùng') AS active_count,
        SUM(status = 'Ngừng dùng') AS inactive_count
     FROM ingredients"
);
$stats = mysqli_fetch_assoc($statsResult) ?: [];

$where = [];
$params = [];
$types = '';

if ($keyword !== '') {
    $where[] = "ingredient_name LIKE ?";
    $params[] = '%' . $keyword . '%';
    $types .= 's';
}

if ($status !== '') {
    $where[] = "status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($lowStock) {
    $where[] = "quantity <= min_quantity";
}

$sql = "SELECT * FROM ingredients";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$ingredients = mysqli_stmt_get_result($stmt);

inventoryHeader("Quản lý kho");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h1 class="h3 mb-1">Quản lý kho nguyên liệu</h1>
            <p class="text-muted mb-0">Theo dõi tồn kho, cảnh báo sắp hết và trạng thái sử dụng.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary" href="/huongviet/admin/dashboard.php">Dashboard</a>
            <a class="btn btn-primary" href="/huongviet/admin/inventory/add_ingredient.php">Thêm nguyên liệu</a>
            <a class="btn btn-outline-primary" href="/huongviet/admin/inventory/suppliers.php">Nhà cung cấp</a>
            <a class="btn btn-danger" href="/huongviet/admin/inventory/add_import.php">Nhập kho</a>
            <a class="btn btn-outline-danger" href="/huongviet/admin/inventory/imports.php">Lịch sử nhập</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Tổng nguyên liệu</div><div class="h4 mb-0"><?php echo (int) ($stats['total'] ?? 0); ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Sắp hết</div><div class="h4 mb-0 text-danger"><?php echo (int) ($stats['low_count'] ?? 0); ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Đang dùng</div><div class="h4 mb-0"><?php echo (int) ($stats['active_count'] ?? 0); ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Ngừng dùng</div><div class="h4 mb-0"><?php echo (int) ($stats['inactive_count'] ?? 0); ?></div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-4">
                    <label class="form-label">Tìm tên nguyên liệu</label>
                    <input type="text" name="keyword" class="form-control" value="<?php echo e($keyword); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Đang dùng" <?php echo $status === 'Đang dùng' ? 'selected' : ''; ?>>Đang dùng</option>
                        <option value="Ngừng dùng" <?php echo $status === 'Ngừng dùng' ? 'selected' : ''; ?>>Ngừng dùng</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="low_stock" <?php echo $lowStock ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="low_stock">Chỉ nguyên liệu sắp hết</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-danger w-100" type="submit">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Tên nguyên liệu</th>
                        <th>Đơn vị</th>
                        <th>Số lượng tồn</th>
                        <th>Tồn tối thiểu</th>
                        <th>Trạng thái tồn</th>
                        <th>Trạng thái sử dụng</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; ?>
                    <?php while ($item = mysqli_fetch_assoc($ingredients)) { ?>
                        <tr>
                            <td><?php echo $stt++; ?></td>
                            <td class="fw-semibold"><?php echo e($item['ingredient_name']); ?></td>
                            <td><?php echo e($item['unit']); ?></td>
                            <td><?php echo number_format((float) $item['quantity'], 2, ',', '.'); ?></td>
                            <td><?php echo number_format((float) $item['min_quantity'], 2, ',', '.'); ?></td>
                            <td>
                                <?php if ((float) $item['quantity'] <= (float) $item['min_quantity']) { ?>
                                    <span class="badge text-bg-danger">Sắp hết</span>
                                <?php } else { ?>
                                    <span class="badge text-bg-success">Đủ hàng</span>
                                <?php } ?>
                            </td>
                            <td><span class="badge <?php echo $item['status'] === 'Đang dùng' ? 'text-bg-primary' : 'text-bg-secondary'; ?>"><?php echo e($item['status']); ?></span></td>
                            <td><?php echo e($item['created_at']); ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="/huongviet/admin/inventory/edit_ingredient.php?id=<?php echo (int) $item['id']; ?>">Sửa</a>
                                <a class="btn btn-sm btn-outline-danger" href="/huongviet/admin/inventory/delete_ingredient.php?id=<?php echo (int) $item['id']; ?>" onclick="return confirm('Bạn có chắc muốn ngừng dùng nguyên liệu này không?');">Ngừng dùng</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php inventoryFooter(); ?>
