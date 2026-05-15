<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";

requireLogin();
require_once __DIR__ . "/_bootstrap.php";

$id = (int) ($_GET['id'] ?? 0);
$task = null;
$errors = [];

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM service_assignments WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (!$task) {
    serviceHeader("Gán nhân viên phục vụ");
    echo '<main class="admin-container"><div class="admin-alert admin-alert-error">Không tìm thấy nhiệm vụ phục vụ.</div><a class="admin-btn admin-btn-outline" href="/huongviet/admin/service/index.php">Quay lại</a></main>';
    serviceFooter();
    exit;
}

if (in_array($task['service_status'], ['Hoàn thành', 'Đã hủy'], true)) {
    header("Location: /huongviet/admin/service/index.php?error=" . urlencode("Không thể gán nhân viên cho nhiệm vụ đã hoàn thành hoặc đã hủy."));
    exit;
}

$staffId = (int) ($_POST['staff_id'] ?? $task['staff_id'] ?? 0);
$note = trim($_POST['note'] ?? $task['note'] ?? '');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if ($staffId <= 0) {
        $errors[] = "Vui lòng chọn nhân viên phục vụ.";
    }

    if (!$errors) {
        $stmt = mysqli_prepare($conn, "UPDATE service_assignments SET staff_id = ?, note = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "isi", $staffId, $note, $id);
        mysqli_stmt_execute($stmt);
        header("Location: /huongviet/admin/service/view.php?id=" . $id);
        exit;
    }
}

$staffs = mysqli_query($conn, "
    SELECT u.id, u.full_name, u.username, r.role_name, r.role_group
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE u.status = 'Hoạt động'
    AND (r.role_name = 'Phục vụ' OR r.role_group = 'Nhân viên')
    ORDER BY u.full_name ASC
");

serviceHeader("Gán nhân viên phục vụ");
?>
<main class="admin-container">
    <div class="admin-page-title">
        <h1>Gán nhân viên phục vụ</h1>
        <p>Nhiệm vụ #<?php echo (int) $id; ?></p>
    </div>

    <?php if ($errors) { ?>
        <div class="admin-alert admin-alert-error">
            <?php foreach ($errors as $error) { ?><div><?php echo e($error); ?></div><?php } ?>
        </div>
    <?php } ?>

    <div class="admin-form-card">
        <form method="post" class="admin-form-grid">
            <div class="admin-form-group">
                <label>Nhân viên phục vụ</label>
                <select name="staff_id">
                    <option value="0">-- Chọn nhân viên --</option>
                    <?php while ($staff = mysqli_fetch_assoc($staffs)) { ?>
                        <option value="<?php echo (int) $staff['id']; ?>" <?php echo $staffId === (int) $staff['id'] ? 'selected' : ''; ?>>
                            <?php echo e(($staff['full_name'] ?: $staff['username']) . ' - ' . $staff['role_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="admin-form-group full-width">
                <label>Ghi chú</label>
                <textarea name="note" rows="4"><?php echo e($note); ?></textarea>
            </div>

            <div class="admin-actions full-width">
                <button class="admin-btn admin-btn-primary" type="submit">Lưu phân công</button>
                <a class="admin-btn admin-btn-outline" href="/huongviet/admin/service/view.php?id=<?php echo (int) $id; ?>">Quay lại</a>
            </div>
        </form>
    </div>
</main>
<?php serviceFooter(); ?>
