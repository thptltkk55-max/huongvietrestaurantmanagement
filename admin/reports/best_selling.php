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
$limit = (int) ($_GET['limit'] ?? 10);
$limit = max(1, min(100, $limit));

if ($fromDate > $toDate) {
    [$fromDate, $toDate] = [$toDate, $fromDate];
}

$stmt = mysqli_prepare($conn, "
    SELECT f.id,
           f.food_name,
           f.image,
           c.category_name,
           SUM(od.quantity) AS total_quantity,
           SUM(od.total_price) AS total_sales
    FROM order_details od
    INNER JOIN orders o ON od.order_id = o.id
    INNER JOIN foods f ON od.food_id = f.id
    LEFT JOIN food_categories c ON f.category_id = c.id
    WHERE o.payment_status = 'Đã thanh toán'
    AND o.order_status = 'Đã hoàn thành'
    AND DATE(o.order_date) BETWEEN ? AND ?
    GROUP BY f.id, f.food_name, f.image, c.category_name
    ORDER BY total_quantity DESC, total_sales DESC
    LIMIT ?
");
mysqli_stmt_bind_param($stmt, "ssi", $fromDate, $toDate, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

reportHeader("Món bán chạy");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Món bán chạy</h1>
        <a class="btn btn-outline-secondary" href="/huongviet/admin/reports/index.php">Quay lại báo cáo</a>
    </div>

    <form class="card card-body shadow-sm border-0 mb-4" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Từ ngày</label>
                <input class="form-control" type="date" name="from_date" value="<?php echo e($fromDate); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Đến ngày</label>
                <input class="form-control" type="date" name="to_date" value="<?php echo e($toDate); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Số món</label>
                <input class="form-control" type="number" min="1" max="100" name="limit" value="<?php echo (int) $limit; ?>">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" type="submit">Lọc báo cáo</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Hạng</th>
                            <th>Ảnh</th>
                            <th>Tên món</th>
                            <th>Danh mục</th>
                            <th>Số lượng bán</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td class="fw-bold">#<?php echo $rank++; ?></td>
                                <td style="width: 90px;">
                                    <?php if (!empty($row['image'])) { ?>
                                        <img src="/huongviet/assets/images/<?php echo e($row['image']); ?>" alt="<?php echo e($row['food_name']); ?>" class="img-thumbnail" style="width: 72px; height: 56px; object-fit: cover;">
                                    <?php } else { ?>
                                        <span class="text-muted small">Chưa có ảnh</span>
                                    <?php } ?>
                                </td>
                                <td><?php echo e($row['food_name']); ?></td>
                                <td><?php echo e($row['category_name'] ?? 'Chưa phân loại'); ?></td>
                                <td><?php echo (int) $row['total_quantity']; ?></td>
                                <td class="fw-bold text-success"><?php echo reportMoney($row['total_sales']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($rank === 1) { ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu món bán chạy.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php reportFooter(); ?>
