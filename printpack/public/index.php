<?php
declare(strict_types=1);
require dirname(__DIR__) . '/config/bootstrap.php';
require BASE_PATH . '/app/Core/View.php';
$router=require BASE_PATH . '/routes/web.php';
$base='/'.trim(str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$path=parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if($base!=='/' && str_starts_with($path,$base))$path=substr($path,strlen($base));
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET',$path);
