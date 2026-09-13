<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu đã đăng nhập rồi thì chuyển về trang checkout (hoặc trang chủ)
        if (isset($_SESSION['user_id']) || isset($_SESSION['user'])) {
            $redirectUrl = $_SESSION['redirect_after_login'] ?? '/';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . url($redirectUrl));
            exit();
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('auth/login', [
            'pageCss' => 'login',
            'error'   => $error,
        ]);
    }

    public function register(): void
    {
        $this->view('auth/register', [
            'pageCss' => 'login',
        ]);
    }

    /**
     * Giả lập đăng nhập nhanh 1-Click để test chức năng Thanh toán (Checkout)
     */
    public function mockLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Tạo thông tin User giả lập trong Session
        $_SESSION['user_id'] = 1;
        $_SESSION['user'] = [
            'id'       => 1,
            'fullname' => 'Võ Thái Sang',
            'email'    => 'thaisang@gmail.com',
            'phone'    => '0901234567',
            'address'  => '12 Nguyễn Văn Bảo, Phường 4, Gò Vấp, TP.HCM',
        ];

        // 2. Chuyển hướng người dùng quay lại trang Checkout (hoặc trang chủ)
        $redirectUrl = $_SESSION['redirect_after_login'] ?? '/checkout';
        unset($_SESSION['redirect_after_login']);

        header('Location: ' . url($redirectUrl));
        exit();
    }

    /**
     * Đăng xuất hệ thống
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['user_id'], $_SESSION['user'], $_SESSION['redirect_after_login']);

        header('Location: ' . url('/login'));
        exit();
    }
}