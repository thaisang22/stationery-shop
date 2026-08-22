<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class HomeController extends Controller
{

    public function index(): void
    {
        $productModel = new Product();
        $productsBestSeller = $productModel->bestSeller();
        $categories = $productModel->getCategorys();

        $this->view('home/index', [
            'pageCss'    => 'home',
            'bestSeller' => $productsBestSeller,
            'categories' => $categories,
        ]);
    }
}
