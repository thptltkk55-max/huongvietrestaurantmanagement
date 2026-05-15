<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
reportRequireManager();

$today = date('Y-m-d');
$firstDay = date('Y-m-01');
$fromDate = validReportDate($_GET['from_date'] ?? '', $firstDay);
$toDate = validReportDate($_GET['to_date'] ?? '', $today);
$validStatuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đã hủy', 'Đã hoàn thành'];
$status = $_GET['status'] ?? '';
$status = in_array($status, $validStatuses, true) ? $status : '';

if ($fromDate > $toDate) {
    [$fromDate, $toDate] = [$toDate, $fromDate];
}

$statusTotals = array_fill_keys($validStatuses, 0);
$stmt = mysqli_prepare($conn, "SELECT status, COUNT(*) AS total FROM bookings WHERE DATE(booking_date) BETWEEN ? AND ? GROUP BY status");
mysqli_stmt_bind_param($stmt, "ss", $fromDate, $toDate);
mysqli_stmt_execute($stmt);
$statsResult = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($statsResult)) {
    if (isset($statusTotals[$row['status']])) {
        $statusTotals[$row['status']] = (int) $row['total'];
    }
}

$stmtPeople = mysqli_prepare($conn, "SELECT COUNT(*) AS total_bookings, COALESCE(SUM(number_of_people), 0) AS total_people FROM bookings WHERE DATE(booking_date) BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmtPeople, "ss", $fromDate, $toDate);
mysqli_stmt_execute($stmtPeople);
$peopleStats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtPeople));

$sql = "
    SELECT b.id,
           DATE(b.booking_date) AS booking_date,
           b.booking_time,
           b.number_of_people,
           b.status,
           c.customer_name,
           c.phone,
           t.table_name
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    LEFT JOIN tables_restaurant t ON b.table_id = t.id
    WHERE DATE(b.booking_date) BETWEEN ? AND ?
";
if ($status !== '') {
    $sql .= " AND b.status = ?";
}
$sql .= " ORDER BY b.booking_date DESC, b.booking_time DESC, b.id DESC";

$stmtList = mysqli_prepare($conn, $sql);
if ($status !== '') {
    mysqli_stmt_bind_param($stmtList, "sss", $fromDate, $toDate, $status);
} else {
    mysqli_stmt_bind_param($stmtList, "ss", $fromDate, $toDate);
}
mysqli_stmt_execute($stmtList);
$listResult = mysqli_stmt_get_result($stmtList);

function bookingBadgeClass($status)
{
    return match ($status) {
        'Chờ xác nhận' => 'warning',
        'Đã xác nhận' => 'primary',
        'Đã hủy' => 'danger',
        'Đã hoàn thành' => 'success',
        default => 'secondary',
    };
}

reportHeader("Báo cáo đặt bàn");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Báo cáo đặt bàn</h1>
        <a class="btn btn-outline-secondary" href="/huongviet/admin/reports/index.php">Quay lại báo cáo</a>
    </div>

    <form class="card card-body shadow-sm border-0 mb-4" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-md-3"><label class="form-label">Từ ngày</label><input class="form-control" type="date" name="from_date" value="<?php echo e($fromDate); ?>"></div>
            <div class="col-md-3"><label class="form-label">Đến ngày</label><input class="form-control" type="date" name="to_date" value="<?php echo e($toDate); ?>"></div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" name="status">
                    <option value="">Tất cả</option>
                    <?php foreach ($validStatuses as $item) { ?>
                        <option value="<?php echo e($item); ?>" <?php echo $status === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary w-100" type="submit">Lọc báo cáo</button></div>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-4 col-xl-2"><div class="card card-body shadow-sm border-0"><span class="text-muted small">Tổng lượt</span><strong class="fs-5"><?php echo (int) $peopleStats['total_bookings']; ?></strong></div></div>
        <div class="col-md-4 col-xl-2"><div class="card card-body shadow-sm border-0"><span class="text-muted small">Chờ xác nhận</span><strong class="fs-5"><?php echo $statusTotals['Chờ xác nhận']; ?></strong></div></div>
        <div class="col-md-4 col-xl-2"><div class="card card-body shadow-sm border-0"><span class="text-muted small">Đã xác nhận</span><strong class="fs-5"><?php echo $statusTotals['Đã xác nhận']; ?></strong></div></div>
        <div class="col-md-4 col-xl-2"><div class="card card-body shadow-sm border-0"><span class="text-muted small">Đã hủy</span><strong class="fs-5"><?php echo $statusTotals['Đã hủy']; ?></strong></div></div>
        <div class="col-md-4 col-xl-2"><div class="card card-body shadow-sm border-0"><span class="text-muted small">Hoàn thành</span><strong class="fs-5"><?php echo $statusTotals['Đã hoàn thành']; ?></strong></div></div>
        <div class="col-md-4 col-xl-2"><div class="card card-body shadow-sm border-0"><span class="text-muted small">Khách dự kiến</span><strong class="fs-5"><?php echo (int) $peopleStats['total_people']; ?></strong></div></div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Bàn</th>
                        <th>Ngày đặt</th>
                        <th>Giờ</th>
                        <th>Số người</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($listResult)) { ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo e($row['customer_name'] ?? 'Khách vãng lai'); ?></td>
                            <td><?php echo e($row['phone'] ?? ''); ?></td>
                            <td><?php echo e($row['table_name'] ?? 'Chưa chọn'); ?></td>
                            <td><?php echo e(reportDate($row['booking_date'])); ?></td>
                            <td><?php echo e(substr((string) $row['booking_time'], 0, 5)); ?></td>
                            <td><?php echo (int) $row['number_of_people']; ?></td>
                            <td><span class="badge text-bg-<?php echo bookingBadgeClass($row['status']); ?>"><?php echo e($row['status']); ?></span></td>
                        </tr>
                    <?php } ?>
                    <?php if ($i === 1) { ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có dữ liệu đặt bàn.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php reportFooter(); ?>
