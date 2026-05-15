<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
reportRequireManager();

$types = ['Khách VIP' => 0, 'Khách quen' => 0, 'Khách vãng lai' => 0];
$totalCustomers = 0;
$result = mysqli_query($conn, "SELECT customer_type, COUNT(*) AS total FROM customers WHERE status = 'Hoạt động' GROUP BY customer_type");
while ($result && $row = mysqli_fetch_assoc($result)) {
    if (isset($types[$row['customer_type']])) {
        $types[$row['customer_type']] = (int) $row['total'];
    }
    $totalCustomers += (int) $row['total'];
}

$topResult = mysqli_query($conn, "
    SELECT c.id,
           c.customer_name,
           c.phone,
           c.customer_type,
           COUNT(b.id) AS total_bookings
    FROM customers c
    LEFT JOIN bookings b ON c.id = b.customer_id
    WHERE c.status = 'Hoạt động'
    GROUP BY c.id, c.customer_name, c.phone, c.customer_type
    ORDER BY total_bookings DESC
    LIMIT 10
");

function customerTypeBadge($type)
{
    return match ($type) {
        'Khách VIP' => 'danger',
        'Khách quen' => 'success',
        default => 'secondary',
    };
}

reportHeader("Báo cáo khách hàng");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Báo cáo khách hàng</h1>
        <a class="btn btn-outline-secondary" href="/huongviet/admin/reports/index.php">Quay lại báo cáo</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card card-body shadow-sm border-0"><span class="text-muted">Tổng khách hàng</span><strong class="fs-4"><?php echo (int) $totalCustomers; ?></strong></div></div>
        <div class="col-md-3"><div class="card card-body shadow-sm border-0"><span class="text-muted">Khách VIP</span><strong class="fs-4 text-danger"><?php echo $types['Khách VIP']; ?></strong></div></div>
        <div class="col-md-3"><div class="card card-body shadow-sm border-0"><span class="text-muted">Khách quen</span><strong class="fs-4 text-success"><?php echo $types['Khách quen']; ?></strong></div></div>
        <div class="col-md-3"><div class="card card-body shadow-sm border-0"><span class="text-muted">Khách vãng lai</span><strong class="fs-4 text-secondary"><?php echo $types['Khách vãng lai']; ?></strong></div></div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">Top khách đặt bàn nhiều nhất</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Hạng</th>
                        <th>Tên khách</th>
                        <th>Số điện thoại</th>
                        <th>Loại khách</th>
                        <th>Số lần đặt bàn</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($topResult)) { ?>
                        <tr>
                            <td class="fw-bold">#<?php echo $rank++; ?></td>
                            <td><?php echo e($row['customer_name']); ?></td>
                            <td><?php echo e($row['phone']); ?></td>
                            <td><span class="badge text-bg-<?php echo customerTypeBadge($row['customer_type']); ?>"><?php echo e($row['customer_type']); ?></span></td>
                            <td><?php echo (int) $row['total_bookings']; ?></td>
                            <td><a class="btn btn-outline-primary btn-sm" href="/huongviet/admin/customers/view.php?id=<?php echo (int) $row['id']; ?>">Xem</a></td>
                        </tr>
                    <?php } ?>
                    <?php if ($rank === 1) { ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu khách hàng.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php reportFooter(); ?>
