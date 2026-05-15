<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
orderRequireAccess();
$id=(int)($_GET['id']??0); if($id<=0){header("Location: /huongviet/admin/orders/index.php");exit;}
$order=getOrder($conn,$id); if(!$order){header("Location: /huongviet/admin/orders/index.php");exit;}
$details=getOrderDetails($conn,$id);
$canManageOrder = $order['payment_status'] === 'Chưa thanh toán' && $order['order_status'] === 'Đang phục vụ';
$canPrintOrder = $order['payment_status'] === 'Đã thanh toán' && $order['order_status'] === 'Đã hoàn thành';
$canPrintOrder = $canPrintOrder || $canManageOrder;
$successMessage = trim($_GET['success'] ?? '');
$errorMessage = trim($_GET['error'] ?? '');
orderHeader("Chi tiết hóa đơn");
?>
<main class="container py-4">
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><h1 class="h3 mb-0">Hóa đơn #<?php echo $id; ?></h1><div><a class="btn btn-outline-secondary" href="/huongviet/admin/orders/index.php">Danh sách</a> <?php if($canManageOrder){?><a class="btn btn-outline-primary" href="/huongviet/admin/orders/edit.php?id=<?php echo $id;?>">Sửa</a> <a class="btn btn-success" href="/huongviet/admin/orders/pay.php?id=<?php echo $id;?>">Thanh toán</a> <a class="btn btn-outline-danger" href="/huongviet/admin/orders/cancel.php?id=<?php echo $id;?>" onclick="return confirm('Bạn có chắc muốn hủy order này không?');">Hủy</a><?php } ?> <?php if($canPrintOrder){?><a class="btn btn-dark" href="/huongviet/admin/orders/print.php?id=<?php echo $id;?>">In hóa đơn</a><?php } ?></div></div>
<?php if($successMessage!==''){?><div class="alert alert-success shadow-sm"><?php echo e($successMessage);?></div><?php }?>
<?php if($errorMessage!==''){?><div class="alert alert-danger shadow-sm"><?php echo e($errorMessage);?></div><?php }?>
<div class="card border-0 shadow-sm mb-3"><div class="card-body row g-3"><div class="col-md-4"><strong>Ngày tạo:</strong> <?php echo e($order['order_date']);?></div><div class="col-md-4"><strong>Khách:</strong> <?php echo e($order['customer_name']?:'Khách vãng lai');?></div><div class="col-md-4"><strong>Điện thoại:</strong> <?php echo e($order['phone']);?></div><div class="col-md-4"><strong>Bàn:</strong> <?php echo e($order['table_name']);?></div><div class="col-md-4"><strong>Nhân viên:</strong> <?php echo e($order['user_name']);?></div><div class="col-md-4"><strong>PTTT:</strong> <?php echo e($order['payment_method']);?></div><div class="col-md-4"><strong>Order:</strong> <span class="badge <?php echo badgeOrder($order['order_status']);?>"><?php echo e($order['order_status']);?></span></div><div class="col-md-4"><strong>Thanh toán:</strong> <span class="badge <?php echo badgePayment($order['payment_status']);?>"><?php echo e($order['payment_status']);?></span></div><div class="col-md-12"><strong>Ghi chú:</strong> <?php echo e($order['note']);?></div></div></div>
<div class="card border-0 shadow-sm mb-3"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>STT</th><th>Tên món</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th></tr></thead><tbody><?php $stt=1; while($d=mysqli_fetch_assoc($details)){?><tr><td><?php echo $stt++;?></td><td><?php echo e($d['food_name']);?></td><td><?php echo orderMoney($d['price']);?></td><td><?php echo (int)$d['quantity'];?></td><td><?php echo orderMoney($d['total_price']);?></td></tr><?php }?></tbody></table></div></div>
<div class="card border-0 shadow-sm"><div class="card-body ms-auto" style="max-width:420px"><div class="d-flex justify-content-between"><span>Tổng tiền</span><strong><?php echo orderMoney($order['total_amount']);?></strong></div><div class="d-flex justify-content-between"><span>Giảm giá</span><strong><?php echo orderMoney($order['discount']);?></strong></div><hr><div class="d-flex justify-content-between h5"><span>Thành tiền</span><strong><?php echo orderMoney($order['final_amount']);?></strong></div></div></div>
</main><?php orderFooter(); ?>
