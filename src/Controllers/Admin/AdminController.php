<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Post;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        $this->view('admin/dashboard');
    }
    public function orders(): void
    {
        $this->view('admin/orders');
    }
    public function products(): void
    {
        $this->view('/admin/products');
    }
    public function customers(): void
    {
        $this->view('admin/customers');
    }
    public function categories(): void
    {
        $this->view('admin/categories');
    }
    public function sales(): void
    {
        $this->view('admin/sales');
    }
}
