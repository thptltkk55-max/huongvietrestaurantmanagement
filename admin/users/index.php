<?php
require_once __DIR__ . "/../auth.php";
requireRoleGroup('Quản lý');

$sql = "
    SELECT
        u.id,
        u.full_name,
        u.username,
        u.email,
        u.phone,
        u.status,
        u.created_at,
        r.role_name,
        r.role_group
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    ORDER BY u.id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Lỗi truy vấn người dùng: " . mysqli_error($conn));
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý người dùng - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/users.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/huongviet/admin/dashboard.php">
                Hương Việt Admin
            </a>
            <a class="btn btn-outline-danger btn-sm" href="/huongviet/admin/logout.php">
                Đăng xuất
            </a>
        </div>
    </nav>

    <main class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h1 class="h3 mb-1">Quản lý người dùng</h1>
                <p class="text-muted mb-0">Danh sách tài khoản quản trị và nhân viên.</p>
            </div>
            <a class="btn btn-primary" href="/huongviet/admin/users/add.php">
                Thêm người dùng
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Vai trò</th>
                            <th>Nhóm</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) { ?>
                            <?php while ($user = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo (int) $user['id']; ?></td>
                                    <td><?php echo e($user['full_name']); ?></td>
                                    <td><?php echo e($user['username']); ?></td>
                                    <td><?php echo e($user['email']); ?></td>
                                    <td><?php echo e($user['phone']); ?></td>
                                    <td><?php echo e($user['role_name']); ?></td>
                                    <td><?php echo e($user['role_group']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['status'] === 'Hoạt động' ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                            <?php echo e($user['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="/huongviet/admin/users/edit.php?id=<?php echo (int) $user['id']; ?>">
                                            Sửa
                                        </a>
                                        <a 
                                            class="btn btn-sm btn-outline-danger" 
                                            href="/huongviet/admin/users/delete.php?id=<?php echo (int) $user['id']; ?>"
                                            onclick="return confirm('Bạn có chắc muốn khóa người dùng này?');"
                                        >
                                            Khóa
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Chưa có người dùng.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
