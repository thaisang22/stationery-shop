<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Models\Category;

class LayoutController
{
    public function getHeaderData(): array
    {
        $user = null;
        $cartCount = 0;

        if (isset($_SESSION['user_id'])) {

            $userModel = new User();

            $user = $userModel->findById(
                (int) $_SESSION['user_id']
            );

            $cartModel = new Cart();

            $cartCount = $cartModel->getTotalQuantity(
                (int) $_SESSION['user_id']
            );

        } elseif (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {

            $cartCount = (int) array_sum($_SESSION['cart']);
        }

        $categoryModel = new Category();
        $categories = $categoryModel->getAllActive();

        return [
            'user' => $user,
            'cartCount' => $cartCount,
            'categories' => $categories
        ];
    }
}