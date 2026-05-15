<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
orderRequireAccess();
$id=(int)($_GET['id']??0);
$success='';$error='';
if($id<=0){$error='Không tìm thấy order.';}else{$order=getOrder($conn,$id);if(!$order){$error='Không tìm thấy order.';}elseif($order['payment_status']!=='Chưa thanh toán'||$order['order_status']!=='Đang phục vụ'){$error='Chỉ có thể hủy order đang phục vụ và chưa thanh toán.';}else{mysqli_begin_transaction($conn);try{$os='Đã hủy';$stmt=mysqli_prepare($conn,"UPDATE orders SET order_status=? WHERE id=?");mysqli_stmt_bind_param($stmt,"si",$os,$id);mysqli_stmt_execute($stmt);if($order['table_id']){$s='Trống';$tid=(int)$order['table_id'];$stmt=mysqli_prepare($conn,"UPDATE tables_restaurant SET status=? WHERE id=?");mysqli_stmt_bind_param($stmt,"si",$s,$tid);mysqli_stmt_execute($stmt);}mysqli_commit($conn);$success='Hủy order thành công.';}catch(Throwable $e){mysqli_rollback($conn);$error='Không thể hủy order.';}}}
$query=$success!==''?'?success='.urlencode($success):'?error='.urlencode($error);
header("Location: /huongviet/admin/orders/index.php".$query);exit;
?>
