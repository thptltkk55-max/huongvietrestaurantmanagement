<?php
function inventoryRequireAccess()
{
    $user = currentUser();

    if (
        !$user ||
        (($user['role_group'] ?? '') !== 'Quản lý' && ($user['role_name'] ?? '') !== 'Nhân viên kho')
    ) {
        http_response_code(403);
        echo "Bạn không có quyền truy cập chức năng này.";
        exit;
    }
}

function inventoryMoney($amount)
{
    return number_format((float) $amount, 0, ',', '.') . " VNĐ";
}

function inventoryHeader($title)
{
    echo '<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>' . e($title) . ' - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=' . time() . '">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/inventory.css?v=' . time() . '">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/huongviet/admin/dashboard.php">Hương Việt Admin</a>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="/huongviet/admin/inventory/index.php">Kho</a>
            <a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/logout.php">Đăng xuất</a>
        </div>
    </div>
</nav>';
}

function inventoryFooter()
{
    echo '</body></html>';
}
?>
