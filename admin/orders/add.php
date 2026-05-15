<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
orderRequireAccess();

$errors = [];
$form = ['customer_id' => 0, 'table_id' => 0, 'note' => ''];

function orderOptions(mysqli $conn, string $sql) { $r = mysqli_query($conn, $sql); if (!$r) die(mysqli_error($conn)); return $r; }
function getFood(mysqli $conn, int $id) {
    $stmt = mysqli_prepare($conn, "SELECT id, price FROM foods WHERE id=? AND status='Còn bán' LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id); mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $form['customer_id'] = (int)($_POST['customer_id'] ?? 0);
    $form['table_id'] = (int)($_POST['table_id'] ?? 0);
    $form['note'] = trim($_POST['note'] ?? '');
    $foodIds = $_POST['food_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $details = [];
    $total = 0;
    for ($i=0; $i<count($foodIds); $i++) {
        $foodId = (int)($foodIds[$i] ?? 0);
        if ($foodId <= 0) continue;
        $qty = (int)($quantities[$i] ?? 0);
        if ($qty <= 0) { $errors[] = 'Số lượng món phải > 0.'; continue; }
        $food = getFood($conn, $foodId);
        if (!$food) { $errors[] = 'Món ăn không hợp lệ hoặc ngừng bán.'; continue; }
        $price = (float)$food['price'];
        $lineTotal = $price * $qty;
        $details[] = compact('foodId','qty','price','lineTotal');
        $total += $lineTotal;
    }
    if (!$details) $errors[] = 'Phải chọn ít nhất 1 món hợp lệ.';
    if (!$errors) {
        mysqli_begin_transaction($conn);
        try {
            $customerId = $form['customer_id'] > 0 ? $form['customer_id'] : null;
            $tableId = $form['table_id'] > 0 ? $form['table_id'] : null;
            $current = currentUser(); $userId = $current ? (int)$current['id'] : null;
            $discount = 0; $final = $total; $pm = 'Tiền mặt'; $ps = 'Chưa thanh toán'; $os = 'Đang phục vụ';
            $stmt = mysqli_prepare($conn, "INSERT INTO orders (customer_id, table_id, user_id, order_date, total_amount, discount, final_amount, payment_method, payment_status, order_status, note, created_at) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "iiidddssss", $customerId, $tableId, $userId, $total, $discount, $final, $pm, $ps, $os, $form['note']);
            mysqli_stmt_execute($stmt);
            $orderId = mysqli_insert_id($conn);
            foreach ($details as $d) {
                $stmt = mysqli_prepare($conn, "INSERT INTO order_details (order_id, food_id, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iiidd", $orderId, $d['foodId'], $d['qty'], $d['price'], $d['lineTotal']);
                mysqli_stmt_execute($stmt);
            }
            if ($tableId) {
                $status = 'Đang sử dụng';
                $stmt = mysqli_prepare($conn, "UPDATE tables_restaurant SET status=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "si", $status, $tableId); mysqli_stmt_execute($stmt);
            }
            mysqli_commit($conn);
            header("Location: /huongviet/admin/orders/view.php?id=" . $orderId); exit;
        } catch (Throwable $e) { mysqli_rollback($conn); $errors[] = 'Không thể tạo order: ' . $e->getMessage(); }
    }
}

$customers = orderOptions($conn, "SELECT id, customer_name, phone FROM customers WHERE status IS NULL OR status='Hoạt động' ORDER BY customer_name ASC");
$tables = orderOptions($conn, "SELECT id, table_name, status FROM tables_restaurant ORDER BY id ASC");
$foods = orderOptions($conn, "SELECT id, food_name, price FROM foods WHERE status='Còn bán' ORDER BY food_name ASC");
$foodRows=[]; while($f=mysqli_fetch_assoc($foods)) $foodRows[]=$f;
orderHeader("Tạo order");
?>
<main class="container py-4"><div class="card border-0 shadow-sm"><div class="card-body p-4">
<div class="d-flex justify-content-between mb-3"><h1 class="h3 mb-0">Tạo order mới</h1><a class="btn btn-outline-secondary" href="/huongviet/admin/orders/index.php">Danh sách</a></div>
<?php if ($errors) { ?><div class="alert alert-danger"><?php foreach($errors as $er) echo '<div>'.e($er).'</div>'; ?></div><?php } ?>
<form method="POST">
<div class="row g-3 mb-4"><div class="col-md-4"><label class="form-label">Khách hàng</label><select class="form-select" name="customer_id"><option value="0">Khách vãng lai / không chọn</option><?php while($c=mysqli_fetch_assoc($customers)){ ?><option value="<?php echo (int)$c['id']; ?>"><?php echo e($c['customer_name'].' - '.$c['phone']); ?></option><?php } ?></select></div><div class="col-md-4"><label class="form-label">Bàn</label><select class="form-select" name="table_id"><option value="0">-- Không chọn --</option><?php while($t=mysqli_fetch_assoc($tables)){ ?><option value="<?php echo (int)$t['id']; ?>"><?php echo e($t['table_name'].' - '.$t['status']); ?></option><?php } ?></select></div><div class="col-md-4"><label class="form-label">Ghi chú</label><input class="form-control" name="note" value="<?php echo e($form['note']); ?>"></div></div>
<h2 class="h5">Danh sách món</h2><div class="table-responsive"><table class="table"><thead class="table-light"><tr><th>Món ăn</th><th style="width:180px">Số lượng</th></tr></thead><tbody><?php for($i=0;$i<8;$i++){ ?><tr><td><select class="form-select" name="food_id[]"><option value="0">-- Chọn món --</option><?php foreach($foodRows as $f){ ?><option value="<?php echo (int)$f['id']; ?>"><?php echo e($f['food_name'].' - '.orderMoney($f['price'])); ?></option><?php } ?></select></td><td><input type="number" min="1" class="form-control" name="quantity[]"></td></tr><?php } ?></tbody></table></div>
<button class="btn btn-primary">Tạo order</button>
</form></div></div></main>
<?php orderFooter(); ?>
