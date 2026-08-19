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

$router->get('/product', [
    ProductController::class,
    'detail'
]);

$router->get('/cart', [
    CartController::class,
    'index'
]);

$router->get('/login', [
    AuthController::class,
    'login'
]);

$router->get('/register', [
    AuthController::class,
    'register'
]);

$router->get('/checkout', [
    OrderController::class,
    'checkout'
]);

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
