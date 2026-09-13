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
            'pageCss'     => 'checkout',
            'user'        => $user,
            'cartItems'   => $cartItems,
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
        $fullname      = trim($_POST['fullname'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $address       = trim($_POST['address'] ?? '');
        $city          = trim($_POST['city'] ?? '');
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
                'user_id'          => $userId,
                'total_amount'     => $totalAmount,
                'recipient_name'   => $fullname,
                'recipient_phone'  => $phone,
                'shipping_address' => $shippingAddress,
            ];

            $orderId = $this->orderModel->createOrder($orderData, $cartItems, $paymentMethod);

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
        $userId  = (int) ($_SESSION['user_id'] ?? 0);

        if ($orderId <= 0 || $userId <= 0) {
            header('Location: ' . url('/'));
            exit();
        }

        $order = $this->orderModel->getOrderWithDetails($orderId, $userId);

        if (!$order) {
            header('Location: ' . url('/'));
            exit();
        }

        $this->view('orders/success', [
            'pageCss' => 'checkout',
            'order'   => $order,
        ]);
    }
}