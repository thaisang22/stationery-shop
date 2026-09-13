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
        $productsNew = $productModel->productsNew();
        $this->view('home/index', [
            'pageCss'    => 'home',
            'productsBestSeller' => $productsBestSeller,
            'categories' => $categories,
            'productsNew' => $productsNew,
        ]);
    }
}
