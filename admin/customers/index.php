<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();

$customerTypes = ['Khách vãng lai', 'Khách quen', 'Khách VIP'];
$statuses = ['Hoạt động', 'Ngừng theo dõi'];
$keyword = trim($_GET['keyword'] ?? '');
$customerType = trim($_GET['customer_type'] ?? '');
$status = trim($_GET['status'] ?? '');

if (!in_array($customerType, $customerTypes, true)) {
    $customerType = '';
}

if (!in_array($status, $statuses, true)) {
    $status = '';
}

$stats = [
    'total' => 0,
    'vip' => 0,
    'regular' => 0,
    'walkin' => 0,
];

$statsResult = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS total,
        SUM(customer_type = 'Khách VIP') AS vip,
        SUM(customer_type = 'Khách quen') AS regular,
        SUM(customer_type = 'Khách vãng lai') AS walkin
     FROM customers"
);

if ($statsResult) {
    $statsRow = mysqli_fetch_assoc($statsResult);
    $stats = [
        'total' => (int) ($statsRow['total'] ?? 0),
        'vip' => (int) ($statsRow['vip'] ?? 0),
        'regular' => (int) ($statsRow['regular'] ?? 0),
        'walkin' => (int) ($statsRow['walkin'] ?? 0),
    ];
}

$where = [];
$params = [];
$types = '';

if ($keyword !== '') {
    $where[] = "(c.customer_name LIKE ? OR c.phone LIKE ? OR c.email LIKE ?)";
    $like = '%' . $keyword . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

if ($customerType !== '') {
    $where[] = "c.customer_type = ?";
    $params[] = $customerType;
    $types .= 's';
}

if ($status !== '') {
    $where[] = "c.status = ?";
    $params[] = $status;
    $types .= 's';
}

$sql = "
    SELECT
        c.id,
        c.customer_name,
        c.phone,
        c.email,
        c.address,
        c.customer_type,
        c.status,
        c.created_at,
        COUNT(b.id) AS total_bookings
    FROM customers c
    LEFT JOIN bookings b ON c.id = b.customer_id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= "
    GROUP BY
        c.id,
        c.customer_name,
        c.phone,
        c.email,
        c.address,
        c.customer_type,
        c.status,
        c.created_at
    ORDER BY c.id DESC
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Lỗi chuẩn bị truy vấn khách hàng: " . mysqli_error($conn));
}

if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$customers = mysqli_stmt_get_result($stmt);

function customerTypeBadge($type)
{
    return match ($type) {
        'Khách VIP' => 'text-bg-warning',
        'Khách quen' => 'text-bg-success',
        default => 'text-bg-secondary',
    };
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý khách hàng - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/customers.css?v=<?php echo time(); ?>">
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
                <h1 class="h3 mb-1">Quản lý khách hàng</h1>
                <p class="text-muted mb-0">Theo dõi thông tin và lịch sử đặt bàn của khách.</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="/huongviet/admin/dashboard.php">Dashboard</a>
                <a class="btn btn-primary" href="/huongviet/admin/customers/add.php">Thêm khách hàng</a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Tổng khách</div><div class="h4 mb-0"><?php echo $stats['total']; ?></div></div></div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Khách VIP</div><div class="h4 mb-0"><?php echo $stats['vip']; ?></div></div></div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Khách quen</div><div class="h4 mb-0"><?php echo $stats['regular']; ?></div></div></div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Khách vãng lai</div><div class="h4 mb-0"><?php echo $stats['walkin']; ?></div></div></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form class="row g-3" method="GET" action="/huongviet/admin/customers/index.php">
                    <div class="col-md-4">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="keyword" class="form-control" value="<?php echo e($keyword); ?>" placeholder="Tên, số điện thoại, email...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Loại khách</label>
                        <select name="customer_type" class="form-select">
                            <option value="">Tất cả</option>
                            <?php foreach ($customerTypes as $type) { ?>
                                <option value="<?php echo e($type); ?>" <?php echo $customerType === $type ? 'selected' : ''; ?>><?php echo e($type); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            <?php foreach ($statuses as $item) { ?>
                                <option value="<?php echo e($item); ?>" <?php echo $status === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option>
                            <?php } ?>
                        </select>
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
                            <th>Tên khách</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Địa chỉ</th>
                            <th>Loại khách</th>
                            <th>Số lần đặt</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $stt = 1; ?>
                        <?php if (mysqli_num_rows($customers) > 0) { ?>
                            <?php while ($customer = mysqli_fetch_assoc($customers)) { ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td class="fw-semibold"><?php echo e($customer['customer_name']); ?></td>
                                    <td><?php echo e($customer['phone']); ?></td>
                                    <td><?php echo e($customer['email']); ?></td>
                                    <td style="max-width: 240px;"><?php echo e($customer['address']); ?></td>
                                    <td><span class="badge <?php echo customerTypeBadge($customer['customer_type']); ?>"><?php echo e($customer['customer_type']); ?></span></td>
                                    <td><?php echo (int) $customer['total_bookings']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $customer['status'] === 'Hoạt động' ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                            <?php echo e($customer['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo e($customer['created_at']); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-secondary" href="/huongviet/admin/customers/view.php?id=<?php echo (int) $customer['id']; ?>">Xem</a>
                                        <a class="btn btn-sm btn-outline-primary" href="/huongviet/admin/customers/edit.php?id=<?php echo (int) $customer['id']; ?>">Sửa</a>
                                        <a 
                                            class="btn btn-sm btn-outline-danger" 
                                            href="/huongviet/admin/customers/delete.php?id=<?php echo (int) $customer['id']; ?>"
                                            onclick="return confirm('Bạn có chắc muốn ngừng theo dõi khách hàng này không?');"
                                        >
                                            Ngừng theo dõi
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="10" class="text-center text-muted py-4">Không tìm thấy khách hàng phù hợp.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
