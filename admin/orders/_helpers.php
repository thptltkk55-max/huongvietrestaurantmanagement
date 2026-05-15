<?php
function orderRequireAccess()
{
    $user = currentUser();
    $allowedRoles = ['Thu ngân', 'Phục vụ'];

    if (
        !$user ||
        (($user['role_group'] ?? '') !== 'Quản lý' && !in_array(($user['role_name'] ?? ''), $allowedRoles, true))
    ) {
        http_response_code(403);
        echo "Bạn không có quyền truy cập chức năng này.";
        exit;
    }
}

function orderMoney($amount)
{
    return number_format((float) $amount, 0, ',', '.') . " VNĐ";
}

function orderHeader($title)
{
    echo '<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($title) . ' - Hương Việt</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=' . time() . '">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/orders.css?v=' . time() . '"></head><body class="bg-light"><nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm"><div class="container"><a class="navbar-brand fw-bold" href="/huongviet/admin/dashboard.php">Hương Việt Admin</a><div class="d-flex gap-2"><a class="btn btn-outline-secondary btn-sm" href="/huongviet/admin/orders/index.php">Orders</a><a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/logout.php">Đăng xuất</a></div></div></nav>';
}

function orderFooter()
{
    echo '</body></html>';
}

function getOrder(mysqli $conn, int $id)
{
    $sql = "SELECT o.*, c.customer_name, c.phone, t.table_name, u.full_name AS user_name
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.id
            LEFT JOIN tables_restaurant t ON o.table_id = t.id
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function getOrderDetails(mysqli $conn, int $orderId)
{
    $stmt = mysqli_prepare($conn, "SELECT d.*, f.food_name FROM order_details d LEFT JOIN foods f ON d.food_id = f.id WHERE d.order_id = ? ORDER BY d.id ASC");
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function badgePayment($status)
{
    return $status === 'Đã thanh toán' ? 'text-bg-success' : 'text-bg-warning';
}

function badgeOrder($status)
{
    return match ($status) {
        'Đã hoàn thành' => 'text-bg-success',
        'Đã hủy' => 'text-bg-danger',
        default => 'text-bg-primary',
    };
}
?>
