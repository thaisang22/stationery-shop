<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Post;
class HomeController extends Controller
{

    public function index(): void
    {

        $productModel = new Product();
        $productsBestSeller = $productModel->bestSeller();
        $categories = $productModel->getCategorys();
        $productsNew = $productModel->productsNew();
        $postModels = new Post();
        $lastPosts = $postModels->getLatestPosts();
        $this->view('home/index', [
            'pageCss' => 'home',
            'productsBestSeller' => $productsBestSeller,
            'categories' => $categories,
            'productsNew' => $productsNew,
            'posts' => $lastPosts
        ]);
    }
}
