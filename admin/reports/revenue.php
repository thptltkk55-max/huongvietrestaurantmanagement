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

if ($fromDate > $toDate) {
    [$fromDate, $toDate] = [$toDate, $fromDate];
}

$rows = [];
$summary = [
    'orders' => 0,
    'before_discount' => 0,
    'discount' => 0,
    'revenue' => 0,
];

$stmt = mysqli_prepare($conn, "
    SELECT DATE(order_date) AS report_date,
           COUNT(id) AS total_orders,
           COALESCE(SUM(total_amount), 0) AS total_before_discount,
           COALESCE(SUM(discount), 0) AS total_discount,
           COALESCE(SUM(final_amount), 0) AS total_revenue
    FROM orders
    WHERE payment_status = 'Đã thanh toán'
    AND order_status = 'Đã hoàn thành'
    AND DATE(order_date) BETWEEN ? AND ?
    GROUP BY DATE(order_date)
    ORDER BY report_date DESC
");
mysqli_stmt_bind_param($stmt, "ss", $fromDate, $toDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
    $summary['orders'] += (int) $row['total_orders'];
    $summary['before_discount'] += (float) $row['total_before_discount'];
    $summary['discount'] += (float) $row['total_discount'];
    $summary['revenue'] += (float) $row['total_revenue'];
}

reportHeader("Báo cáo doanh thu");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Báo cáo doanh thu</h1>
        <a class="btn btn-outline-secondary" href="/huongviet/admin/reports/index.php">Quay lại báo cáo</a>
    </div>

    <form class="card card-body shadow-sm border-0 mb-4" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Từ ngày</label>
                <input class="form-control" type="date" name="from_date" value="<?php echo e($fromDate); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Đến ngày</label>
                <input class="form-control" type="date" name="to_date" value="<?php echo e($toDate); ?>">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100" type="submit">Lọc báo cáo</button>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card card-body shadow-sm border-0"><span class="text-muted">Tổng doanh thu</span><strong class="fs-5"><?php echo reportMoney($summary['before_discount']); ?></strong></div></div>
        <div class="col-md-3"><div class="card card-body shadow-sm border-0"><span class="text-muted">Tổng hóa đơn</span><strong class="fs-5"><?php echo (int) $summary['orders']; ?></strong></div></div>
        <div class="col-md-3"><div class="card card-body shadow-sm border-0"><span class="text-muted">Tổng giảm giá</span><strong class="fs-5"><?php echo reportMoney($summary['discount']); ?></strong></div></div>
        <div class="col-md-3"><div class="card card-body shadow-sm border-0"><span class="text-muted">Thực nhận</span><strong class="fs-5 text-success"><?php echo reportMoney($summary['revenue']); ?></strong></div></div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <?php if (!$rows) { ?>
                <div class="alert alert-info mb-0">Chưa có dữ liệu doanh thu trong khoảng thời gian này.</div>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Số hóa đơn</th>
                                <th>Tổng tiền trước giảm</th>
                                <th>Tổng giảm giá</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row) { ?>
                                <tr>
                                    <td><?php echo e(reportDate($row['report_date'])); ?></td>
                                    <td><?php echo (int) $row['total_orders']; ?></td>
                                    <td><?php echo reportMoney($row['total_before_discount']); ?></td>
                                    <td><?php echo reportMoney($row['total_discount']); ?></td>
                                    <td class="fw-bold text-success"><?php echo reportMoney($row['total_revenue']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</main>
<?php reportFooter(); ?>
