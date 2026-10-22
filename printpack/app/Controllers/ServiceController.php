<?php
declare(strict_types=1);
namespace App\Controllers;
final class ServiceController {
    public function index(): void {
        $q = trim($_GET['q'] ?? ''); $category = trim($_GET['category'] ?? ''); $sort = $_GET['sort'] ?? 'newest';
        $sql = "SELECT s.*,u.name vendor_name,c.name category_name,c.slug category_slug FROM services s JOIN users u ON u.id=s.vendor_id LEFT JOIN categories c ON c.id=s.category_id WHERE s.status='active' AND u.status='active'"; $params=[];
        if ($q !== '') { $sql .= ' AND (s.name LIKE ? OR s.description LIKE ? OR u.name LIKE ?)'; $like='%'.$q.'%'; array_push($params,$like,$like,$like); }
        if ($category !== '') { $sql .= ' AND c.slug=?'; $params[]=$category; }
        $sql .= $sort === 'price' ? ' ORDER BY s.base_price ASC' : ($sort === 'popular' ? ' ORDER BY s.order_count DESC,s.created_at DESC' : ' ORDER BY s.created_at DESC');
        $stmt=db()->prepare($sql);$stmt->execute($params);$services=$stmt->fetchAll();$categories=db()->query('SELECT * FROM categories WHERE status="active" ORDER BY sort_order,name')->fetchAll();
        view('services/index',compact('services','categories','q','category','sort'));
    }
    public function addToCart(): void {
        require_login(); $id=(int)($_POST['service_id']??0); $qty=max(1,min(10000,(int)($_POST['quantity']??1)));
        $stmt=db()->prepare("SELECT id FROM services WHERE id=? AND status='active'");$stmt->execute([$id]);if(!$stmt->fetch()){flash('error','Service is unavailable.');redirect('services');}
        $_SESSION['cart']??=[];$_SESSION['cart'][$id]=min(10000,($_SESSION['cart'][$id]??0)+$qty);flash('success','Added to your cart.');redirect('services');
    }
}
