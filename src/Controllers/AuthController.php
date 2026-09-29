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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/login', [
                'pageCss' => 'login',
            ]);

            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->login(
            $email,
            $password
        );

        if (!$user) {
            $_SESSION['login_error'] = 'Email hoặc mật khẩu không chính xác.';

            header('Location: ' . url('/login'));
            exit;
        }

        // Đăng nhập thành công
        $_SESSION['user_id'] = $user['id'];

        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
        ];

        header('Location: ' . url('/'));
        exit;
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/register', [
                'pageCss' => 'login',
            ]);
            return;
        }

        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $this->userModel->register(
            $name,
            $email,
            $password
        );
        $_SESSION['register_success'] = 'Đăng ký tài khoản thành công!';

        header('Location: ' . url('/login'));
        exit;
    }

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