<?php
declare(strict_types=1);
namespace App\Controllers;
final class ReviewController {
 public function submit(): void {$u=require_role('customer');$order=(int)($_POST['order_id']??0);$rating=max(1,min(5,(int)($_POST['rating']??5)));$comment=trim($_POST['comment']??'');$q=db()->prepare("SELECT vendor_id FROM orders WHERE id=? AND customer_id=? AND status='delivered'");$q->execute([$order,$u['id']]);$vendor=$q->fetchColumn();if(!$vendor){flash('error','Reviews are available after delivery.');redirect('my-orders');}db()->prepare('INSERT INTO reviews(order_id,customer_id,vendor_id,rating,comment) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE rating=VALUES(rating),comment=VALUES(comment)')->execute([$order,$u['id'],$vendor,$rating,$comment]);flash('success','Thank you for your feedback.');redirect('my-orders');}
}
