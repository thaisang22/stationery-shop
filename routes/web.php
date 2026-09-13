<?php

use App\Controllers\Admin\AdminController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\PostController;
use App\Controllers\ProductController;

$router->get('/', [
    HomeController::class,
    'index'
]);

$router->get('/products', [
    ProductController::class,
    'index'
]);

$router->get('/product/{slug}', [
    ProductController::class,
    'detail'
]);

$router->get('/cart', [
    CartController::class,
    'index'
]);
$router->post('/cart/add', [
    CartController::class,
    'add'
]);
$router->post('/cart/update', [
    CartController::class,
    'update'
]);
$router->post('/cart/remove', [
    CartController::class,
    'remove'
]);

$router->get('/login', [
    AuthController::class,
    'login'
]);

$router->post('/login/mock', [
    AuthController::class,
    'mockLogin'
]);

$router->get('/register', [
    AuthController::class,
    'register'
]);

$router->get('/checkout', [App\Controllers\OrderController::class, 'checkout']);
$router->post('/checkout/process', [App\Controllers\OrderController::class, 'process']);
$router->get('/checkout/success', [App\Controllers\OrderController::class, 'success']);

$router->get('/posts', [
    PostController::class,
    'index'
]);

$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/products', [AdminController::class, 'products']);
$router->get('/admin/categories', [AdminController::class, 'categories']);
$router->get('/admin/orders', [AdminController::class, 'orders']);
$router->get('/admin/customers', [AdminController::class, 'customers']);
$router->get('/admin/posts', [AdminController::class, 'posts']);
$router->get('/admin/sales', [AdminController::class, 'sales']);
