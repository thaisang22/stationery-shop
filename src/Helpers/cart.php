<?php

declare(strict_types=1);

use App\Models\Cart;

function getCartBadgeCount(): int
{
    if (isset($_SESSION['user_id'])) {
        $cartModel = new Cart();
        return $cartModel->getTotalQuantity(
            (int) $_SESSION['user_id']
        );
    }
    if (
        !empty($_SESSION['cart']) &&
        is_array($_SESSION['cart'])
    ) {
        return (int) array_sum($_SESSION['cart']);
    }

    return 0;
}