<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();
requireRoleGroup(['Quản lý']);

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: /huongviet/admin/foods/index.php");
    exit;
}

function loadFoodCategories(mysqli $conn)
{
    $result = mysqli_query($conn, "SELECT id, category_name FROM food_categories ORDER BY id ASC");

    if (!$result) {
        die("Lỗi truy vấn danh mục: " . mysqli_error($conn));
    }

    return $result;
}

function categoryExists(mysqli $conn, int $categoryId)
{
    $stmt = mysqli_prepare($conn, "SELECT id FROM food_categories WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $categoryId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result) !== null;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM foods WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$foodResult = mysqli_stmt_get_result($stmt);
$form = mysqli_fetch_assoc($foodResult);

if (!$form) {
    header("Location: /huongviet/admin/foods/index.php");
    exit;
}

$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $form['category_id'] = (int) ($_POST['category_id'] ?? 0);
    $form['food_name'] = trim($_POST['food_name'] ?? '');
    $form['price'] = trim($_POST['price'] ?? '');
    $form['image'] = trim($_POST['image'] ?? '');
    $form['description'] = trim($_POST['description'] ?? '');
    $form['status'] = $_POST['status'] ?? 'Còn bán';

    if ($form['food_name'] === '') {
        $errors[] = "Tên món không được rỗng.";
    }

    if (!is_numeric($form['price']) || (float) $form['price'] <= 0) {
        $errors[] = "Giá phải là số và lớn hơn 0.";
    }

    if ((int) $form['category_id'] <= 0 || !categoryExists($conn, (int) $form['category_id'])) {
        $errors[] = "Danh mục không hợp lệ.";
    }

    if (!in_array($form['status'], ['Còn bán', 'Ngừng bán'], true)) {
        $form['status'] = 'Còn bán';
    }

    if (empty($errors)) {
        $price = (float) $form['price'];
        $categoryId = (int) $form['category_id'];

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE foods
             SET category_id = ?, food_name = ?, price = ?, image = ?, description = ?, status = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isdsssi",
            $categoryId,
            $form['food_name'],
            $price,
            $form['image'],
            $form['description'],
            $form['status'],
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: /huongviet/admin/foods/index.php");
            exit;
        }

        $errors[] = "Không thể cập nhật món ăn: " . mysqli_error($conn);
    }
}

$categories = loadFoodCategories($conn);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa món ăn - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/foods.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <h1 class="h3 mb-0">Sửa món ăn</h1>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-secondary" href="/huongviet/admin/dashboard.php">Dashboard</a>
                        <a class="btn btn-outline-primary" href="/huongviet/admin/foods/index.php">Danh sách món</a>
                    </div>
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
                            <label class="form-label">Tên món</label>
                            <input type="text" name="food_name" class="form-control" value="<?php echo e($form['food_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Danh mục</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php while ($category = mysqli_fetch_assoc($categories)) { ?>
                                    <option value="<?php echo (int) $category['id']; ?>" <?php echo (int) $form['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($category['category_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giá</label>
                            <input type="number" min="0" step="1000" name="price" class="form-control" value="<?php echo e($form['price']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tên file ảnh</label>
                            <input type="text" name="image" class="form-control" value="<?php echo e($form['image']); ?>" placeholder="ga_nuong.jpg">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="Còn bán" <?php echo $form['status'] === 'Còn bán' ? 'selected' : ''; ?>>Còn bán</option>
                                <option value="Ngừng bán" <?php echo $form['status'] === 'Ngừng bán' ? 'selected' : ''; ?>>Ngừng bán</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="4"><?php echo e($form['description']); ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Cập nhật món ăn</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
