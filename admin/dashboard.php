<?php
require_once __DIR__ . "/auth.php";
requireLogin();

$user = currentUser();
$isManager = ($user['role_group'] ?? '') === 'Quản lý';
$pendingContacts = 0;
$contactTableResult = mysqli_query($conn, "SHOW TABLES LIKE 'contact_messages'");

if ($contactTableResult && mysqli_num_rows($contactTableResult) > 0) {
    $pendingContactResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM contact_messages WHERE status = 'Chưa xử lý'");
    if ($pendingContactResult) {
        $pendingContactRow = mysqli_fetch_assoc($pendingContactResult);
        $pendingContacts = (int) ($pendingContactRow['total'] ?? 0);
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard quản trị - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/dashboard.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/huongviet/admin/dashboard.php">
                Hương Việt Admin
            </a>

            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">
                    <?php echo e($user['full_name']); ?>
                </span>
                <a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/logout.php">
                    Đăng xuất
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
            <h1 class="h3 mb-2">Xin chào, <?php echo e($user['full_name']); ?></h1>
            <p class="mb-0 text-muted">
                Vai trò: <strong><?php echo e($user['role_name']); ?></strong>
                | Nhóm quyền: <strong><?php echo e($user['role_group']); ?></strong>
            </p>
        </div>

        <div class="row g-4">
            <?php
            $cards = [
                ['title' => 'Quản lý món ăn', 'text' => 'Thêm, sửa, ẩn hiện món ăn.', 'href' => '/huongviet/admin/foods/index.php', 'manager' => true],
                ['title' => 'Quản lý đặt bàn', 'text' => 'Theo dõi và xử lý lịch đặt bàn.', 'href' => '/huongviet/admin/bookings/index.php'],
                ['title' => 'Quản lý hóa đơn / Order', 'text' => 'Tạo, thanh toán và in hóa đơn.', 'href' => '/huongviet/admin/orders/index.php'],
                ['title' => 'Tạo order mới', 'text' => 'Chọn bàn, chọn món và lập hóa đơn.', 'href' => '/huongviet/admin/orders/add.php'],
                ['title' => 'Quản lý phục vụ', 'text' => 'Theo dõi bàn, phân công nhân viên phục vụ và cập nhật trạng thái phục vụ.', 'href' => '/huongviet/admin/service/index.php'],
                ['title' => 'Quản lý khách hàng', 'text' => 'Theo dõi hồ sơ và lịch sử đặt bàn.', 'href' => '/huongviet/admin/customers/index.php'],
                ['title' => 'Quản lý liên hệ', 'text' => 'Xem và xử lý tin nhắn khách gửi. Chưa xử lý: ' . $pendingContacts, 'href' => '/huongviet/admin/contacts/index.php'],
                ['title' => 'Quản lý người dùng', 'text' => 'Tạo tài khoản và phân quyền.', 'href' => '/huongviet/admin/users/index.php', 'manager' => true],
                ['title' => 'Quản lý kho', 'text' => 'Theo dõi nguyên liệu và tồn kho.', 'href' => '/huongviet/admin/inventory/index.php'],
                ['title' => 'Nhập kho', 'text' => 'Tạo phiếu nhập nguyên liệu.', 'href' => '/huongviet/admin/inventory/add_import.php'],
                ['title' => 'Báo cáo thống kê', 'text' => 'Xem tổng quan vận hành nhà hàng.', 'href' => '/huongviet/admin/reports/index.php', 'manager' => true],
                ['title' => 'Báo cáo doanh thu', 'text' => 'Thống kê doanh thu theo khoảng ngày.', 'href' => '/huongviet/admin/reports/revenue.php', 'manager' => true],
                ['title' => 'Món bán chạy', 'text' => 'Xem các món bán chạy nhất.', 'href' => '/huongviet/admin/reports/best_selling.php', 'manager' => true],
            ];
            ?>

            <?php foreach ($cards as $card) { ?>
                <?php $disabled = !empty($card['manager']) && !$isManager; ?>

                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5"><?php echo e($card['title']); ?></h2>
                            <p class="text-muted"><?php echo e($card['text']); ?></p>

                            <?php if ($disabled) { ?>
                                <button class="btn btn-secondary btn-sm" disabled>
                                    Không có quyền
                                </button>
                            <?php } else { ?>
                                <a class="btn btn-primary btn-sm" href="<?php echo e($card['href']); ?>">
                                    Truy cập
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>
</body>
</html>
