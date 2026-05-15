<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();
requireRoleGroup('Quản lý');

$errors = [];
$form = [
    'full_name' => '',
    'username' => '',
    'email' => '',
    'phone' => '',
    'role_id' => '',
    'status' => 'Hoạt động',
];

$roles = mysqli_query($conn, "SELECT id, role_name, role_group FROM roles ORDER BY role_group ASC, id ASC");

if (!$roles) {
    die("Lỗi truy vấn vai trò: " . mysqli_error($conn));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['full_name'] = trim($_POST['full_name'] ?? '');
    $form['username'] = trim($_POST['username'] ?? '');
    $form['email'] = trim($_POST['email'] ?? '');
    $form['phone'] = trim($_POST['phone'] ?? '');
    $form['role_id'] = (int) ($_POST['role_id'] ?? 0);
    $form['status'] = $_POST['status'] ?? 'Hoạt động';
    $password = $_POST['password'] ?? '';

    if ($form['full_name'] === '') {
        $errors[] = "Vui lòng nhập họ tên.";
    }

    if ($form['username'] === '') {
        $errors[] = "Vui lòng nhập username.";
    }

    if ($form['email'] === '') {
        $errors[] = "Vui lòng nhập email.";
    }

    if ($password === '') {
        $errors[] = "Vui lòng nhập mật khẩu.";
    }

    if ($form['role_id'] <= 0) {
        $errors[] = "Vui lòng chọn vai trò.";
    }

    if (!in_array($form['status'], ['Hoạt động', 'Khóa'], true)) {
        $form['status'] = 'Hoạt động';
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO users (role_id, full_name, username, email, password, phone, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issssss",
            $form['role_id'],
            $form['full_name'],
            $form['username'],
            $form['email'],
            $passwordHash,
            $form['phone'],
            $form['status']
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: /huongviet/admin/users/index.php");
            exit;
        }

        $errors[] = "Không thể thêm người dùng. Username hoặc email có thể đã tồn tại.";
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm người dùng - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/users.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="h3 mb-0">Thêm người dùng</h1>
                    <a class="btn btn-outline-secondary" href="/huongviet/admin/users/index.php">Quay lại</a>
                </div>

                <?php if (!empty($errors)) { ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error) { ?>
                            <div><?php echo e($error); ?></div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo e($form['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo e($form['username']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e($form['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo e($form['phone']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vai trò</label>
                            <select name="role_id" class="form-select" required>
                                <option value="">-- Chọn vai trò --</option>
                                <?php while ($role = mysqli_fetch_assoc($roles)) { ?>
                                    <option value="<?php echo (int) $role['id']; ?>" <?php echo (int) $form['role_id'] === (int) $role['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($role['role_group'] . " - " . $role['role_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="Hoạt động" <?php echo $form['status'] === 'Hoạt động' ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="Khóa" <?php echo $form['status'] === 'Khóa' ? 'selected' : ''; ?>>Khóa</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Lưu người dùng</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
