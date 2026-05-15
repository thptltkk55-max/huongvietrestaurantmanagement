<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_bootstrap.php";

$serviceStatus = trim($_GET['service_status'] ?? '');
$staffId = (int) ($_GET['staff_id'] ?? 0);
$tableId = (int) ($_GET['table_id'] ?? 0);
$successMessage = trim($_GET['success'] ?? '');
$errorMessage = trim($_GET['error'] ?? '');
$validServiceStatuses = serviceValidStatuses();

if (!in_array($serviceStatus, $validServiceStatuses, true)) {
    $serviceStatus = '';
}

$tableStats = array_fill_keys(tableValidStatuses(), 0);
$tableStatsResult = mysqli_query($conn, "SELECT status, COUNT(*) AS total FROM tables_restaurant GROUP BY status");
while ($tableStatsResult && $row = mysqli_fetch_assoc($tableStatsResult)) {
    if (isset($tableStats[$row['status']])) {
        $tableStats[$row['status']] = (int) $row['total'];
    }
}
$totalTables = array_sum($tableStats);

$waitingServices = 0;
$servingServices = 0;
$serviceStatsResult = mysqli_query($conn, "SELECT service_status, COUNT(*) AS total FROM service_assignments WHERE service_status IN ('Chờ phục vụ', 'Đang phục vụ') GROUP BY service_status");
while ($serviceStatsResult && $row = mysqli_fetch_assoc($serviceStatsResult)) {
    if ($row['service_status'] === 'Chờ phục vụ') {
        $waitingServices = (int) $row['total'];
    }
    if ($row['service_status'] === 'Đang phục vụ') {
        $servingServices = (int) $row['total'];
    }
}

