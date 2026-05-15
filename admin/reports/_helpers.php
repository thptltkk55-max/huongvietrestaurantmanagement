<?php
function reportRequireManager()
{
    $user = currentUser();
    if (!$user || ($user['role_group'] ?? '') !== 'Quản lý') {
        http_response_code(403);
        echo "Bạn không có quyền truy cập chức năng này.";
        exit;
    }
}

function reportMoney($amount)
{
    return number_format((float) $amount, 0, ',', '.') . " VNĐ";
}

function reportDate($date)
{
    if (!$date) {
        return '';
    }
    return date('d/m/Y', strtotime($date));
}

function validReportDate($date, $fallback)
{
    $dt = DateTime::createFromFormat('Y-m-d', (string) $date);
    return ($dt && $dt->format('Y-m-d') === $date) ? $date : $fallback;
}

function reportHeader($title)
{
    echo '<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($title) . ' - Hương Việt</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=' . time() . '">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/reports.css?v=' . time() . '"></head><body class="bg-light"><nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm"><div class="container"><a class="navbar-brand fw-bold" href="/huongviet/admin/dashboard.php">Hương Việt Admin</a><div class="d-flex gap-2"><a class="btn btn-outline-secondary btn-sm" href="/huongviet/admin/reports/index.php">Báo cáo</a><a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/logout.php">Đăng xuất</a></div></div></nav>';
}

function reportFooter()
{
    echo '</body></html>';
}

function tableExists(mysqli $conn, string $table)
{
    $safe = mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$safe'");
    return $result && mysqli_num_rows($result) > 0;
}
?>
