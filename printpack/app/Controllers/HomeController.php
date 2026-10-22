<?php
declare(strict_types=1);
namespace App\Controllers;
final class HomeController {
    public function index(): void {
        $q = trim($_GET['q'] ?? ''); $category = trim($_GET['category'] ?? '');
        $sql = "SELECT s.*, u.name AS vendor_name, c.name AS category_name FROM services s JOIN users u ON u.id=s.vendor_id LEFT JOIN categories c ON c.id=s.category_id WHERE s.status='active' AND u.status='active'";
        $params = [];
        if ($q !== '') { $sql .= ' AND (s.name LIKE ? OR s.description LIKE ? OR u.name LIKE ?)'; $like = '%' . $q . '%'; array_push($params, $like, $like, $like); }
        if ($category !== '') { $sql .= ' AND c.slug = ?'; $params[] = $category; }
        $sql .= ' ORDER BY s.created_at DESC LIMIT 8'; $stmt = db()->prepare($sql); $stmt->execute($params); $services = $stmt->fetchAll();
        $categories = db()->query('SELECT id,name,slug,icon FROM categories WHERE status="active" ORDER BY sort_order,name')->fetchAll();
        view('home', compact('services','categories','q','category'));
    }
}
