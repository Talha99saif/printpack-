<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Services\PricingService;
use App\Services\UploadService;
final class OrderController {
    public function cart(): void {
        require_login(); $cart=$_SESSION['cart']??[];$items=[];$subtotal=0;
        if($cart){$ids=array_map('intval',array_keys($cart));$in=implode(',',array_fill(0,count($ids),'?'));$stmt=db()->prepare("SELECT s.*,u.name vendor_name FROM services s JOIN users u ON u.id=s.vendor_id WHERE s.id IN ($in)");$stmt->execute($ids);foreach($stmt->fetchAll() as $s){$s['quantity']=$cart[$s['id']];$s['line_total']=$s['base_price']*$s['quantity'];$subtotal+=$s['line_total'];$items[]=$s;}}
        view('orders/cart',compact('items','subtotal'));
    }
    public function checkout(): void {
        $user=require_login();$cart=$_SESSION['cart']??[];if(!$cart){flash('error','Your cart is empty.');redirect('services');}
        $ids=array_map('intval',array_keys($cart));$in=implode(',',array_fill(0,count($ids),'?'));$stmt=db()->prepare("SELECT * FROM services WHERE id IN ($in) AND status='active'");$stmt->execute($ids);$services=$stmt->fetchAll();
        if(!$services){flash('error','Your cart contains no available services.');redirect('cart');}
        $distance=max(0,min(500,(float)($_POST['distance_km']??5)));$address=trim($_POST['delivery_address']??'');if($address===''){flash('error','Enter a delivery address.');redirect('cart');}
        $design=null;try{$design=UploadService::saveDesign($_FILES['design']??[]);}catch(\RuntimeException $e){flash('error',$e->getMessage());redirect('cart');}
        $pdo=db();$pdo->beginTransaction();
        try {
            $groups=[];foreach($services as $s){$qty=(int)($cart[$s['id']]??1);$quote=PricingService::quote((float)$s['base_price'],$qty,0,$distance);$groups[$s['vendor_id']]['items'][]=['service'=>$s,'qty'=>$qty,'quote'=>$quote];$groups[$s['vendor_id']]['subtotal']=($groups[$s['vendor_id']]['subtotal']??0)+$quote['subtotal'];$groups[$s['vendor_id']]['delivery']=($groups[$s['vendor_id']]['delivery']??0)+$quote['delivery'];}
            foreach($groups as $vendorId=>$group){$total=$group['subtotal']+$group['delivery'];$stmt=$pdo->prepare("INSERT INTO orders (customer_id,vendor_id,delivery_address,distance_km,subtotal,delivery_fee,total_amount,payment_method,payment_status,status,design_file) VALUES (?,?,?,?,?,?,?,'cod','unpaid','pending',?)");$stmt->execute([$user['id'],$vendorId,$address,$distance,$group['subtotal'],$group['delivery'],$total,$design]);$orderId=(int)$pdo->lastInsertId();foreach($group['items'] as $item){$s=$item['service'];$pdo->prepare('INSERT INTO order_items (order_id,service_id,quantity,unit_price,total_price,customization_details) VALUES (?,?,?,?,?,?)')->execute([$orderId,$s['id'],$item['qty'],$s['base_price'],$item['quote']['subtotal'],json_encode(['size'=>$s['size_label'],'material'=>$s['material']])]);}$pdo->prepare("INSERT INTO notifications (user_id,title,message) VALUES (?,?,?)")->execute([$user['id'],'Order received','Order #'.$orderId.' was placed with cash on delivery.']);}
            $pdo->commit();unset($_SESSION['cart']);flash('success','Order placed successfully with Cash on Delivery.');redirect('my-orders');
        }catch(\Throwable $e){$pdo->rollBack();throw $e;}
    }
    public function mine(): void {$user=require_login();$stmt=db()->prepare('SELECT o.*,u.name vendor_name FROM orders o JOIN users u ON u.id=o.vendor_id WHERE o.customer_id=? ORDER BY o.created_at DESC');$stmt->execute([$user['id']]);$orders=$stmt->fetchAll();view('orders/mine',compact('orders'));}
    public function updateStatus(): void {$vendor=require_role('vendor','admin');$id=(int)($_POST['order_id']??0);$status=$_POST['status']??'processing';$allowed=['accepted','processing','dispatched','delivered','cancelled'];if(!in_array($status,$allowed,true)){flash('error','Invalid order status.');redirect('vendor');}$sql="UPDATE orders SET status=? WHERE id=?";$args=[$status,$id];if($vendor['role']==='vendor'){$sql.=' AND vendor_id=?';$args[]=$vendor['id'];}$stmt=db()->prepare($sql);$stmt->execute($args);if($stmt->rowCount()){$q=db()->prepare('SELECT customer_id FROM orders WHERE id=?');$q->execute([$id]);$customer=$q->fetchColumn();if($customer)db()->prepare('INSERT INTO notifications(user_id,title,message) VALUES (?,?,?)')->execute([$customer,'Order update','Order #'.$id.' status changed to '.ucfirst($status).'.']);}flash('success','Order status updated.');redirect($vendor['role']==='admin'?'admin':'vendor');}
}
