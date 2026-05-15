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

$id = (int) ($_GET['id'] ?? 0);
$contact = null;

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "
        SELECT id, full_name, phone, email, address, subject, message, status, created_at
        FROM contact_messages
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $contact = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

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
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết liên hệ - Hương Việt</title>
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
            <h1 class="h3 mb-0">Chi tiết liên hệ</h1>
            <a class="btn btn-outline-secondary" href="/huongviet/admin/contacts/index.php">Quay lại danh sách</a>
        </div>

        <?php if (!$contact) { ?>
            <div class="alert alert-warning shadow-sm">Không tìm thấy liên hệ.</div>
        <?php } else { ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Họ tên</div>
                            <div class="fw-semibold"><?php echo e($contact['full_name']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Số điện thoại</div>
                            <div class="fw-semibold"><?php echo e($contact['phone']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold"><?php echo e($contact['email']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Địa chỉ</div>
                            <div class="fw-semibold"><?php echo e($contact['address']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Chủ đề</div>
                            <div class="fw-semibold"><?php echo e($contact['subject']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Ngày gửi</div>
                            <div class="fw-semibold"><?php echo e(date('d/m/Y H:i', strtotime($contact['created_at']))); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Trạng thái</div>
                            <span class="badge text-bg-<?php echo contactStatusBadge($contact['status']); ?>">
                                <?php echo e($contact['status']); ?>
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="text-muted small mb-2">Nội dung</div>
                        <div class="p-3 bg-light rounded border" style="white-space: pre-wrap;"><?php echo e($contact['message']); ?></div>
                    </div>

                    <?php if ($contact['status'] !== 'Đã xử lý') { ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($contact['status'] === 'Chưa xử lý') { ?>
                                <a class="btn btn-outline-info" href="/huongviet/admin/contacts/update_status.php?id=<?php echo (int) $contact['id']; ?>&status=<?php echo urlencode('Đang xử lý'); ?>&redirect=view">Đánh dấu Đang xử lý</a>
                            <?php } ?>

                            <?php if ($contact['status'] === 'Chưa xử lý' || $contact['status'] === 'Đang xử lý') { ?>
                                <a class="btn btn-outline-success" href="/huongviet/admin/contacts/update_status.php?id=<?php echo (int) $contact['id']; ?>&status=<?php echo urlencode('Đã xử lý'); ?>&redirect=view">Đánh dấu Đã xử lý</a>
                                <a class="btn btn-outline-danger" href="/huongviet/admin/contacts/delete.php?id=<?php echo (int) $contact['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa liên hệ này không?');">Xóa liên hệ</a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </main>
</body>
</html>
