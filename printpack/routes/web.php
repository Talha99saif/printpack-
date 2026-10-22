<?php
declare(strict_types=1);
use App\Core\Router;
use App\Controllers\{AuthController,HomeController,ServiceController,OrderController,DashboardController,ReviewController,VendorServiceController};
$router=new Router();$auth=new AuthController();$home=new HomeController();$services=new ServiceController();$orders=new OrderController();$dash=new DashboardController();$reviews=new ReviewController();$vendorServices=new VendorServiceController();
$router->get('/',fn()=>$home->index());$router->get('/services',fn()=>$services->index());
$router->get('/login',fn()=>$auth->showLogin());$router->post('/login',fn()=>$auth->login());$router->get('/register',fn()=>$auth->showRegister());$router->post('/register',fn()=>$auth->register());$router->post('/logout',fn()=>$auth->logout());
$router->post('/cart/add',fn()=>$services->addToCart());$router->get('/cart',fn()=>$orders->cart());$router->post('/checkout',fn()=>$orders->checkout());$router->get('/my-orders',fn()=>$orders->mine());$router->post('/orders/status',fn()=>$orders->updateStatus());$router->post('/reviews',fn()=>$reviews->submit());
$router->get('/dashboard',fn()=>$dash->customer());$router->get('/vendor',fn()=>$dash->vendor());$router->post('/vendor/services/save',fn()=>$vendorServices->save());$router->get('/admin',fn()=>$dash->admin());$router->post('/admin/vendor-status',fn()=>$dash->approveVendor());
return $router;
