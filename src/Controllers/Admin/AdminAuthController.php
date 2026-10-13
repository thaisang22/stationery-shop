<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;

class AdminAuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Hiển thị form & xử lý đăng nhập riêng cho Admin
     */
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu đã là admin rồi thì chuyển thẳng vào dashboard
        if (isAdmin()) {
            header('Location: ' . url('/admin/dashboard'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $error = $_SESSION['admin_login_error'] ?? null;
            unset($_SESSION['admin_login_error']);

            $this->view('admin/login', [
                'pageCss' => 'admin',
                'error' => $error,
            ]);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['admin_login_error'] = 'Vui lòng nhập đầy đủ Email và Mật khẩu.';
            header('Location: ' . url('/admin/login'));
            exit();
        }

        $user = $this->userModel->login($email, $password);

        if (!$user) {
            $_SESSION['admin_login_error'] = 'Email hoặc mật khẩu không chính xác.';
            header('Location: ' . url('/admin/login'));
            exit();
        }

        // BẮT BUỘC KIỂM TRA ROLE ADMIN
        if (strtoupper((string)($user['role'] ?? '')) !== 'ADMIN') {
            $_SESSION['admin_login_error'] = 'Tài khoản này là Khách hàng, không có quyền truy cập trang Quản trị viên!';
            header('Location: ' . url('/admin/login'));
            exit();
        }

        // Đăng nhập Admin thành công
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = 'ADMIN';
        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => 'ADMIN',
        ];

        $redirect = (string) ($_SESSION['redirect_after_admin_login'] ?? '/admin/dashboard');
        unset($_SESSION['redirect_after_admin_login']);

        $config = require dirname(__DIR__, 3) . '/config/config.php';
        $baseUrl = rtrim((string) ($config['app']['base_url'] ?? ''), '/');
        $basePath = rtrim((string) (parse_url($baseUrl, PHP_URL_PATH) ?? ''), '/');

        if ($basePath !== '' && str_starts_with($redirect, $basePath)) {
            $redirect = substr($redirect, strlen($basePath));
        }

        $redirect = '/' . ltrim($redirect, '/');

        if ($redirect === '/' || $redirect === '/admin/login' || $redirect === '') {
            $redirect = '/admin/dashboard';
        }

        header('Location: ' . url($redirect));
        exit();
    }

    /**
     * Đăng xuất Admin
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user'], $_SESSION['redirect_after_admin_login']);

        $_SESSION['admin_login_error'] = 'Bạn đã đăng xuất khỏi hệ thống Quản trị.';
        header('Location: ' . url('/admin/login'));
        exit();
    }
}
