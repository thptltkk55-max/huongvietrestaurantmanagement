<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
requireRoleGroup(['Quản lý', 'Nhân viên']);

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        email VARCHAR(255),
        address VARCHAR(255),
        subject VARCHAR(255),
        message TEXT,
        status VARCHAR(50) DEFAULT 'Chưa xử lý',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
$statusColumn = mysqli_query($conn, "SHOW COLUMNS FROM contact_messages LIKE 'status'");
if ($statusColumn && mysqli_num_rows($statusColumn) === 0) {
    mysqli_query($conn, "ALTER TABLE contact_messages ADD COLUMN status VARCHAR(50) DEFAULT 'Chưa xử lý'");
}

$validStatuses = ['Chưa xử lý', 'Đang xử lý', 'Đã xử lý'];
$keyword = trim($_GET['keyword'] ?? '');
$status = $_GET['status'] ?? '';
$status = in_array($status, $validStatuses, true) ? $status : '';
$successMessage = trim($_GET['success'] ?? '');
$errorMessage = trim($_GET['error'] ?? '');

if (!function_exists('contactStatusBadge')) {
    function contactStatusBadge($status)
    {
        return match ($status) {
            'Chưa xử lý' => 'warning',
            'Đang xử lý' => 'primary',
            'Đã xử lý' => 'success',
            default => 'secondary',
        };
    }
}

$stats = array_fill_keys($validStatuses, 0);
$totalContacts = 0;
$statsResult = mysqli_query($conn, "SELECT status, COUNT(*) AS total FROM contact_messages GROUP BY status");
if ($statsResult) {
    while ($row = mysqli_fetch_assoc($statsResult)) {
        if (isset($stats[$row['status']])) {
            $stats[$row['status']] = (int) $row['total'];
        }
        $totalContacts += (int) $row['total'];
    }
}

$sql = "
    SELECT id, full_name, phone, email, address, subject, message, status, created_at
    FROM contact_messages
    WHERE 1
";
$params = [];
$types = "";

if ($keyword !== '') {
    $likeKeyword = '%' . $keyword . '%';
    $sql .= " AND (full_name LIKE ? OR phone LIKE ? OR email LIKE ? OR subject LIKE ?)";
    $params = array_merge($params, [$likeKeyword, $likeKeyword, $likeKeyword, $likeKeyword]);
    $types .= "ssss";
}

if ($status !== '') {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý liên hệ - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/contacts.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/huongviet/admin/dashboard.php">Hương Việt Admin</a>
            <a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/logout.php">Đăng xuất</a>
        </div>
    </nav>

    <main class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h1 class="h3 mb-0">Quản lý liên hệ</h1>
            <a class="btn btn-outline-secondary" href="/huongviet/admin/dashboard.php">Quay lại dashboard</a>
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

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Tổng liên hệ</div>
                        <div class="h4 mb-0"><?php echo (int) $totalContacts; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Chưa xử lý</div>
                        <div class="h4 mb-0 text-warning"><?php echo $stats['Chưa xử lý']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Đang xử lý</div>
                        <div class="h4 mb-0 text-primary"><?php echo $stats['Đang xử lý']; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Đã xử lý</div>
                        <div class="h4 mb-0 text-success"><?php echo $stats['Đã xử lý']; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <form class="card card-body border-0 shadow-sm mb-4" method="get">
            <div class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label class="form-label">Tìm kiếm</label>
                    <input class="form-control" type="text" name="keyword" value="<?php echo e($keyword); ?>" placeholder="Họ tên, số điện thoại, email, chủ đề">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="">Tất cả</option>
                        <?php foreach ($validStatuses as $item) { ?>
                            <option value="<?php echo e($item); ?>" <?php echo $status === $item ? 'selected' : ''; ?>>
                                <?php echo e($item); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Lọc</button>
                </div>
            </div>
        </form>

        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Họ tên</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Chủ đề</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo e($row['full_name']); ?></td>
                                <td><?php echo e($row['phone']); ?></td>
                                <td><?php echo e($row['email']); ?></td>
                                <td><?php echo e($row['subject']); ?></td>
                                <td>
                                    <span class="badge text-bg-<?php echo contactStatusBadge($row['status']); ?>">
                                        <?php echo e($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo e(date('d/m/Y H:i', strtotime($row['created_at']))); ?></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a class="btn btn-outline-primary btn-sm" href="/huongviet/admin/contacts/view.php?id=<?php echo (int) $row['id']; ?>">Xem</a>

                                        <?php if ($row['status'] === 'Chưa xử lý') { ?>
                                            <a class="btn btn-outline-info btn-sm" href="/huongviet/admin/contacts/update_status.php?id=<?php echo (int) $row['id']; ?>&status=<?php echo urlencode('Đang xử lý'); ?>">Đang xử lý</a>
                                            <a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/contacts/delete.php?id=<?php echo (int) $row['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa liên hệ này không?');">Xóa</a>
                                        <?php } ?>

                                        <?php if ($row['status'] === 'Đang xử lý') { ?>
                                            <a class="btn btn-outline-success btn-sm" href="/huongviet/admin/contacts/update_status.php?id=<?php echo (int) $row['id']; ?>&status=<?php echo urlencode('Đã xử lý'); ?>">Đã xử lý</a>
                                            <a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/contacts/delete.php?id=<?php echo (int) $row['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa liên hệ này không?');">Xóa</a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if ($i === 1) { ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Không tìm thấy liên hệ phù hợp.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
