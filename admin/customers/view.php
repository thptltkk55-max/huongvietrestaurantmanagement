<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: /huongviet/admin/customers/index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM customers WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$customerResult = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($customerResult);

if (!$customer) {
    header("Location: /huongviet/admin/customers/index.php");
    exit;
}

$sql = "
    SELECT 
        b.id,
        DATE(b.booking_date) AS booking_date,
        b.booking_time,
        b.number_of_people,
        b.note,
        b.status,
        b.created_at,
        t.table_name
    FROM bookings b
    LEFT JOIN tables_restaurant t ON b.table_id = t.id
    WHERE b.customer_id = ?
    ORDER BY b.booking_date DESC, b.booking_time DESC, b.id DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$bookings = mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết khách hàng - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/customers.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <h1 class="h3 mb-0">Chi tiết khách hàng</h1>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="/huongviet/admin/customers/index.php">Quay lại danh sách</a>
                <a class="btn btn-primary" href="/huongviet/admin/customers/edit.php?id=<?php echo (int) $customer['id']; ?>">Sửa khách hàng</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Tên khách:</strong> <?php echo e($customer['customer_name']); ?></div>
                    <div class="col-md-6"><strong>Số điện thoại:</strong> <?php echo e($customer['phone']); ?></div>
                    <div class="col-md-6"><strong>Email:</strong> <?php echo e($customer['email']); ?></div>
                    <div class="col-md-6"><strong>Địa chỉ:</strong> <?php echo e($customer['address']); ?></div>
                    <div class="col-md-6"><strong>Loại khách:</strong> <?php echo e($customer['customer_type']); ?></div>
                    <div class="col-md-6"><strong>Trạng thái:</strong> <?php echo e($customer['status']); ?></div>
                    <div class="col-md-6"><strong>Ngày tạo:</strong> <?php echo e($customer['created_at']); ?></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h5 mb-3">Lịch sử đặt bàn</h2>

                <?php if (mysqli_num_rows($bookings) > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Bàn</th>
                                    <th>Ngày đặt</th>
                                    <th>Giờ đặt</th>
                                    <th>Số người</th>
                                    <th>Trạng thái</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt = 1; ?>
                                <?php while ($booking = mysqli_fetch_assoc($bookings)) { ?>
                                    <tr>
                                        <td><?php echo $stt++; ?></td>
                                        <td><?php echo e($booking['table_name']); ?></td>
                                        <td><?php echo e($booking['booking_date']); ?></td>
                                        <td><?php echo e(substr((string) $booking['booking_time'], 0, 5)); ?></td>
                                        <td><?php echo (int) $booking['number_of_people']; ?></td>
                                        <td><?php echo e($booking['status']); ?></td>
                                        <td><?php echo e($booking['note']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-muted mb-0">Khách hàng này chưa có lịch sử đặt bàn.</p>
                <?php } ?>
            </div>
        </div>
    </main>
</body>
</html>
