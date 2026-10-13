<?php

use App\Controllers\Admin\AdminController;
use App\Controllers\Admin\AdminAuthController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\HomeController;
use App\Controllers\LayoutController;
use App\Controllers\OrderController;
use App\Controllers\PostController;
use App\Controllers\ProductController;
use App\Controllers\Admin\PostControllerAdmin;
use App\Controllers\WebhookController;

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

use App\Controllers\UserProfileController;

$router->get('/login', [
    AuthController::class,
    'login'
]);

$router->post('/login', [
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

$router->post('/register', [
    AuthController::class,
    'register'
]);

$router->get('/logout', [
    AuthController::class,
    'logout'
]);
$router->post('/logout', [
    AuthController::class,
    'logout'
]);

// User Profile Routes
$router->get('/profile', [UserProfileController::class, 'index']);
$router->post('/profile/update', [UserProfileController::class, 'updateInfo']);
$router->post('/profile/change-password', [UserProfileController::class, 'changePassword']);
$router->post('/profile/test-email', [UserProfileController::class, 'testMail']);

$router->get('/checkout', [App\Controllers\OrderController::class, 'checkout']);

$router->post('/checkout/process', [App\Controllers\OrderController::class, 'process']);
$router->get('/checkout/success', [App\Controllers\OrderController::class, 'success']);

$router->get('/posts', [
    PostController::class,
    'index'
]);

$router->get('/posts/{slug}', [
    PostController::class,
    'detail'
]);

// sepay 
$router->post('/webhook/sepay', [WebhookController::class, 'sepay']);
$router->get('/api/orders/check-status', [OrderController::class, 'checkStatus']);
$router->post('/orders/cancel', [OrderController::class, 'cancel']);
$router->post('/api/orders/cancel', [OrderController::class, 'cancelApi']);
//ghn
$router->post('/webhook/ghn', [WebhookController::class, 'ghn']);


// Admin Auth Routes (Trang đăng nhập / đăng xuất riêng cho Admin)
$router->get('/admin/login', [AdminAuthController::class, 'login']);
$router->post('/admin/login', [AdminAuthController::class, 'login']);
$router->get('/admin/logout', [AdminAuthController::class, 'logout']);
$router->post('/admin/logout', [AdminAuthController::class, 'logout']);

$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);

// Admin Products CRUD
$router->get('/admin/products', [AdminController::class, 'products']);
$router->get('/admin/products/create', [AdminController::class, 'createProduct']);
$router->post('/admin/products/create', [AdminController::class, 'storeProduct']);
$router->get('/admin/products/{id}/edit', [AdminController::class, 'editProduct']);
$router->post('/admin/products/{id}/edit', [AdminController::class, 'updateProduct']);
$router->post('/admin/products/{id}/delete', [AdminController::class, 'deleteProduct']);

// Admin Categories & Customers
$router->get('/admin/categories', [AdminController::class, 'categories']);
$router->get('/admin/customers', [AdminController::class, 'customers']);

// Admin Orders
$router->get('/admin/orders', [AdminController::class, 'orders']);
$router->get('/admin/orders/{id}', [AdminController::class, 'orderDetail']);
$router->post('/admin/orders/{id}/status', [AdminController::class, 'updateOrderStatus']);

// Admin Posts
$router->get('/admin/posts', [PostControllerAdmin::class, 'posts']);
$router->get('/admin/posts/create', [PostControllerAdmin::class, 'createPost']);
$router->post('/admin/posts', [PostControllerAdmin::class, 'storePost']);
$router->get('/admin/posts/{id}/edit', [PostControllerAdmin::class, 'editPost']);
$router->post('/admin/posts/{id}', [PostControllerAdmin::class, 'updatePost']);
$router->post('/admin/posts/{id}/hidden', [PostControllerAdmin::class, 'toggleHidden']);
$router->post('/admin/posts/{id}/delete', [PostControllerAdmin::class, 'deletePost']);

// Admin Sales Statistics
$router->get('/admin/sales', [AdminController::class, 'sales']);

