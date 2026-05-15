<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_bootstrap.php";

$id = (int) ($_GET['id'] ?? 0);
$table = null;
$errors = [];
$validStatuses = tableValidStatuses();

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT id, table_name, capacity, status FROM tables_restaurant WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $table = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (!$table) {
    serviceHeader("Cập nhật trạng thái bàn");
    echo '<main class="admin-container"><div class="admin-alert admin-alert-error">Không tìm thấy bàn.</div><a class="admin-btn admin-btn-outline" href="/huongviet/admin/service/index.php">Quay lại</a></main>';
    serviceFooter();
    exit;
}

$status = $_POST['status'] ?? $table['status'];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!in_array($status, $validStatuses, true)) {
        $errors[] = "Trạng thái bàn không hợp lệ.";
    }

    if (!$errors) {
        $stmt = mysqli_prepare($conn, "UPDATE tables_restaurant SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        mysqli_stmt_execute($stmt);
        header("Location: /huongviet/admin/service/index.php?success=" . urlencode("Cập nhật trạng thái bàn thành công."));
        exit;
    }
}

serviceHeader("Cập nhật trạng thái bàn");
?>
<main class="admin-container">
    <div class="admin-page-title">
        <h1>Cập nhật trạng thái bàn</h1>
        <p><?php echo e($table['table_name']); ?> - Sức chứa <?php echo (int) $table['capacity']; ?></p>
    </div>

    <?php if ($errors) { ?>
        <div class="admin-alert admin-alert-error">
            <?php foreach ($errors as $error) { ?><div><?php echo e($error); ?></div><?php } ?>
        </div>
    <?php } ?>

    <div class="admin-form-card">
        <form method="post" class="admin-form-grid">
            <div class="admin-form-group">
                <label>Tên bàn</label>
                <input value="<?php echo e($table['table_name']); ?>" disabled>
            </div>

            <div class="admin-form-group">
                <label>Trạng thái mới</label>
                <select name="status">
                    <?php foreach ($validStatuses as $item) { ?>
                        <option value="<?php echo e($item); ?>" <?php echo $status === $item ? 'selected' : ''; ?>><?php echo e($item); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="admin-actions full-width">
                <button class="admin-btn admin-btn-primary" type="submit">Cập nhật</button>
                <a class="admin-btn admin-btn-outline" href="/huongviet/admin/service/index.php">Quay lại</a>
            </div>
        </form>
    </div>
</main>
<?php serviceFooter(); ?>
