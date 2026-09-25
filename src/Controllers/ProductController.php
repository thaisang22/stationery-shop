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
        $categories = $productModel->getCategorys();
        $brands = $productModel->getBrands();
        $filters = [
            'category_slug' => $_GET['category_slug'] ?? null,
            'brand_slug' => $_GET['brand'] ?? null,
            'price_range' => $_GET['price_range'] ?? null,
            'keyword' => $_GET['keyword'] ?? null,
            'sort' => $_GET['sort'] ?? 'newest',
        ];

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $limit = 12;

        $products = $productModel->getPaginated(
            $filters,
            $page,
            $limit
        );

        $total = $productModel->count($filters);

        $totalPages = (int) ceil(
            $total / $limit
        );

        $this->view('products/index', [
            'pageCss' => 'catalog',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters,
            'page' => $page,
            'total' => $total,
            'totalPages' => $totalPages,
        ]);
    }

    public function detail(string $slug): void
    {
        $productModel = new Product();

        $productDetail = $productModel->findBySlug($slug);
        $variants = $productModel->getVariantsByProductId($productDetail['id']);
        

        $this->view('products/detail', [
            'pageCss' => 'catalog',
            'productDetail' => $productDetail,
            'variants' => $variants,
        ]);
    }


}
