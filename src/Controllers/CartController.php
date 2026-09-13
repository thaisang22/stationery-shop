<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;

class CartController extends Controller
{
    private Cart $cartModel;

    public function __construct()
    {
        $this->cartModel = new Cart();
    }

    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/'));
            exit();
        }

        $variantId = (int) ($_POST['variant_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);

       
        if ($variantId <= 0 || $quantity <= 0) {
            $_SESSION['error'] = 'Thông tin sản phẩm hoặc số lượng không hợp lệ.';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('/')));
            exit();
        }

       
        if (isset($_SESSION['user_id'])) {

            $userId = (int) $_SESSION['user_id'];
            $success = $this->cartModel->addToCart($userId, $variantId, $quantity);
        } else {
        
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }


            if (isset($_SESSION['cart'][$variantId])) {
                $_SESSION['cart'][$variantId] += $quantity;
            } else {
                $_SESSION['cart'][$variantId] = $quantity;
            }

            $success = true;
        }

        
        if ($success) {
            $_SESSION['flash_message'] = 'Đã thêm sản phẩm vào giỏ hàng!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại.';
        }

        header('Location: ' . url('/cart'));
        exit();
    }

    public function index(): void
    {
        $cartItems = [];
        $totalAmount = 0;

        if (isset($_SESSION['user_id'])) {
            $userId = (int) $_SESSION['user_id'];
            $cartItems = $this->cartModel->getCartByUserId($userId);
        } else {
            $sessionCart = $_SESSION['cart'] ?? [];
            if (!empty($sessionCart)) {
                $variantIds = array_keys($sessionCart);
                $variantsInfo = $this->cartModel->getVariantsByIds($variantIds);

                foreach ($variantsInfo as $item) {
                    $qty = $sessionCart[$item['variant_id']];
                    $subtotal = $item['price'] * $qty;

                    $cartItems[] = array_merge($item, [
                        'quantity' => $qty,
                        'subtotal' => $subtotal,
                    ]);
                }
            }
        }

        foreach ($cartItems as $item) {
            $totalAmount += $item['subtotal'];
        }

        $this->view('cart/index', [
            'pageCss' => 'checkout',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('/cart'));
            exit();
        }

        $variantId = (int) ($_POST['variant_id'] ?? 0);
        $action = $_POST['action'] ?? null;
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if ($variantId > 0) {
            if (isset($_SESSION['user_id'])) {
                // Xử lý khi đã đăng nhập (Cập nhật trong Database)
                $userId = (int) $_SESSION['user_id'];

                if ($action === 'increase') {
                    $this->cartModel->incrementQuantity($userId, $variantId, 1);
                } elseif ($action === 'decrease') {
                    $this->cartModel->decrementQuantity($userId, $variantId, 1);
                } else {
                    $this->cartModel->updateQuantity($userId, $variantId, max(1, $quantity));
                }
            } else {
                // Xử lý khi chưa đăng nhập (Cập nhật trong SESSION)
                if (isset($_SESSION['cart'][$variantId])) {
                    if ($action === 'increase') {
                        $_SESSION['cart'][$variantId] += 1;
                    } elseif ($action === 'decrease') {
                        $_SESSION['cart'][$variantId] = max(1, $_SESSION['cart'][$variantId] - 1);
                    } else {
                        $_SESSION['cart'][$variantId] = max(1, $quantity);
                    }
                }
            }
        }

        header('Location: ' . url('/cart'));
        exit();
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng (POST /cart/remove)
     */
    public function remove(): void
    {
        $variantId = (int) ($_POST['variant_id'] ?? 0);

        if ($variantId > 0) {
            if (isset($_SESSION['user_id'])) {
                $this->cartModel->removeItem((int) $_SESSION['user_id'], $variantId);
            } else {
                unset($_SESSION['cart'][$variantId]);
            }
            $_SESSION['flash_message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
        }

        header('Location: ' . url('/cart'));
        exit();
    }
}