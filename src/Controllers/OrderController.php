<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Exception;

class OrderController extends Controller
{
    private Cart $cartModel;
    private Order $orderModel;
    private User $userModel;

    public function __construct()
    {
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->userModel = new User();
    }
    public function checkout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = '/checkout';
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiến hành thanh toán.';
            header('Location: ' . url('/login'));
            exit();
        }

        $userId = (int) $_SESSION['user_id'];

        $user = method_exists($this->userModel, 'findById')
            ? $this->userModel->findById($userId)
            : ($_SESSION['user'] ?? []);

        // Đồng bộ dữ liệu tên cột từ DB 
        if (!empty($user)) {
            $user['fullname'] = $user['full_name'] ?? $user['fullname'] ?? '';
            $user['phone'] = $user['phone_number'] ?? $user['phone'] ?? '';
        }

        // Lấy danh sách sản phẩm giỏ hàng từ DB
        $cartItems = $this->cartModel->getCartByUserId($userId);

        if (empty($cartItems)) {
            header('Location: ' . url('/cart'));
            exit();
        }

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1));
        }

        $this->view('orders/checkout', [
            'pageCss' => 'checkout',
            'user' => $user,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function process(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/checkout'));
            exit();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('/login'));
            exit();
        }

        $userId = (int) $_SESSION['user_id'];

        // Lấy dữ liệu gửi lên từ Form
        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $paymentMethod = trim($_POST['payment_method'] ?? 'COD');

        if (empty($fullname) || empty($phone) || empty($address)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin giao hàng.';
            header('Location: ' . url('/checkout'));
            exit();
        }

        $cartItems = $this->cartModel->getCartByUserId($userId);

        if (empty($cartItems)) {
            header('Location: ' . url('/cart'));
            exit();
        }

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $subtotal = $item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1));
            $totalAmount += $subtotal;
        }

        $shippingAddress = $address . ', ' . $city;

        try {
            $orderData = [
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'recipient_name' => $fullname,
                'recipient_phone' => $phone,
                'shipping_address' => $shippingAddress,
            ];

            $orderId = $this->orderModel->createOrder($orderData, $cartItems, $paymentMethod);

            // Gửi email xác nhận đặt hàng thành công
            $recipientEmail = !empty($email) ? $email : ($_SESSION['user']['email'] ?? '');
            if (empty($recipientEmail)) {
                $userObj = (new \App\Models\User())->findById($userId);
                $recipientEmail = $userObj['email'] ?? '';
            }
            if (!empty($recipientEmail)) {
                try {
                    $mailService = new \App\Services\MailService();
                    $mailService->sendOrderConfirmation($recipientEmail, $orderId, $orderData, $cartItems, $paymentMethod);
                } catch (\Throwable $mailError) {
                    error_log('Order confirmation mail error: ' . $mailError->getMessage());
                }
            }

            //delete session cart
            unset($_SESSION['cart']);
            header('Location: ' . url('/checkout/success?id=' . $orderId));
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = 'Đặt hàng thất bại: ' . $e->getMessage();
            header('Location: ' . url('/checkout'));
            exit();
        }
    }

    public function success(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $orderId = (int) ($_GET['id'] ?? 0);
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if ($orderId <= 0 || $userId <= 0) {
            header('Location: ' . url('/'));
            exit();
        }

        $order = $this->orderModel->getOrderWithDetails($orderId, $userId);

        if (!$order) {
            header('Location: ' . url('/'));
            exit();
        }

        $config = require __DIR__ . '/../../config/config.php';
        $sepayConfig = $config['sepay'] ?? [];

        // Kiểm tra và cập nhật nếu đơn hàng đã quá hạn 15 phút
        $expireInfo = $this->orderModel->checkAndExpireOrder($orderId, 15);
        if ($expireInfo['is_cancelled'] && $order['order_status'] !== 'CANCELLED') {
            $order = $this->orderModel->getOrderWithDetails($orderId, $userId);
        }

        $this->view('orders/success', [
            'pageCss' => 'checkout',
            'order' => $order,
            'sepayConfig' => $sepayConfig,
            'expireInfo' => $expireInfo,
        ]);
    }

    public function cancel(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/profile?tab=orders'));
            exit();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('/login'));
            exit();
        }

        $userId = (int) $_SESSION['user_id'];
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $redirect = trim($_POST['redirect'] ?? '');

        if ($orderId <= 0) {
            $_SESSION['profile_error'] = 'Đơn hàng không hợp lệ.';
            header('Location: ' . url('/profile?tab=orders'));
            exit();
        }

        $success = $this->orderModel->cancelOrder($orderId, $userId, 'Khách hàng hủy đơn');

        if ($success) {
            $_SESSION['profile_success'] = "Đã hủy đơn hàng #DH{$orderId} thành công. Số lượng sản phẩm đã được hoàn trả lại kho.";
        } else {
            $_SESSION['profile_error'] = "Không thể hủy đơn hàng #DH{$orderId} (Đơn đã thanh toán hoặc đang được giao).";
        }

        if ($redirect === 'success') {
            header('Location: ' . url('/checkout/success?id=' . $orderId));
            exit();
        }

        header('Location: ' . url('/profile?tab=orders'));
        exit();
    }

    public function cancelApi(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Không được phép thực hiện hoặc chưa đăng nhập.']);
            return;
        }

        $userId = (int) $_SESSION['user_id'];
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $orderId = (int) ($input['order_id'] ?? 0);

        if ($orderId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Đơn hàng không hợp lệ.']);
            return;
        }

        $success = $this->orderModel->cancelOrder($orderId, $userId, 'Khách hàng hủy đơn qua giao diện');

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => "Đã hủy đơn hàng #DH{$orderId} thành công. Tồn kho đã được hoàn lại.",
                'order_id' => $orderId,
                'order_status' => 'CANCELLED',
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng (Đơn đã thanh toán hoặc đang được giao).'
            ]);
        }
    }

    public function checkStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $orderId = (int) ($_GET['id'] ?? 0);
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if ($orderId <= 0 || $userId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
            return;
        }

        // Tự động kiểm tra hết hạn 15 phút
        $expireInfo = $this->orderModel->checkAndExpireOrder($orderId, 15);

        $order = $this->orderModel->getOrderWithDetails($orderId, $userId);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng.']);
            return;
        }

        $isPaid = strtoupper((string) ($order['payment_status'] ?? '')) === 'PAID';
        $isCancelled = strtoupper((string) ($order['order_status'] ?? '')) === 'CANCELLED';

        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'order_status' => $order['order_status'],
            'payment_status' => $order['payment_status'],
            'is_paid' => $isPaid,
            'is_cancelled' => $isCancelled,
            'is_expired' => $expireInfo['is_expired'] ?? false,
            'remaining_seconds' => $expireInfo['remaining_seconds'] ?? 0,
        ]);
    }
}
