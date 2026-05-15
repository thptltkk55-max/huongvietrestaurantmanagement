<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();
requireRoleGroup(['Quản lý']);

$customerTypes = ['Khách vãng lai', 'Khách quen', 'Khách VIP'];
$statuses = ['Hoạt động', 'Ngừng theo dõi'];
$errors = [];
$form = [
    'customer_name' => '',
    'phone' => '',
    'email' => '',
    'address' => '',
    'customer_type' => 'Khách vãng lai',
    'status' => 'Hoạt động',
];

function customerPhoneExists(mysqli $conn, string $phone, int $ignoreId = 0)
{
    $sql = "SELECT id FROM customers WHERE phone = ?";

    if ($ignoreId > 0) {
        $sql .= " AND id <> ?";
    }

    $sql .= " LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);

    if ($ignoreId > 0) {
        mysqli_stmt_bind_param($stmt, "si", $phone, $ignoreId);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $phone);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result) !== null;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $form['customer_name'] = trim($_POST['customer_name'] ?? '');
    $form['phone'] = trim($_POST['phone'] ?? '');
    $form['email'] = trim($_POST['email'] ?? '');
    $form['address'] = trim($_POST['address'] ?? '');
    $form['customer_type'] = $_POST['customer_type'] ?? 'Khách vãng lai';
    $form['status'] = $_POST['status'] ?? 'Hoạt động';

    if ($form['customer_name'] === '') {
        $errors[] = "Tên khách không được rỗng.";
    }

    if ($form['phone'] === '') {
        $errors[] = "Số điện thoại không được rỗng.";
    } elseif (customerPhoneExists($conn, $form['phone'])) {
        $errors[] = "Số điện thoại này đã tồn tại.";
    }

    if (!in_array($form['customer_type'], $customerTypes, true)) {
        $form['customer_type'] = 'Khách vãng lai';
    }

    if (!in_array($form['status'], $statuses, true)) {
        $form['status'] = 'Hoạt động';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO customers (customer_name, phone, email, address, customer_type, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "ssssss",
            $form['customer_name'],
            $form['phone'],
            $form['email'],
            $form['address'],
            $form['customer_type'],
            $form['status']
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: /huongviet/admin/customers/index.php");
            exit;
        }

        $errors[] = "Không thể thêm khách hàng: " . mysqli_error($conn);
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm khách hàng - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/customers.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <h1 class="h3 mb-0">Thêm khách hàng</h1>
                    <a class="btn btn-outline-secondary" href="/huongviet/admin/customers/index.php">Quay lại</a>
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
                            <label class="form-label">Tên khách hàng</label>
                            <input type="text" name="customer_name" class="form-control" value="<?php echo e($form['customer_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo e($form['phone']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e($form['email']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" value="<?php echo e($form['address']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại khách</label>
                            <select name="customer_type" class="form-select">
                                <?php foreach ($customerTypes as $type) { ?>
                                    <option value="<?php echo e($type); ?>" <?php echo $form['customer_type'] === $type ? 'selected' : ''; ?>><?php echo e($type); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <?php foreach ($statuses as $item) { ?>
                                    <option value="<?php echo e($item); ?>" <?php echo $form['status'] === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Lưu khách hàng</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
