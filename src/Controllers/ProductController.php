<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
class ProductController extends Controller
{
    public function index(): void
    {
        $productModel = new Product();
        $products = $productModel->getAll();

        $this->view('products/index',
        [
            'products' => $products, // call for view
        ]
        );
    }

    public function detail(): void
    {
        $this->view('products/detail');
    }

    

}
