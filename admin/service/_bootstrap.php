<?php
requireRoleGroup(['Quản lý', 'Nhân viên']);

function serviceEnsureSchema(mysqli $conn)
{
    mysqli_query($conn, "
        CREATE TABLE IF NOT EXISTS service_assignments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            table_id INT NULL,
            booking_id INT NULL,
            order_id INT NULL,
            staff_id INT NULL,
            service_status VARCHAR(50) DEFAULT 'Chờ phục vụ',
            note TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            completed_at DATETIME NULL,
            CONSTRAINT fk_service_table FOREIGN KEY (table_id) REFERENCES tables_restaurant(id) ON DELETE SET NULL,
            CONSTRAINT fk_service_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
            CONSTRAINT fk_service_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
            CONSTRAINT fk_service_staff FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $statusColumn = mysqli_query($conn, "SHOW COLUMNS FROM tables_restaurant LIKE 'status'");
    if ($statusColumn && mysqli_num_rows($statusColumn) === 0) {
        mysqli_query($conn, "ALTER TABLE tables_restaurant ADD COLUMN status VARCHAR(50) DEFAULT 'Trống'");
    }
}

serviceEnsureSchema($conn);

function serviceHeader($title)
{
    echo '<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>' . e($title) . ' - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=' . time() . '">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/service.css?v=' . time() . '">
</head>
<body>
<div class="admin-topbar">
    <div class="admin-topbar-inner">
        <a href="/huongviet/admin/dashboard.php" class="admin-brand">Hương Việt Admin</a>
        <div class="admin-nav">
            <a href="/huongviet/admin/dashboard.php" class="admin-btn admin-btn-outline">Dashboard</a>
            <a href="/huongviet/admin/service/index.php" class="admin-btn admin-btn-outline">Phục vụ</a>
            <a href="/huongviet/admin/logout.php" class="admin-btn admin-btn-danger">Đăng xuất</a>
        </div>
    </div>
</div>';
}

function serviceFooter()
{
    echo '</body></html>';
}

function serviceStatusBadge($status)
{
    return match ($status) {
        'Chờ phục vụ' => 'admin-badge-warning',
        'Đang phục vụ' => 'admin-badge-primary',
        'Hoàn thành' => 'admin-badge-success',
        'Đã hủy' => 'admin-badge-danger',
        default => 'admin-badge-secondary',
    };
}

function tableStatusBadge($status)
{
    return match ($status) {
        'Trống' => 'admin-badge-success',
        'Đã đặt' => 'admin-badge-warning',
        'Đang sử dụng' => 'admin-badge-primary',
        'Bảo trì' => 'admin-badge-danger',
        default => 'admin-badge-secondary',
    };
}

function serviceValidStatuses()
{
    return ['Chờ phục vụ', 'Đang phục vụ', 'Hoàn thành', 'Đã hủy'];
}

function tableValidStatuses()
{
    return ['Trống', 'Đã đặt', 'Đang sử dụng', 'Bảo trì'];
}

function serviceCanTransition($current, $next)
{
    if ($current === 'Chờ phục vụ') {
        return in_array($next, ['Đang phục vụ', 'Đã hủy'], true);
    }

    if ($current === 'Đang phục vụ') {
        return in_array($next, ['Hoàn thành', 'Đã hủy'], true);
    }

    return false;
}

function serviceShouldFreeTable(mysqli $conn, ?int $tableId, ?int $orderId)
{
    if (!$tableId) {
        return false;
    }

    if ($orderId) {
        $stmt = mysqli_prepare($conn, "SELECT order_status, payment_status FROM orders WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($order && $order['order_status'] === 'Đang phục vụ' && $order['payment_status'] === 'Chưa thanh toán') {
            return false;
        }
    }

    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM orders WHERE table_id = ? AND order_status = 'Đang phục vụ' AND payment_status = 'Chưa thanh toán'");
    mysqli_stmt_bind_param($stmt, "i", $tableId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    return ((int) ($row['total'] ?? 0)) === 0;
}

function serviceUpdateTableStatus(mysqli $conn, int $tableId, string $status)
{
    $stmt = mysqli_prepare($conn, "UPDATE tables_restaurant SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $tableId);
    mysqli_stmt_execute($stmt);
}
?>
