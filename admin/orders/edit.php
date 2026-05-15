<?php
require_once __DIR__ . "/../../config/database.php";
/** @var mysqli $conn */
require_once __DIR__ . "/../auth.php";
requireLogin();
require_once __DIR__ . "/_helpers.php";
orderRequireAccess();

$id=(int)($_GET['id']??0); if($id<=0){header("Location: /huongviet/admin/orders/index.php");exit;}
$order=getOrder($conn,$id); if(!$order){header("Location: /huongviet/admin/orders/index.php");exit;}
if($order['payment_status']!=='Chưa thanh toán'||$order['order_status']!=='Đang phục vụ'){header("Location: /huongviet/admin/orders/view.php?id=".$id."&error=".urlencode("Không thể sửa order đã thanh toán, đã hoàn thành hoặc đã hủy."));exit;}
$errors=[];
function foodForOrder(mysqli $conn,int $id){$stmt=mysqli_prepare($conn,"SELECT id,price FROM foods WHERE id=? AND status='Còn bán' LIMIT 1");mysqli_stmt_bind_param($stmt,"i",$id);mysqli_stmt_execute($stmt);return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));}
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
    $customerId=(int)($_POST['customer_id']??0); $tableId=(int)($_POST['table_id']??0); $note=trim($_POST['note']??'');
    $foodIds=$_POST['food_id']??[]; $qtys=$_POST['quantity']??[]; $details=[]; $total=0;
    for($i=0;$i<count($foodIds);$i++){ $fid=(int)($foodIds[$i]??0); if($fid<=0)continue; $qty=(int)($qtys[$i]??0); if($qty<=0){$errors[]='Số lượng món phải > 0.';continue;} $food=foodForOrder($conn,$fid); if(!$food){$errors[]='Món ăn không hợp lệ.';continue;} $price=(float)$food['price']; $line=$price*$qty; $details[]=compact('fid','qty','price','line'); $total+=$line; }
    if(!$details)$errors[]='Phải chọn ít nhất 1 món hợp lệ.';
    if(!$errors){mysqli_begin_transaction($conn);try{
        $customerId=$customerId>0?$customerId:null; $tableId=$tableId>0?$tableId:null; $final=max(0,$total-(float)$order['discount']);
        $stmt=mysqli_prepare($conn,"UPDATE orders SET customer_id=?, table_id=?, note=?, total_amount=?, final_amount=? WHERE id=?");
        mysqli_stmt_bind_param($stmt,"iisddi",$customerId,$tableId,$note,$total,$final,$id); mysqli_stmt_execute($stmt);
        $stmt=mysqli_prepare($conn,"DELETE FROM order_details WHERE order_id=?"); mysqli_stmt_bind_param($stmt,"i",$id); mysqli_stmt_execute($stmt);
        foreach($details as $d){$stmt=mysqli_prepare($conn,"INSERT INTO order_details(order_id,food_id,quantity,price,total_price) VALUES(?,?,?,?,?)");mysqli_stmt_bind_param($stmt,"iiidd",$id,$d['fid'],$d['qty'],$d['price'],$d['line']);mysqli_stmt_execute($stmt);}
        if($tableId){$s='Đang sử dụng';$stmt=mysqli_prepare($conn,"UPDATE tables_restaurant SET status=? WHERE id=?");mysqli_stmt_bind_param($stmt,"si",$s,$tableId);mysqli_stmt_execute($stmt);}
        mysqli_commit($conn); header("Location: /huongviet/admin/orders/view.php?id=".$id);exit;
    }catch(Throwable $e){mysqli_rollback($conn);$errors[]='Không thể cập nhật order: '.$e->getMessage();}}
}
$customers=mysqli_query($conn,"SELECT id,customer_name,phone FROM customers ORDER BY customer_name ASC");
$tables=mysqli_query($conn,"SELECT id,table_name,status FROM tables_restaurant ORDER BY id ASC");
$foods=mysqli_query($conn,"SELECT id,food_name,price FROM foods WHERE status='Còn bán' ORDER BY food_name ASC");$foodRows=[];while($f=mysqli_fetch_assoc($foods))$foodRows[]=$f;
$old=[];$details=getOrderDetails($conn,$id);while($d=mysqli_fetch_assoc($details))$old[]=$d;
orderHeader("Sửa order");
?>
<main class="container py-4"><div class="card border-0 shadow-sm"><div class="card-body p-4">
<div class="d-flex justify-content-between mb-3"><h1 class="h3 mb-0">Sửa order #<?php echo $id; ?></h1><a class="btn btn-outline-secondary" href="/huongviet/admin/orders/view.php?id=<?php echo $id; ?>">Quay lại</a></div>
<?php if($errors){?><div class="alert alert-danger"><?php foreach($errors as $er)echo'<div>'.e($er).'</div>';?></div><?php }?>
<form method="POST"><div class="row g-3 mb-4"><div class="col-md-4"><label class="form-label">Khách hàng</label><select class="form-select" name="customer_id"><option value="0">Khách vãng lai</option><?php while($c=mysqli_fetch_assoc($customers)){?><option value="<?php echo (int)$c['id'];?>" <?php echo (int)$order['customer_id']===(int)$c['id']?'selected':'';?>><?php echo e($c['customer_name'].' - '.$c['phone']);?></option><?php }?></select></div><div class="col-md-4"><label class="form-label">Bàn</label><select class="form-select" name="table_id"><option value="0">-- Không chọn --</option><?php while($t=mysqli_fetch_assoc($tables)){?><option value="<?php echo (int)$t['id'];?>" <?php echo (int)$order['table_id']===(int)$t['id']?'selected':'';?>><?php echo e($t['table_name'].' - '.$t['status']);?></option><?php }?></select></div><div class="col-md-4"><label class="form-label">Ghi chú</label><input class="form-control" name="note" value="<?php echo e($order['note']);?>"></div></div>
<div class="table-responsive"><table class="table"><thead class="table-light"><tr><th>Món</th><th style="width:180px">Số lượng</th></tr></thead><tbody><?php for($i=0;$i<8;$i++){ $cur=$old[$i]??null;?><tr><td><select class="form-select" name="food_id[]"><option value="0">-- Chọn món --</option><?php foreach($foodRows as $f){?><option value="<?php echo (int)$f['id'];?>" <?php echo $cur&&(int)$cur['food_id']===(int)$f['id']?'selected':'';?>><?php echo e($f['food_name'].' - '.orderMoney($f['price']));?></option><?php }?></select></td><td><input type="number" min="1" class="form-control" name="quantity[]" value="<?php echo $cur?(int)$cur['quantity']:'';?>"></td></tr><?php }?></tbody></table></div><button class="btn btn-primary">Cập nhật order</button></form>
</div></div></main><?php orderFooter(); ?>
