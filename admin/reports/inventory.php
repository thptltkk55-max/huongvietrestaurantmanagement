<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
reportRequireManager();

reportHeader("Báo cáo kho");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Báo cáo kho nguyên liệu</h1>
        <a class="btn btn-outline-secondary" href="/huongviet/admin/reports/index.php">Quay lại báo cáo</a>
    </div>

    <?php if (!tableExists($conn, 'ingredients')) { ?>
        <div class="alert alert-warning">Chức năng kho chưa có dữ liệu hoặc chưa được khởi tạo.</div>
    <?php } else { ?>
        <?php
        $totalActive = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM ingredients WHERE status = 'Đang dùng'"))[0] ?? 0;
        $totalLow = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM ingredients WHERE status = 'Đang dùng' AND quantity <= min_quantity"))[0] ?? 0;
        $totalInactive = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM ingredients WHERE status = 'Ngừng dùng'"))[0] ?? 0;
        $lowResult = mysqli_query($conn, "SELECT * FROM ingredients WHERE status = 'Đang dùng' AND quantity <= min_quantity ORDER BY quantity ASC");
        ?>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card card-body shadow-sm border-0"><span class="text-muted">Nguyên liệu đang dùng</span><strong class="fs-4"><?php echo (int) $totalActive; ?></strong></div></div>
            <div class="col-md-4"><div class="card card-body shadow-sm border-0"><span class="text-muted">Nguyên liệu sắp hết</span><strong class="fs-4 text-danger"><?php echo (int) $totalLow; ?></strong></div></div>
            <div class="col-md-4"><div class="card card-body shadow-sm border-0"><span class="text-muted">Nguyên liệu ngừng dùng</span><strong class="fs-4 text-secondary"><?php echo (int) $totalInactive; ?></strong></div></div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold">Danh sách nguyên liệu sắp hết</div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Nguyên liệu</th>
                            <th>Đơn vị</th>
                            <th>Tồn kho</th>
                            <th>Tối thiểu</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($lowResult)) { ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo e($row['ingredient_name']); ?></td>
                                <td><?php echo e($row['unit']); ?></td>
                                <td><?php echo e($row['quantity']); ?></td>
                                <td><?php echo e($row['min_quantity']); ?></td>
                                <td><span class="badge text-bg-danger">Sắp hết</span></td>
                            </tr>
                        <?php } ?>
                        <?php if ($i === 1) { ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Không có nguyên liệu sắp hết.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (tableExists($conn, 'import_receipts')) { ?>
            <?php
            $importResult = mysqli_query($conn, "
                SELECT ir.id,
                       ir.import_date,
                       ir.total_amount,
                       s.supplier_name
                FROM import_receipts ir
                LEFT JOIN suppliers s ON ir.supplier_id = s.id
                ORDER BY ir.id DESC
                LIMIT 10
            ");
            ?>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Phiếu nhập gần đây</div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Ngày nhập</th>
                                <th>Nhà cung cấp</th>
                                <th>Tổng tiền</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $hasImport = false; ?>
                            <?php while ($row = mysqli_fetch_assoc($importResult)) { ?>
                                <?php $hasImport = true; ?>
                                <tr>
                                    <td>#<?php echo (int) $row['id']; ?></td>
                                    <td><?php echo e(reportDate($row['import_date'])); ?></td>
                                    <td><?php echo e($row['supplier_name'] ?? 'Không chọn'); ?></td>
                                    <td><?php echo reportMoney($row['total_amount']); ?></td>
                                    <td><a class="btn btn-outline-primary btn-sm" href="/huongviet/admin/inventory/view_import.php?id=<?php echo (int) $row['id']; ?>">Xem</a></td>
                                </tr>
                            <?php } ?>
                            <?php if (!$hasImport) { ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Chưa có phiếu nhập kho.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</main>
<?php reportFooter(); ?>
