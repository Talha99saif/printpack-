<?php
declare(strict_types=1);
use App\Core\Database;

function db(): PDO { return Database::connection(); }
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return rtrim($_ENV['APP_URL'] ?? '/printpack/public', '/') . ($path ? '/' . ltrim($path, '/') : '/'); }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function csrf_token(): string { if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32)); return $_SESSION['_csrf']; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">'; }
function verify_csrf(): void { if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['_csrf'] ?? '', $_POST['_csrf'] ?? '')) { http_response_code(419); exit('Session token expired. Go back and try again.'); } }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): array { if (!current_user()) redirect('login'); return current_user(); }
function require_role(string ...$roles): array { $user = require_login(); if (!in_array($user['role'], $roles, true)) { http_response_code(403); exit('You do not have permission to view this page.'); } return $user; }
function flash(string $key, ?string $value = null): ?string { if ($value !== null) { $_SESSION['_flash'][$key] = $value; return null; } $msg = $_SESSION['_flash'][$key] ?? null; unset($_SESSION['_flash'][$key]); return $msg; }
function money(float|int|string $amount): string { return 'PKR ' . number_format((float)$amount, 2); }
function status_class(string $status): string { return 'status-' . strtolower(str_replace(' ', '-', $status)); }
function setting(string $key, string $fallback = ''): string { return $_ENV[$key] ?? $fallback; }