$tables = mysqli_query($conn, "SELECT id, table_name, capacity, status FROM tables_restaurant ORDER BY id ASC");
$staffList = mysqli_query($conn, "
    SELECT u.id, u.full_name, u.username
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE u.status = 'Hoạt động'
    AND (r.role_name = 'Phục vụ' OR r.role_group = 'Nhân viên')
    ORDER BY u.full_name ASC
");
$tableFilterList = mysqli_query($conn, "SELECT id, table_name FROM tables_restaurant ORDER BY id ASC");

$sql = "
    SELECT 
        sa.id,
        sa.table_id,
        sa.booking_id,
        sa.order_id,
        sa.staff_id,
        sa.service_status,
        sa.note,
        sa.created_at,
        sa.completed_at,
        t.table_name,
        b.booking_date,
        b.booking_time,
        o.id AS order_code,
        u.full_name AS staff_name
    FROM service_assignments sa
    LEFT JOIN tables_restaurant t ON sa.table_id = t.id
    LEFT JOIN bookings b ON sa.booking_id = b.id
    LEFT JOIN orders o ON sa.order_id = o.id
    LEFT JOIN users u ON sa.staff_id = u.id
    WHERE 1
";
$params = [];
$types = '';

if ($serviceStatus !== '') {
    $sql .= " AND sa.service_status = ?";
    $params[] = $serviceStatus;
    $types .= 's';
}

if ($staffId > 0) {
    $sql .= " AND sa.staff_id = ?";
    $params[] = $staffId;
    $types .= 'i';
}

if ($tableId > 0) {
    $sql .= " AND sa.table_id = ?";
    $params[] = $tableId;
    $types .= 'i';
}

$sql .= " ORDER BY sa.id DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$tasks = mysqli_stmt_get_result($stmt);

serviceHeader("Quản lý phục vụ");
?>
<main class="admin-container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 admin-page-title">
        <div>
            <h1>Quản lý phục vụ</h1>
            <p>Theo dõi bàn, phân công nhân viên và cập nhật trạng thái phục vụ.</p>
        </div>
        <div class="admin-actions">
            <a class="admin-btn admin-btn-primary" href="/huongviet/admin/service/add.php">Tạo nhiệm vụ phục vụ</a>
            <a class="admin-btn admin-btn-outline" href="/huongviet/admin/dashboard.php">Dashboard</a>
        </div>
    </div>

    <?php if ($successMessage !== '') { ?>
        <div class="admin-alert admin-alert-success"><?php echo e($successMessage); ?></div>
    <?php } ?>
    <?php if ($errorMessage !== '') { ?>
        <div class="admin-alert admin-alert-error"><?php echo e($errorMessage); ?></div>
    <?php } ?>

    <div class="service-status-grid">
        <div class="service-card"><span>Tổng bàn</span><strong><?php echo (int) $totalTables; ?></strong></div>
        <div class="service-card"><span>Bàn trống</span><strong><?php echo $tableStats['Trống']; ?></strong></div>
        <div class="service-card"><span>Bàn đã đặt</span><strong><?php echo $tableStats['Đã đặt']; ?></strong></div>
        <div class="service-card"><span>Bàn đang sử dụng</span><strong><?php echo $tableStats['Đang sử dụng']; ?></strong></div>
        <div class="service-card"><span>Bàn bảo trì</span><strong><?php echo $tableStats['Bảo trì']; ?></strong></div>
        <div class="service-card"><span>Chờ phục vụ</span><strong><?php echo (int) $waitingServices; ?></strong></div>
        <div class="service-card"><span>Đang phục vụ</span><strong><?php echo (int) $servingServices; ?></strong></div>
    </div>

    <div class="admin-table-card mb-4">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên bàn</th>
                        <th>Sức chứa</th>
                        <th>Trạng thái bàn</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $stt = 1; ?>
                    <?php while ($table = mysqli_fetch_assoc($tables)) { ?>
                        <tr>
                            <td><?php echo $stt++; ?></td>
                            <td><?php echo e($table['table_name']); ?></td>
                            <td><?php echo (int) $table['capacity']; ?></td>
                            <td><span class="admin-badge <?php echo tableStatusBadge($table['status']); ?>"><?php echo e($table['status']); ?></span></td>
                            <td>
                                <div class="service-actions">
                                    <a class="admin-btn admin-btn-outline" href="/huongviet/admin/service/table_status.php?id=<?php echo (int) $table['id']; ?>">Đổi trạng thái</a>
                                    <a class="admin-btn admin-btn-primary" href="/huongviet/admin/service/add.php?table_id=<?php echo (int) $table['id']; ?>">Tạo nhiệm vụ</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card">
        <form class="admin-filter-form" method="get">
            <div>
                <label>Trạng thái phục vụ</label>
                <select name="service_status">
                    <option value="">Tất cả</option>
                    <?php foreach ($validServiceStatuses as $item) { ?>
                        <option value="<?php echo e($item); ?>" <?php echo $serviceStatus === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label>Nhân viên</label>
                <select name="staff_id">
                    <option value="0">Tất cả</option>
                    <?php while ($staff = mysqli_fetch_assoc($staffList)) { ?>
                        <option value="<?php echo (int) $staff['id']; ?>" <?php echo $staffId === (int) $staff['id'] ? 'selected' : ''; ?>>
                            <?php echo e($staff['full_name'] ?: $staff['username']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label>Bàn</label>
                <select name="table_id">
                    <option value="0">Tất cả</option>
                    <?php while ($table = mysqli_fetch_assoc($tableFilterList)) { ?>
                        <option value="<?php echo (int) $table['id']; ?>" <?php echo $tableId === (int) $table['id'] ? 'selected' : ''; ?>>
                            <?php echo e($table['table_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <button class="admin-btn admin-btn-primary" type="submit">Lọc</button>
            </div>
        </form>
    </div>

    <div class="admin-table-card service-task-list">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Bàn</th>
                        <th>Booking</th>
                        <th>Order</th>
                        <th>Nhân viên</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                        <th>Thời gian tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $hasTask = false; ?>
                    <?php while ($task = mysqli_fetch_assoc($tasks)) { ?>
                        <?php $hasTask = true; ?>
                        <tr>
                            <td>#<?php echo (int) $task['id']; ?></td>
                            <td><?php echo e($task['table_name'] ?? ''); ?></td>
                            <td><?php echo $task['booking_id'] ? '#' . (int) $task['booking_id'] . ' - ' . e($task['booking_date'] . ' ' . substr((string) $task['booking_time'], 0, 5)) : ''; ?></td>
                            <td><?php echo $task['order_id'] ? '#' . (int) $task['order_id'] : ''; ?></td>
                            <td><?php echo e($task['staff_name'] ?? 'Chưa gán'); ?></td>
                            <td><span class="admin-badge service-badge <?php echo serviceStatusBadge($task['service_status']); ?>"><?php echo e($task['service_status']); ?></span></td>
                            <td><?php echo e(mb_strimwidth((string) $task['note'], 0, 80, '...')); ?></td>
                            <td><?php echo e($task['created_at']); ?></td>
                            <td>
                                <div class="service-actions">
                                    <a class="admin-btn admin-btn-outline" href="/huongviet/admin/service/view.php?id=<?php echo (int) $task['id']; ?>">Xem</a>
                                    <?php if ($task['service_status'] === 'Chờ phục vụ') { ?>
                                        <a class="admin-btn admin-btn-primary" href="/huongviet/admin/service/assign_staff.php?id=<?php echo (int) $task['id']; ?>">Gán NV</a>
                                        <a class="admin-btn admin-btn-success" href="/huongviet/admin/service/update_status.php?id=<?php echo (int) $task['id']; ?>&status=<?php echo urlencode('Đang phục vụ'); ?>">Bắt đầu</a>
                                        <a class="admin-btn admin-btn-danger" href="/huongviet/admin/service/update_status.php?id=<?php echo (int) $task['id']; ?>&status=<?php echo urlencode('Đã hủy'); ?>" onclick="return confirm('Bạn có chắc muốn hủy nhiệm vụ này không?');">Hủy</a>
                                    <?php } elseif ($task['service_status'] === 'Đang phục vụ') { ?>
                                        <a class="admin-btn admin-btn-success" href="/huongviet/admin/service/update_status.php?id=<?php echo (int) $task['id']; ?>&status=<?php echo urlencode('Hoàn thành'); ?>">Hoàn thành</a>
                                        <a class="admin-btn admin-btn-danger" href="/huongviet/admin/service/update_status.php?id=<?php echo (int) $task['id']; ?>&status=<?php echo urlencode('Đã hủy'); ?>" onclick="return confirm('Bạn có chắc muốn hủy nhiệm vụ này không?');">Hủy</a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!$hasTask) { ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Chưa có nhiệm vụ phục vụ.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php serviceFooter(); ?>
