<?php
declare(strict_types=1);
function view(string $name, array $data = []): void {
    extract($data, EXTR_SKIP);
    $viewFile = BASE_PATH . '/app/Views/' . $name . '.php';
    if (!is_file($viewFile)) { http_response_code(500); exit('View not found: ' . e($name)); }
    require BASE_PATH . '/app/Views/layouts/header.php';
    require $viewFile;
    require BASE_PATH . '/app/Views/layouts/footer.php';
}
