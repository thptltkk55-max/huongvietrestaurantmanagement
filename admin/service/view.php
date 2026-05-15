<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_bootstrap.php";

$id = (int) ($_GET['id'] ?? 0);
$task = null;

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "
        SELECT sa.*, t.table_name, t.capacity, b.booking_date, b.booking_time, o.id AS order_code, u.full_name AS staff_name
        FROM service_assignments sa
        LEFT JOIN tables_restaurant t ON sa.table_id = t.id
        LEFT JOIN bookings b ON sa.booking_id = b.id
        LEFT JOIN orders o ON sa.order_id = o.id
        LEFT JOIN users u ON sa.staff_id = u.id
        WHERE sa.id = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$details = null;
if ($task && $task['order_id']) {
    $stmt = mysqli_prepare($conn, "
        SELECT od.quantity, od.price, od.total_price, f.food_name
        FROM order_details od
        LEFT JOIN foods f ON od.food_id = f.id
        WHERE od.order_id = ?
        ORDER BY od.id ASC
    ");
    mysqli_stmt_bind_param($stmt, "i", $task['order_id']);
    mysqli_stmt_execute($stmt);
    $details = mysqli_stmt_get_result($stmt);
}

serviceHeader("Chi tiết nhiệm vụ phục vụ");
?>
<main class="admin-container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 admin-page-title">
        <div>
            <h1>Chi tiết nhiệm vụ phục vụ</h1>
            <p>Thông tin phân công và trạng thái phục vụ.</p>
        </div>
        <a class="admin-btn admin-btn-outline" href="/huongviet/admin/service/index.php">Quay lại danh sách</a>
    </div>

    <?php if (!$task) { ?>
        <div class="admin-alert admin-alert-error">Không tìm thấy nhiệm vụ phục vụ.</div>
    <?php } else { ?>
        <div class="admin-card">
            <div class="service-detail-grid">
                <div><strong>Mã nhiệm vụ:</strong> #<?php echo (int) $task['id']; ?></div>
                <div><strong>Bàn:</strong> <?php echo e($task['table_name'] ?? ''); ?></div>
                <div><strong>Sức chứa:</strong> <?php echo (int) ($task['capacity'] ?? 0); ?></div>
                <div><strong>Booking:</strong> <?php echo $task['booking_id'] ? '#' . (int) $task['booking_id'] . ' - ' . e($task['booking_date'] . ' ' . substr((string) $task['booking_time'], 0, 5)) : 'Không có'; ?></div>
                <div><strong>Order:</strong> <?php echo $task['order_id'] ? '#' . (int) $task['order_id'] : 'Không có'; ?></div>
                <div><strong>Nhân viên:</strong> <?php echo e($task['staff_name'] ?? 'Chưa gán'); ?></div>
                <div><strong>Trạng thái:</strong> <span class="admin-badge <?php echo serviceStatusBadge($task['service_status']); ?>"><?php echo e($task['service_status']); ?></span></div>
                <div><strong>Thời gian tạo:</strong> <?php echo e($task['created_at']); ?></div>
                <div><strong>Hoàn thành lúc:</strong> <?php echo e($task['completed_at'] ?? ''); ?></div>
            </div>
            <hr>
            <div class="service-message"><strong>Ghi chú:</strong><br><?php echo e($task['note'] ?? ''); ?></div>

            <?php if (!in_array($task['service_status'], ['Hoàn thành', 'Đã hủy'], true)) { ?>
                <div class="service-actions mt-3">
                    <a class="admin-btn admin-btn-primary" href="/huongviet/admin/service/assign_staff.php?id=<?php echo (int) $task['id']; ?>">Gán nhân viên</a>
                    <?php if ($task['service_status'] === 'Chờ phục vụ') { ?>
                        <a class="admin-btn admin-btn-success" href="/huongviet/admin/service/update_status.php?id=<?php echo (int) $task['id']; ?>&status=<?php echo urlencode('Đang phục vụ'); ?>">Bắt đầu phục vụ</a>
                    <?php } ?>
                    <?php if ($task['service_status'] === 'Đang phục vụ') { ?>
                        <a class="admin-btn admin-btn-success" href="/huongviet/admin/service/update_status.php?id=<?php echo (int) $task['id']; ?>&status=<?php echo urlencode('Hoàn thành'); ?>">Hoàn thành</a>
                    <?php } ?>
                    <a class="admin-btn admin-btn-danger" href="/huongviet/admin/service/update_status.php?id=<?php echo (int) $task['id']; ?>&status=<?php echo urlencode('Đã hủy'); ?>" onclick="return confirm('Bạn có chắc muốn hủy nhiệm vụ này không?');">Hủy</a>
                </div>
            <?php } ?>
        </div>

        <?php if ($details) { ?>
            <div class="admin-table-card">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead><tr><th>Tên món</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                        <tbody>
                            <?php while ($detail = mysqli_fetch_assoc($details)) { ?>
                                <tr>
                                    <td><?php echo e($detail['food_name']); ?></td>
                                    <td><?php echo (int) $detail['quantity']; ?></td>
                                    <td><?php echo number_format((float) $detail['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                                    <td><?php echo number_format((float) $detail['total_price'], 0, ',', '.') . ' VNĐ'; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</main>
<?php serviceFooter(); ?>
