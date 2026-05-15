<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_bootstrap.php";

$validStatuses = ['Chờ phục vụ', 'Đang phục vụ'];
$errors = [];
$form = [
    'table_id' => (int) ($_POST['table_id'] ?? $_GET['table_id'] ?? 0),
    'booking_id' => (int) ($_POST['booking_id'] ?? 0),
    'order_id' => (int) ($_POST['order_id'] ?? 0),
    'staff_id' => (int) ($_POST['staff_id'] ?? 0),
    'service_status' => $_POST['service_status'] ?? 'Chờ phục vụ',
    'note' => trim($_POST['note'] ?? ''),
];

if (!in_array($form['service_status'], $validStatuses, true)) {
    $form['service_status'] = 'Chờ phục vụ';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if ($form['table_id'] <= 0 && $form['booking_id'] <= 0 && $form['order_id'] <= 0) {
        $errors[] = "Vui lòng chọn ít nhất một thông tin: bàn, booking hoặc order.";
    }

    if (!$errors) {
        $tableId = $form['table_id'] > 0 ? $form['table_id'] : null;
        $bookingId = $form['booking_id'] > 0 ? $form['booking_id'] : null;
        $orderId = $form['order_id'] > 0 ? $form['order_id'] : null;
        $staffId = $form['staff_id'] > 0 ? $form['staff_id'] : null;

        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare($conn, "
                INSERT INTO service_assignments
                (table_id, booking_id, order_id, staff_id, service_status, note, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            mysqli_stmt_bind_param($stmt, "iiiiss", $tableId, $bookingId, $orderId, $staffId, $form['service_status'], $form['note']);
            mysqli_stmt_execute($stmt);

            if ($tableId && $form['service_status'] === 'Đang phục vụ') {
                serviceUpdateTableStatus($conn, (int) $tableId, 'Đang sử dụng');
            }

            mysqli_commit($conn);
            header("Location: /huongviet/admin/service/index.php?success=" . urlencode("Tạo nhiệm vụ phục vụ thành công."));
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $errors[] = "Không thể tạo nhiệm vụ phục vụ.";
        }
    }
}

$tables = mysqli_query($conn, "SELECT id, table_name, capacity, status FROM tables_restaurant ORDER BY id ASC");
$bookings = mysqli_query($conn, "
    SELECT b.id, b.booking_date, b.booking_time, c.customer_name
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    WHERE b.status = 'Đã xác nhận'
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$orders = mysqli_query($conn, "
    SELECT o.id, o.order_date, t.table_name, c.customer_name
    FROM orders o
    LEFT JOIN tables_restaurant t ON o.table_id = t.id
    LEFT JOIN customers c ON o.customer_id = c.id
    WHERE o.order_status = 'Đang phục vụ'
    ORDER BY o.id DESC
");
$staffs = mysqli_query($conn, "
    SELECT u.id, u.full_name, u.username, r.role_name, r.role_group
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE u.status = 'Hoạt động'
    AND (r.role_name = 'Phục vụ' OR r.role_group = 'Nhân viên')
    ORDER BY u.full_name ASC
");

serviceHeader("Tạo nhiệm vụ phục vụ");
?>
<main class="admin-container">
    <div class="admin-page-title">
        <h1>Tạo nhiệm vụ phục vụ</h1>
        <p>Gắn bàn, booking hoặc order với nhân viên phục vụ.</p>
    </div>

    <?php if ($errors) { ?>
        <div class="admin-alert admin-alert-error">
            <?php foreach ($errors as $error) { ?><div><?php echo e($error); ?></div><?php } ?>
        </div>
    <?php } ?>

    <div class="admin-form-card">
        <form method="post" class="admin-form-grid">
            <div class="admin-form-group">
                <label>Bàn</label>
                <select name="table_id">
                    <option value="0">-- Không chọn --</option>
                    <?php while ($table = mysqli_fetch_assoc($tables)) { ?>
                        <option value="<?php echo (int) $table['id']; ?>" <?php echo $form['table_id'] === (int) $table['id'] ? 'selected' : ''; ?>>
                            <?php echo e($table['table_name'] . ' - ' . $table['status']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="admin-form-group">
                <label>Booking đã xác nhận</label>
                <select name="booking_id">
                    <option value="0">-- Không chọn --</option>
                    <?php while ($booking = mysqli_fetch_assoc($bookings)) { ?>
                        <option value="<?php echo (int) $booking['id']; ?>" <?php echo $form['booking_id'] === (int) $booking['id'] ? 'selected' : ''; ?>>
                            #<?php echo (int) $booking['id']; ?> - <?php echo e(($booking['customer_name'] ?: 'Khách') . ' - ' . $booking['booking_date'] . ' ' . substr((string) $booking['booking_time'], 0, 5)); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="admin-form-group">
                <label>Order đang phục vụ</label>
                <select name="order_id">
                    <option value="0">-- Không chọn --</option>
                    <?php while ($order = mysqli_fetch_assoc($orders)) { ?>
                        <option value="<?php echo (int) $order['id']; ?>" <?php echo $form['order_id'] === (int) $order['id'] ? 'selected' : ''; ?>>
                            #<?php echo (int) $order['id']; ?> - <?php echo e(($order['customer_name'] ?: 'Khách vãng lai') . ' - ' . ($order['table_name'] ?: 'Chưa chọn bàn')); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="admin-form-group">
                <label>Nhân viên phục vụ</label>
                <select name="staff_id">
                    <option value="0">-- Chưa gán --</option>
                    <?php while ($staff = mysqli_fetch_assoc($staffs)) { ?>
                        <option value="<?php echo (int) $staff['id']; ?>" <?php echo $form['staff_id'] === (int) $staff['id'] ? 'selected' : ''; ?>>
                            <?php echo e(($staff['full_name'] ?: $staff['username']) . ' - ' . $staff['role_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="admin-form-group">
                <label>Trạng thái phục vụ</label>
                <select name="service_status">
                    <?php foreach ($validStatuses as $status) { ?>
                        <option value="<?php echo e($status); ?>" <?php echo $form['service_status'] === $status ? 'selected' : ''; ?>><?php echo e($status); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="admin-form-group full-width">
                <label>Ghi chú</label>
                <textarea name="note" rows="4"><?php echo e($form['note']); ?></textarea>
            </div>

            <div class="admin-actions full-width">
                <button class="admin-btn admin-btn-primary" type="submit">Tạo nhiệm vụ</button>
                <a class="admin-btn admin-btn-outline" href="/huongviet/admin/service/index.php">Quay lại</a>
            </div>
        </form>
    </div>
</main>
<?php serviceFooter(); ?>
