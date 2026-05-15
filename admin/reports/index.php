<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
reportRequireManager();

function reportScalar(mysqli $conn, string $sql)
{
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return 0;
    }

    $row = mysqli_fetch_row($result);
    return $row[0] ?? 0;
}

$todayRevenue = reportScalar($conn, "SELECT COALESCE(SUM(final_amount), 0) FROM orders WHERE payment_status = 'Đã thanh toán' AND order_status = 'Đã hoàn thành' AND DATE(order_date) = CURDATE()");
$monthRevenue = reportScalar($conn, "SELECT COALESCE(SUM(final_amount), 0) FROM orders WHERE payment_status = 'Đã thanh toán' AND order_status = 'Đã hoàn thành' AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())");
$paidOrders = reportScalar($conn, "SELECT COUNT(*) FROM orders WHERE payment_status = 'Đã thanh toán'");
$totalBookings = reportScalar($conn, "SELECT COUNT(*) FROM bookings");
$totalCustomers = reportScalar($conn, "SELECT COUNT(*) FROM customers");
$activeFoods = reportScalar($conn, "SELECT COUNT(*) FROM foods WHERE status = 'Còn bán'");
$lowIngredients = tableExists($conn, 'ingredients')
    ? reportScalar($conn, "SELECT COUNT(*) FROM ingredients WHERE status = 'Đang dùng' AND quantity <= min_quantity")
    : 0;

$bestSelling = null;
$bestResult = mysqli_query($conn, "
    SELECT f.food_name, SUM(od.quantity) AS total_quantity
    FROM order_details od
    INNER JOIN orders o ON od.order_id = o.id
    INNER JOIN foods f ON od.food_id = f.id
    WHERE o.payment_status = 'Đã thanh toán'
    AND o.order_status = 'Đã hoàn thành'
    GROUP BY f.id, f.food_name
    ORDER BY total_quantity DESC
    LIMIT 1
");
if ($bestResult) {
    $bestSelling = mysqli_fetch_assoc($bestResult);
}

reportHeader("Báo cáo thống kê");
?>
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Báo cáo thống kê</h1>
            <p class="text-muted mb-0">Tổng quan hoạt động nhà hàng Hương Việt.</p>
        </div>
        <a class="btn btn-outline-secondary" href="/huongviet/admin/dashboard.php">Quay lại dashboard</a>
    </div>

    <div class="row g-3 mb-4">
        <?php
        $cards = [
            ['label' => 'Doanh thu hôm nay', 'value' => reportMoney($todayRevenue), 'class' => 'text-bg-success'],
            ['label' => 'Doanh thu tháng này', 'value' => reportMoney($monthRevenue), 'class' => 'text-bg-primary'],
            ['label' => 'Hóa đơn đã thanh toán', 'value' => (int) $paidOrders, 'class' => 'text-bg-info'],
            ['label' => 'Tổng đơn đặt bàn', 'value' => (int) $totalBookings, 'class' => 'text-bg-warning'],
            ['label' => 'Tổng khách hàng', 'value' => (int) $totalCustomers, 'class' => 'text-bg-secondary'],
            ['label' => 'Món đang bán', 'value' => (int) $activeFoods, 'class' => 'text-bg-danger'],
            ['label' => 'Nguyên liệu sắp hết', 'value' => (int) $lowIngredients, 'class' => 'text-bg-dark'],
            ['label' => 'Món bán chạy nhất', 'value' => $bestSelling ? e($bestSelling['food_name']) . ' (' . (int) $bestSelling['total_quantity'] . ')' : 'Chưa có dữ liệu', 'class' => 'text-bg-light'],
        ];
        ?>

        <?php foreach ($cards as $card) { ?>
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0 <?php echo e($card['class']); ?>">
                    <div class="card-body">
                        <div class="small fw-semibold opacity-75"><?php echo e($card['label']); ?></div>
                        <div class="h4 mb-0 mt-2"><?php echo $card['value']; ?></div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row g-3">
        <?php
        $links = [
            ['title' => 'Báo cáo doanh thu', 'href' => '/huongviet/admin/reports/revenue.php'],
            ['title' => 'Món bán chạy', 'href' => '/huongviet/admin/reports/best_selling.php'],
            ['title' => 'Báo cáo đặt bàn', 'href' => '/huongviet/admin/reports/bookings.php'],
            ['title' => 'Báo cáo khách hàng', 'href' => '/huongviet/admin/reports/customers.php'],
            ['title' => 'Báo cáo kho', 'href' => '/huongviet/admin/reports/inventory.php'],
        ];
        ?>

        <?php foreach ($links as $link) { ?>
            <div class="col-md-6 col-xl-4">
                <a class="card card-body text-decoration-none text-dark border-0 shadow-sm h-100" href="<?php echo e($link['href']); ?>">
                    <span class="fw-bold"><?php echo e($link['title']); ?></span>
                    <span class="text-muted small mt-1">Xem chi tiết</span>
                </a>
            </div>
        <?php } ?>
    </div>
</main>
<?php reportFooter(); ?>
