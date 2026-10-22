<?php
declare(strict_types=1);
namespace App\Core;
final class Router {
    private array $routes = [];
    public function get(string $path, callable $handler): void { $this->routes['GET'][$this->normalize($path)] = $handler; }
    public function post(string $path, callable $handler): void { $this->routes['POST'][$this->normalize($path)] = $handler; }
    public function dispatch(string $method, string $path): void {
        $path = $this->normalize($path);
        $handler = $this->routes[$method][$path] ?? null;
        if (!$handler) { http_response_code(404); echo view('errors/404'); return; }
        verify_csrf();
        $handler();
    }
    private function normalize(string $path): string { return '/' . trim($path, '/'); }
}
