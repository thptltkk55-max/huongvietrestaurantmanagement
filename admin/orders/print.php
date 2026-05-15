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
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>In hóa đơn #<?php echo $id; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/base.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/huongviet/assets/css/admin/orders.css?v=<?php echo time(); ?>">
<style>
    body { background:#f5f5f5; }
    .invoice { width: 80mm; max-width: 100%; margin: 24px auto; background: #fff; padding: 18px; box-shadow: 0 8px 24px rgba(0,0,0,.12); }
    .invoice h1 { font-size: 18px; font-weight: 800; text-align:center; }
    .invoice .small { font-size: 12px; }
    @media print {
        body { background:#fff; }
        .no-print { display:none !important; }
        .invoice { width: 80mm; margin:0; box-shadow:none; }
    }
</style>
</head>
<body>
<div class="no-print text-center mt-3">
    <button class="btn btn-primary" onclick="window.print()">In hóa đơn</button>
    <a class="btn btn-outline-secondary" href="/huongviet/admin/orders/view.php?id=<?php echo $id; ?>">Quay lại chi tiết</a>
</div>
<main class="invoice">
    <h1>ẨM THỰC HƯƠNG VIỆT BẾN CÁT</h1>
    <p class="text-center small mb-2">Đường DJ 10, KDC Mỹ Phước 3, P. Thới Hòa, TP Hồ Chí Minh<br>Hotline: 0988659291</p>
    <hr>
    <p class="small mb-1"><strong>Mã hóa đơn:</strong> #<?php echo $id; ?></p>
    <p class="small mb-1"><strong>Ngày tạo:</strong> <?php echo e($order['order_date']); ?></p>
    <p class="small mb-1"><strong>Khách hàng:</strong> <?php echo e($order['customer_name'] ?: 'Khách vãng lai'); ?></p>
    <p class="small mb-1"><strong>Bàn:</strong> <?php echo e($order['table_name']); ?></p>
    <p class="small mb-2"><strong>Nhân viên:</strong> <?php echo e($order['user_name']); ?></p>
    <table class="table table-sm small">
        <thead><tr><th>Món</th><th class="text-end">SL</th><th class="text-end">Tiền</th></tr></thead>
        <tbody>
            <?php while($d=mysqli_fetch_assoc($details)){ ?>
                <tr><td><?php echo e($d['food_name']); ?><br><span class="text-muted"><?php echo orderMoney($d['price']); ?></span></td><td class="text-end"><?php echo (int)$d['quantity']; ?></td><td class="text-end"><?php echo orderMoney($d['total_price']); ?></td></tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="small">
        <div class="d-flex justify-content-between"><span>Tổng tiền</span><strong><?php echo orderMoney($order['total_amount']); ?></strong></div>
        <div class="d-flex justify-content-between"><span>Giảm giá</span><strong><?php echo orderMoney($order['discount']); ?></strong></div>
        <div class="d-flex justify-content-between fs-6 mt-1"><span>Thành tiền</span><strong><?php echo orderMoney($order['final_amount']); ?></strong></div>
        <div class="mt-2"><strong>Phương thức:</strong> <?php echo e($order['payment_method']); ?></div>
    </div>
    <hr>
    <p class="text-center small mb-0">Cảm ơn quý khách. Hẹn gặp lại!</p>
</main>
</body>
</html>
