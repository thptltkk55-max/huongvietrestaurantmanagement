<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: /huongviet/admin/bookings/index.php");
    exit;
}

$sql = "
    SELECT
        b.*,
        DATE(b.booking_date) AS display_booking_date,
        c.customer_name,
        c.phone,
        c.email,
        c.address,
        c.customer_type,
        t.table_name,
        t.capacity
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    LEFT JOIN tables_restaurant t ON b.table_id = t.id
    WHERE b.id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    header("Location: /huongviet/admin/bookings/index.php");
    exit;
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết đặt bàn - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/bookings.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Chi tiết đặt bàn #<?php echo (int) $booking['id']; ?></h1>
            <a class="btn btn-outline-secondary" href="/huongviet/admin/bookings/index.php">Quay lại danh sách</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Thông tin khách hàng</h2>
                        <p><strong>Họ tên:</strong> <?php echo e($booking['customer_name']); ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo e($booking['phone']); ?></p>
                        <p><strong>Email:</strong> <?php echo e($booking['email']); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo e($booking['address']); ?></p>
                        <p class="mb-0"><strong>Loại khách:</strong> <?php echo e($booking['customer_type']); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Thông tin đặt bàn</h2>
                        <p><strong>Bàn:</strong> <?php echo e($booking['table_name']); ?> (<?php echo (int) $booking['capacity']; ?> khách)</p>
                        <p><strong>Ngày giờ:</strong> <?php echo e($booking['display_booking_date'] . " " . substr((string) $booking['booking_time'], 0, 5)); ?></p>
                        <p><strong>Số người:</strong> <?php echo (int) $booking['number_of_people']; ?></p>
                        <p><strong>Trạng thái:</strong> <?php echo e($booking['status']); ?></p>
                        <p><strong>Thời gian tạo:</strong> <?php echo e($booking['created_at']); ?></p>
                        <p class="mb-0"><strong>Ghi chú:</strong> <?php echo nl2br(e($booking['note'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
