<?php
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../auth.php";
requireLogin();
requireRoleGroup(['Quản lý']);

$keyword = trim($_GET['keyword'] ?? '');
$categoryId = (int) ($_GET['category_id'] ?? 0);
$status = trim($_GET['status'] ?? '');

$categories = mysqli_query($conn, "SELECT id, category_name FROM food_categories ORDER BY id ASC");

if (!$categories) {
    die("Lỗi truy vấn danh mục: " . mysqli_error($conn));
}

$where = [];
$params = [];
$types = "";

if ($keyword !== "") {
    $where[] = "f.food_name LIKE ?";
    $params[] = "%" . $keyword . "%";
    $types .= "s";
}

if ($categoryId > 0) {
    $where[] = "f.category_id = ?";
    $params[] = $categoryId;
    $types .= "i";
}

if (in_array($status, ['Còn bán', 'Ngừng bán'], true)) {
    $where[] = "f.status = ?";
    $params[] = $status;
    $types .= "s";
} else {
    $status = "";
}

$sql = "
    SELECT 
        f.id,
        f.food_name,
        f.price,
        f.image,
        f.description,
        f.status,
        f.category_id,
        c.category_name
    FROM foods f
    LEFT JOIN food_categories c ON f.category_id = c.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY f.id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Lỗi chuẩn bị truy vấn món ăn: " . mysqli_error($conn));
}

if ($types !== "") {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$foods = mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý món ăn - Hương Việt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/foods.css?v=<?php echo time(); ?>">
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
                <h1 class="h3 mb-1">Quản lý món ăn</h1>
                <p class="text-muted mb-0">Xem, tìm kiếm, thêm, sửa và ngừng bán món ăn.</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="/huongviet/admin/dashboard.php">Dashboard</a>
                <a class="btn btn-primary" href="/huongviet/admin/foods/add.php">Thêm món</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form class="row g-3" method="GET" action="/huongviet/admin/foods/index.php">
                    <div class="col-md-4">
                        <label class="form-label">Tìm theo tên món</label>
                        <input type="text" name="keyword" class="form-control" value="<?php echo e($keyword); ?>" placeholder="Nhập tên món...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-select">
                            <option value="0">Tất cả danh mục</option>
                            <?php while ($category = mysqli_fetch_assoc($categories)) { ?>
                                <option value="<?php echo (int) $category['id']; ?>" <?php echo $categoryId === (int) $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($category['category_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="Còn bán" <?php echo $status === 'Còn bán' ? 'selected' : ''; ?>>Còn bán</option>
                            <option value="Ngừng bán" <?php echo $status === 'Ngừng bán' ? 'selected' : ''; ?>>Ngừng bán</option>
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
                            <th>Ảnh</th>
                            <th>Tên món</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $stt = 1; ?>
                        <?php if (mysqli_num_rows($foods) > 0) { ?>
                            <?php while ($food = mysqli_fetch_assoc($foods)) { ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td style="width: 110px;">
                                        <?php if (!empty($food['image'])) { ?>
                                            <img 
                                                src="/huongviet/assets/images/<?php echo e($food['image']); ?>" 
                                                alt="<?php echo e($food['food_name']); ?>"
                                                class="img-thumbnail"
                                                style="width: 86px; height: 64px; object-fit: cover;"
                                            >
                                        <?php } else { ?>
                                            <span class="text-muted small">Chưa có ảnh</span>
                                        <?php } ?>
                                    </td>
                                    <td class="fw-semibold"><?php echo e($food['food_name']); ?></td>
                                    <td><?php echo e($food['category_name'] ?: 'Chưa phân loại'); ?></td>
                                    <td><?php echo number_format((float) $food['price'], 0, ',', '.'); ?>đ</td>
                                    <td style="max-width: 280px;">
                                        <?php echo e(mb_strimwidth((string) $food['description'], 0, 120, '...')); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $food['status'] === 'Còn bán' ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                            <?php echo e($food['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="/huongviet/admin/foods/edit.php?id=<?php echo (int) $food['id']; ?>">
                                            Sửa
                                        </a>
                                        <a 
                                            class="btn btn-sm btn-outline-danger" 
                                            href="/huongviet/admin/foods/delete.php?id=<?php echo (int) $food['id']; ?>"
                                            onclick="return confirm('Bạn có chắc muốn ngừng bán món này không?');"
                                        >
                                            Ngừng bán
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Không tìm thấy món ăn phù hợp.
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
