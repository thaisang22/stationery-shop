<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Order;
use App\Services\MailService;

class UserProfileController extends Controller
{
    private User $userModel;
    private Order $orderModel;
    private MailService $mailService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->mailService = new MailService();
    }

    /**
     * Hiển thị trang Profile người dùng
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = '/profile';
            header('Location: ' . url('/login'));
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);

        if (!$user) {
            unset($_SESSION['user_id'], $_SESSION['user']);
            header('Location: ' . url('/login'));
            exit;
        }

        $orders = $this->orderModel->getOrdersByUserId($userId);

        $successMessage = $_SESSION['profile_success'] ?? null;
        $errorMessage = $_SESSION['profile_error'] ?? null;

        unset($_SESSION['profile_success'], $_SESSION['profile_error']);

        $this->view('userprofile/index', [
            'pageTitle'      => 'Trang cá nhân - ' . htmlspecialchars($user['full_name']),
            'pageCss'        => ['userprofile'],
            'user'           => $user,
            'orders'         => $orders,
            'successMessage' => $successMessage,
            'errorMessage'   => $errorMessage,
        ]);
    }

    /**
     * Xử lý cập nhật thông tin cá nhân
     */
    public function updateInfo(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/profile'));
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone_number'] ?? '');

        if (mb_strlen($fullName) < 2) {
            $_SESSION['profile_error'] = 'Họ và tên không được để trống và phải từ 2 ký tự trở lên.';
            header('Location: ' . url('/profile?tab=info'));
            exit;
        }

        $phoneNumber = $phone !== '' ? $phone : null;

        $updated = $this->userModel->updateProfile($userId, $fullName, $phoneNumber);

        if ($updated) {
            // Cập nhật lại session user name
            if (isset($_SESSION['user'])) {
                $_SESSION['user']['full_name'] = $fullName;
            }
            $_SESSION['profile_success'] = 'Cập nhật thông tin cá nhân thành công!';

            // Gửi thông báo bảo mật qua Mail nếu có email
            $user = $this->userModel->findById($userId);
            if (!empty($user['email'])) {
                @$this->mailService->sendProfileUpdateAlert($user['email'], $fullName);
            }
        } else {
            $_SESSION['profile_error'] = 'Có lỗi xảy ra trong quá trình cập nhật thông tin.';
        }

        header('Location: ' . url('/profile?tab=info'));
        exit;
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function changePassword(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/profile'));
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = $this->userModel->findById($userId);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            $_SESSION['profile_error'] = 'Mật khẩu hiện tại không chính xác.';
            header('Location: ' . url('/profile?tab=password'));
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['profile_error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            header('Location: ' . url('/profile?tab=password'));
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['profile_error'] = 'Xác nhận mật khẩu mới không trùng khớp.';
            header('Location: ' . url('/profile?tab=password'));
            exit;
        }

        $updated = $this->userModel->updatePassword($userId, $newPassword);

        if ($updated) {
            $_SESSION['profile_success'] = 'Đổi mật khẩu thành công! Lần đăng nhập sau hãy dùng mật khẩu mới.';
            if (!empty($user['email'])) {
                @$this->mailService->sendProfileUpdateAlert($user['email'], $user['full_name']);
            }
        } else {
            $_SESSION['profile_error'] = 'Có lỗi xảy ra khi thay đổi mật khẩu.';
        }

        header('Location: ' . url('/profile?tab=password'));
        exit;
    }
}
