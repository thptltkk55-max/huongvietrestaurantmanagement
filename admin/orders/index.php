<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
orderRequireAccess();

$fromDate = trim($_GET['from_date'] ?? '');
$toDate = trim($_GET['to_date'] ?? '');
$paymentStatus = trim($_GET['payment_status'] ?? '');
$orderStatus = trim($_GET['order_status'] ?? '');
$paymentMethod = trim($_GET['payment_method'] ?? '');
$successMessage = trim($_GET['success'] ?? '');
$errorMessage = trim($_GET['error'] ?? '');
$validPayments = ['Chưa thanh toán', 'Đã thanh toán'];
$validOrders = ['Đang phục vụ', 'Đã hoàn thành', 'Đã hủy'];
$validMethods = ['Tiền mặt', 'Chuyển khoản', 'Thẻ ngân hàng'];
$where = [];
$params = [];
$types = '';
if ($fromDate !== '') { $where[] = 'DATE(o.order_date) >= ?'; $params[] = $fromDate; $types .= 's'; }
if ($toDate !== '') { $where[] = 'DATE(o.order_date) <= ?'; $params[] = $toDate; $types .= 's'; }
if (in_array($paymentStatus, $validPayments, true)) { $where[] = 'o.payment_status = ?'; $params[] = $paymentStatus; $types .= 's'; } else $paymentStatus = '';
if (in_array($orderStatus, $validOrders, true)) { $where[] = 'o.order_status = ?'; $params[] = $orderStatus; $types .= 's'; } else $orderStatus = '';
if (in_array($paymentMethod, $validMethods, true)) { $where[] = 'o.payment_method = ?'; $params[] = $paymentMethod; $types .= 's'; } else $paymentMethod = '';

$sql = "SELECT o.id, o.order_date, o.total_amount, o.discount, o.final_amount, o.payment_method, o.payment_status, o.order_status, c.customer_name, c.phone, t.table_name, u.full_name AS user_name
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        LEFT JOIN tables_restaurant t ON o.table_id = t.id
        LEFT JOIN users u ON o.user_id = u.id";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY o.id DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);

orderHeader("Quản lý hóa đơn");
?>
<main class="container py-4">
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><div><h1 class="h3 mb-1">Quản lý hóa đơn / Order</h1><p class="text-muted mb-0">Tạo order, thanh toán, hủy và in hóa đơn.</p></div><div><a class="btn btn-outline-secondary" href="/huongviet/admin/dashboard.php">Dashboard</a> <a class="btn btn-primary" href="/huongviet/admin/orders/add.php">Tạo order mới</a></div></div>
<?php if ($successMessage !== '') { ?><div class="alert alert-success shadow-sm"><?php echo e($successMessage); ?></div><?php } ?>
<?php if ($errorMessage !== '') { ?><div class="alert alert-danger shadow-sm"><?php echo e($errorMessage); ?></div><?php } ?>
<div class="card border-0 shadow-sm mb-3"><div class="card-body"><form class="row g-3"><div class="col-md-2"><label class="form-label">Từ ngày</label><input type="date" class="form-control" name="from_date" value="<?php echo e($fromDate); ?>"></div><div class="col-md-2"><label class="form-label">Đến ngày</label><input type="date" class="form-control" name="to_date" value="<?php echo e($toDate); ?>"></div><div class="col-md-2"><label class="form-label">Thanh toán</label><select class="form-select" name="payment_status"><option value="">Tất cả</option><?php foreach ($validPayments as $v) { ?><option <?php echo $paymentStatus===$v?'selected':''; ?> value="<?php echo e($v); ?>"><?php echo e($v); ?></option><?php } ?></select></div><div class="col-md-2"><label class="form-label">Order</label><select class="form-select" name="order_status"><option value="">Tất cả</option><?php foreach ($validOrders as $v) { ?><option <?php echo $orderStatus===$v?'selected':''; ?> value="<?php echo e($v); ?>"><?php echo e($v); ?></option><?php } ?></select></div><div class="col-md-2"><label class="form-label">Phương thức</label><select class="form-select" name="payment_method"><option value="">Tất cả</option><?php foreach ($validMethods as $v) { ?><option <?php echo $paymentMethod===$v?'selected':''; ?> value="<?php echo e($v); ?>"><?php echo e($v); ?></option><?php } ?></select></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-danger w-100">Lọc</button></div></form></div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Mã</th><th>Khách hàng</th><th>Bàn</th><th>Nhân viên</th><th>Ngày tạo</th><th>Tổng tiền</th><th>Giảm</th><th>Thành tiền</th><th>PTTT</th><th>TT thanh toán</th><th>TT order</th><th class="text-end">Hành động</th></tr></thead><tbody>
<?php while ($order = mysqli_fetch_assoc($orders)) { ?><tr><td>#<?php echo (int)$order['id']; ?></td><td><?php echo e($order['customer_name'] ?: 'Khách vãng lai'); ?><br><small class="text-muted"><?php echo e($order['phone']); ?></small></td><td><?php echo e($order['table_name']); ?></td><td><?php echo e($order['user_name']); ?></td><td><?php echo e($order['order_date']); ?></td><td><?php echo orderMoney($order['total_amount']); ?></td><td><?php echo orderMoney($order['discount']); ?></td><td><?php echo orderMoney($order['final_amount']); ?></td><td><?php echo e($order['payment_method']); ?></td><td><span class="badge <?php echo badgePayment($order['payment_status']); ?>"><?php echo e($order['payment_status']); ?></span></td><td><span class="badge <?php echo badgeOrder($order['order_status']); ?>"><?php echo e($order['order_status']); ?></span></td><td class="text-end"><div class="btn-group btn-group-sm"><a class="btn btn-outline-secondary" href="/huongviet/admin/orders/view.php?id=<?php echo (int)$order['id']; ?>">Xem</a><?php if ($order['payment_status'] === 'Chưa thanh toán' && $order['order_status'] === 'Đang phục vụ') { ?><a class="btn btn-outline-primary" href="/huongviet/admin/orders/edit.php?id=<?php echo (int)$order['id']; ?>">Sửa</a><a class="btn btn-outline-success" href="/huongviet/admin/orders/pay.php?id=<?php echo (int)$order['id']; ?>">Thanh toán</a><a class="btn btn-outline-danger" href="/huongviet/admin/orders/cancel.php?id=<?php echo (int)$order['id']; ?>" onclick="return confirm('Bạn có chắc muốn hủy order này không?');">Hủy</a><a class="btn btn-outline-dark" href="/huongviet/admin/orders/print.php?id=<?php echo (int)$order['id']; ?>">In</a><?php } elseif ($order['payment_status'] === 'Đã thanh toán' && $order['order_status'] === 'Đã hoàn thành') { ?><a class="btn btn-outline-dark" href="/huongviet/admin/orders/print.php?id=<?php echo (int)$order['id']; ?>">In</a><?php } ?></div></td></tr><?php } ?>
</tbody></table></div></div>
</main>
<?php orderFooter(); ?>
