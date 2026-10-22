<?php
declare(strict_types=1);
namespace App\Controllers;
final class AuthController {
    public function showLogin(): void { view('auth/login'); }
    public function showRegister(): void { view('auth/register'); }
    public function register(): void {
        $name = trim($_POST['name'] ?? ''); $email = strtolower(trim($_POST['email'] ?? ''));
         $password = $_POST['password'] ?? '';
        $role = ($_POST['role'] ?? 'customer') === 'vendor' ? 'vendor' : 'customer';
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) { flash('error', 'Enter a name, valid email, and password with at least 8 characters.'); redirect('register'); }
        $check = db()->prepare('SELECT id FROM users WHERE email = ?'); $check->execute([$email]);
        if ($check->fetch()) { flash('error', 'An account already exists for that email.'); redirect('register'); }
        $status = $role === 'vendor' ? 'pending' : 'active';
        $stmt = db()->prepare('INSERT INTO users (name,email,password_hash,role,status) VALUES (?,?,?,?,?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $status]);
        flash('success', $role === 'vendor' ? 'Vendor account submitted. An administrator must approve it.' : 'Account created. Please sign in.'); redirect('login');
    }
    public function login(): void {
        $email = strtolower(trim($_POST['email'] ?? '')); $password = $_POST['password'] ?? '';
        $stmt = db()->prepare('SELECT id,name,email,password_hash,role,status FROM users WHERE email = ? LIMIT 1'); $stmt->execute([$email]); $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash']) || $user['status'] !== 'active') { flash('error', 'Sign-in failed. Check your details or vendor approval status.'); redirect('login'); }
        session_regenerate_id(true); unset($user['password_hash']); $_SESSION['user'] = $user;
        redirect($user['role'] === 'admin' ? 'admin' : ($user['role'] === 'vendor' ? 'vendor' : 'dashboard'));
    }
    public function logout(): void { $_SESSION = []; session_regenerate_id(true); flash('success', 'You have signed out.'); redirect(''); }
}
