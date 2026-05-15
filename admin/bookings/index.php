<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();

$date = trim($_GET['booking_date'] ?? '');
$status = trim($_GET['status'] ?? '');
$phone = trim($_GET['phone'] ?? '');
$customerName = trim($_GET['customer_name'] ?? '');
$successMessage = trim($_GET['success'] ?? '');
$errorMessage = trim($_GET['error'] ?? '');
$validStatuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đã hủy', 'Đã hoàn thành'];

$where = [];
$params = [];
$types = "";

if ($date !== "") {
    $where[] = "DATE(b.booking_date) = ?";
    $params[] = $date;
    $types .= "s";
}

if (in_array($status, $validStatuses, true)) {
    $where[] = "b.status = ?";
    $params[] = $status;
    $types .= "s";
} else {
    $status = "";
}

if ($phone !== "") {
    $where[] = "c.phone LIKE ?";
    $params[] = "%" . $phone . "%";
    $types .= "s";
}

if ($customerName !== "") {
    $where[] = "c.customer_name LIKE ?";
    $params[] = "%" . $customerName . "%";
    $types .= "s";
}

$sql = "
    SELECT
        b.id,
        DATE(b.booking_date) AS booking_date,
        b.booking_time,
        b.number_of_people,
        b.note,
        b.status,
        c.customer_name,
        c.phone,
        c.customer_type,
        t.table_name
    FROM bookings b
    LEFT JOIN customers c ON b.customer_id = c.id
    LEFT JOIN tables_restaurant t ON b.table_id = t.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY b.booking_date DESC, b.booking_time DESC, b.id DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($types !== "") {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$bookings = mysqli_stmt_get_result($stmt);

function bookingBadge($status)
{
    return match ($status) {
        'Chờ xác nhận' => 'text-bg-warning',
        'Đã xác nhận' => 'text-bg-primary',
        'Đã hủy' => 'text-bg-danger',
        'Đã hoàn thành' => 'text-bg-success',
        default => 'text-bg-secondary',
    };
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý đặt bàn - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/bookings.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/huongviet/admin/dashboard.php">Hương Việt Admin</a>
            <a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/logout.php">Đăng xuất</a>
        </div>
    </nav>

    <main class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h1 class="h3 mb-1">Quản lý đặt bàn</h1>
                <p class="text-muted mb-0">Theo dõi, xác nhận, hủy và hoàn thành lịch đặt bàn.</p>
            </div>
            <a class="btn btn-outline-secondary" href="/huongviet/admin/dashboard.php">Dashboard</a>
        </div>

        <?php if ($successMessage !== '') { ?>
            <div class="alert alert-success shadow-sm">
                <?php echo e($successMessage); ?>
            </div>
        <?php } ?>

        <?php if ($errorMessage !== '') { ?>
            <div class="alert alert-danger shadow-sm">
                <?php echo e($errorMessage); ?>
            </div>
        <?php } ?>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form class="row g-3" method="GET" action="/huongviet/admin/bookings/index.php">
                    <div class="col-md-3">
                        <label class="form-label">Ngày đặt</label>
                        <input type="date" name="booking_date" class="form-control" value="<?php echo e($date); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            <?php foreach ($validStatuses as $item) { ?>
                                <option value="<?php echo e($item); ?>" <?php echo $status === $item ? 'selected' : ''; ?>>
                                    <?php echo e($item); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e($phone); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tên khách</label>
                        <input type="text" name="customer_name" class="form-control" value="<?php echo e($customerName); ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-danger w-100">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Loại khách</th>
                            <th>Bàn</th>
                            <th>Số người</th>
                            <th>Ngày đặt</th>
                            <th>Giờ đặt</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $stt = 1; ?>
                        <?php if (mysqli_num_rows($bookings) > 0) { ?>
                            <?php while ($booking = mysqli_fetch_assoc($bookings)) { ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td><?php echo e($booking['customer_name']); ?></td>
                                    <td><?php echo e($booking['phone']); ?></td>
                                    <td><?php echo e($booking['customer_type']); ?></td>
                                    <td><?php echo e($booking['table_name']); ?></td>
                                    <td><?php echo (int) $booking['number_of_people']; ?></td>
                                    <td><?php echo e($booking['booking_date']); ?></td>
                                    <td><?php echo e(substr((string) $booking['booking_time'], 0, 5)); ?></td>
                                    <td>
                                        <span class="badge <?php echo bookingBadge($booking['status']); ?>">
                                            <?php echo e($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td style="max-width: 220px;"><?php echo e(mb_strimwidth((string) $booking['note'], 0, 90, '...')); ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a class="btn btn-outline-secondary" href="/huongviet/admin/bookings/view.php?id=<?php echo (int) $booking['id']; ?>">Xem</a>

                                            <?php if ($booking['status'] === 'Chờ xác nhận') { ?>
                                                <a class="btn btn-outline-primary" href="/huongviet/admin/bookings/update_status.php?id=<?php echo (int) $booking['id']; ?>&status=<?php echo urlencode('Đã xác nhận'); ?>">Xác nhận</a>
                                                <a 
                                                    class="btn btn-outline-danger" 
                                                    href="/huongviet/admin/bookings/delete.php?id=<?php echo (int) $booking['id']; ?>"
                                                    onclick="return confirm('Bạn có chắc muốn hủy lịch đặt bàn này không?');"
                                                >
                                                    Hủy
                                                </a>
                                            <?php } ?>

                                            <?php if ($booking['status'] === 'Đã xác nhận') { ?>
                                                <a class="btn btn-outline-success" href="/huongviet/admin/bookings/update_status.php?id=<?php echo (int) $booking['id']; ?>&status=<?php echo urlencode('Đã hoàn thành'); ?>">Hoàn thành</a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">Chưa có đặt bàn phù hợp.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
