<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Admin\AdminCategoryModel;
use App\Models\Admin\AdminCustomerModel;
use App\Models\Admin\AdminDashboardModel;
use App\Models\Admin\AdminOrderModel;
use App\Models\Admin\AdminProductModel;
use App\Models\Admin\AdminSalesModel;
use Throwable;

class AdminController extends Controller
{
    private AdminDashboardModel $dashboardModel;
    private AdminProductModel $productModel;
    private AdminOrderModel $orderModel;
    private AdminSalesModel $salesModel;
    private AdminCategoryModel $categoryModel;
    private AdminCustomerModel $customerModel;

    public function __construct()
    {
        // 1. Kiểm tra session đăng nhập
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Chặn nếu chưa đăng nhập hoặc không phải ADMIN -> Chuyển hướng sang trang đăng nhập Admin riêng
        if (!isLoggedIn() || !isAdmin()) {
            $config = require dirname(__DIR__, 3) . '/config/config.php';
            $baseUrl = rtrim((string) ($config['app']['base_url'] ?? ''), '/');
            $basePath = rtrim((string) (parse_url($baseUrl, PHP_URL_PATH) ?? ''), '/');

            $requestUri = $_SERVER['REQUEST_URI'] ?? '/admin/dashboard';
            $path = (string) (parse_url($requestUri, PHP_URL_PATH) ?? '/admin/dashboard');

            if ($basePath !== '' && str_starts_with($path, $basePath)) {
                $path = substr($path, strlen($basePath));
            }

            $path = '/' . ltrim($path, '/');
            $query = parse_url($requestUri, PHP_URL_QUERY);
            $target = $path . ($query ? '?' . $query : '');

            if ($target === '/' || $target === '/admin/login' || $target === '') {
                $target = '/admin/dashboard';
            }

            $_SESSION['redirect_after_admin_login'] = $target;
            $_SESSION['admin_login_error'] = 'Vui lòng đăng nhập với tài khoản Quản trị viên để truy cập.';
            header('Location: ' . url('/admin/login'));
            exit();
        }

        // 3. Khởi tạo các Admin Models
        $this->dashboardModel = new AdminDashboardModel();
        $this->productModel = new AdminProductModel();
        $this->orderModel = new AdminOrderModel();
        $this->salesModel = new AdminSalesModel();
        $this->categoryModel = new AdminCategoryModel();
        $this->customerModel = new AdminCustomerModel();
    }

    /**
     * TRANG TỔNG QUAN (DASHBOARD)
     */
    public function dashboard(): void
    {
        $todayRevenue = $this->dashboardModel->getTodayRevenue();
        $totalOrders = $this->dashboardModel->getTotalOrders();
        $totalCustomers = $this->dashboardModel->getTotalCustomers();
        $totalProducts = $this->dashboardModel->getTotalProducts();
        $recentOrders = $this->dashboardModel->getRecentOrders(5);

        $this->view('admin/dashboard', [
            'pageCss' => 'admin',
            'todayRevenue' => $todayRevenue,
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * ==========================================
     * QUẢN LÝ SẢN PHẨM (PRODUCTS CRUD)
     * ==========================================
     */

    // Danh sách sản phẩm (có lọc & phân trang)
    public function products(): void
    {
        $filters = [
            'keyword' => trim((string)($_GET['keyword'] ?? '')),
            'category_id' => !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null,
            'brand_id' => !empty($_GET['brand_id']) ? (int)$_GET['brand_id'] : null,
            'price_range' => trim((string)($_GET['price_range'] ?? '')),
            'stock_status' => trim((string)($_GET['stock_status'] ?? '')),
            'sort' => trim((string)($_GET['sort'] ?? 'newest')),
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;

        $products = $this->productModel->getAll($filters, $page, $limit);
        $total = $this->productModel->count($filters);
        $totalPages = (int) ceil($total / $limit);

        $categories = $this->productModel->getAllCategories();
        $brands = $this->productModel->getAllBrands();

        $success = $_SESSION['admin_success'] ?? null;
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        $this->view('admin/products', [
            'pageCss' => 'admin',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters,
            'page' => $page,
            'total' => $total,
            'totalPages' => $totalPages,
            'success' => $success,
            'error' => $error,
        ]);
    }

    // Hiển thị Form thêm sản phẩm
    public function createProduct(): void
    {
        $categories = $this->productModel->getAllCategories();
        $brands = $this->productModel->getAllBrands();

        $this->view('admin/product_form', [
            'pageCss' => 'admin',
            'categories' => $categories,
            'brands' => $brands,
            'product' => null,
            'actionTitle' => 'Thêm sản phẩm mới',
        ]);
    }

    // Xử lý lưu sản phẩm mới
    public function storeProduct(): void
    {
        $name = trim($_POST['name'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $brandId = !empty($_POST['brand_id']) ? (int) $_POST['brand_id'] : null;
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (int) ($_POST['stock_quantity'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $isBestSeller = isset($_POST['is_best_seller']) ? 1 : 0;

        if ($name === '' || $price <= 0) {
            $_SESSION['admin_error'] = 'Vui lòng nhập tên và giá sản phẩm hợp lệ.';
            header('Location: ' . url('/admin/products/create'));
            exit();
        }

        // Tạo slug đơn giản
        $slug = $this->makeSlug($name);
        if ($this->productModel->slugExists($slug)) {
            $slug .= '-' . time();
        }

        // Xử lý ảnh đại diện
        $imageUrl = $this->handleProductImage();

        try {
            $productData = [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'price' => $price,
                'is_best_seller' => $isBestSeller,
            ];

            $this->productModel->create($productData, $stock, $imageUrl);

            $_SESSION['admin_success'] = "Đã thêm sản phẩm '{$name}' thành công!";
            header('Location: ' . url('/admin/products'));
            exit();
        } catch (Throwable $e) {
            $_SESSION['admin_error'] = 'Lỗi thêm sản phẩm: ' . $e->getMessage();
            header('Location: ' . url('/admin/products/create'));
            exit();
        }
    }

    // Hiển thị Form sửa sản phẩm
    public function editProduct(string $id): void
    {
        $productId = (int) $id;
        $product = $this->productModel->findById($productId);

        if (!$product) {
            $_SESSION['admin_error'] = 'Không tìm thấy sản phẩm.';
            header('Location: ' . url('/admin/products'));
            exit();
        }

        $categories = $this->productModel->getAllCategories();
        $brands = $this->productModel->getAllBrands();

        $this->view('admin/product_form', [
            'pageCss' => 'admin',
            'categories' => $categories,
            'brands' => $brands,
            'product' => $product,
            'actionTitle' => 'Chỉnh sửa sản phẩm #' . $productId,
        ]);
    }

    // Xử lý cập nhật sản phẩm
    public function updateProduct(string $id): void
    {
        $productId = (int) $id;
        $name = trim($_POST['name'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $brandId = !empty($_POST['brand_id']) ? (int) $_POST['brand_id'] : null;
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (int) ($_POST['stock_quantity'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $isBestSeller = isset($_POST['is_best_seller']) ? 1 : 0;

        if ($name === '' || $price <= 0) {
            $_SESSION['admin_error'] = 'Vui lòng nhập tên và giá sản phẩm hợp lệ.';
            header('Location: ' . url('/admin/products/' . $productId . '/edit'));
            exit();
        }

        // Cập nhật ảnh nếu có tải lên mới
        $uploadedImage = $this->handleProductImage();

        try {
            $productData = [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'is_best_seller' => $isBestSeller,
            ];

            $this->productModel->update($productId, $productData, $stock, $uploadedImage);

            $_SESSION['admin_success'] = "Cập nhật sản phẩm '{$name}' thành công!";
            header('Location: ' . url('/admin/products'));
            exit();
        } catch (Throwable $e) {
            $_SESSION['admin_error'] = 'Lỗi cập nhật: ' . $e->getMessage();
            header('Location: ' . url('/admin/products/' . $productId . '/edit'));
            exit();
        }
    }

    // Xóa sản phẩm (Ẩn sản phẩm)
    public function deleteProduct(string $id): void
    {
        $productId = (int) $id;
        $this->productModel->delete($productId);

        $_SESSION['admin_success'] = "Đã xóa sản phẩm #{$productId} thành công.";
        header('Location: ' . url('/admin/products'));
        exit();
    }

    /**
     * ==========================================
     * QUẢN LÝ ĐƠN HÀNG (ORDERS MANAGEMENT)
     * ==========================================
     */

    // Danh sách đơn hàng
    public function orders(): void
    {
        $orders = $this->orderModel->getAll();

        $success = $_SESSION['admin_success'] ?? null;
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        $this->view('admin/orders', [
            'pageCss' => 'admin',
            'orders' => $orders,
            'success' => $success,
            'error' => $error,
        ]);
    }

    // Xem chi tiết đơn hàng
    public function orderDetail(string $id): void
    {
        $orderId = (int) $id;
        $order = $this->orderModel->findById($orderId);

        if (!$order) {
            $_SESSION['admin_error'] = 'Không tìm thấy đơn hàng.';
            header('Location: ' . url('/admin/orders'));
            exit();
        }

        $items = $this->orderModel->getItems($orderId);

        $success = $_SESSION['admin_success'] ?? null;
        unset($_SESSION['admin_success']);

        $this->view('admin/order_detail', [
            'pageCss' => 'admin',
            'order' => $order,
            'items' => $items,
            'success' => $success,
        ]);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus(string $id): void
    {
        $orderId = (int) $id;
        $orderStatus = trim($_POST['order_status'] ?? 'PENDING');
        $paymentStatus = trim($_POST['payment_status'] ?? 'UNPAID');

        $this->orderModel->updateStatus($orderId, $orderStatus, $paymentStatus);

        $_SESSION['admin_success'] = "Đã cập nhật trạng thái đơn hàng #DH{$orderId} thành công!";
        header('Location: ' . url('/admin/orders/' . $orderId));
        exit();
    }

    /**
     * ==========================================
     * THỐNG KÊ ĐƠN HÀNG & DOANH THU (SALES STATS)
     * ==========================================
     */
    public function sales(): void
    {
        $summary = $this->salesModel->getSummary();
        $monthlyStats = $this->salesModel->getMonthlyStats();
        $quarterlyStats = $this->salesModel->getQuarterlyStats();
        $yearlyStats = $this->salesModel->getYearlyStats();
        $paymentStats = $this->salesModel->getPaymentStats();

        $this->view('admin/sales', [
            'pageCss' => 'admin',
            'summary' => $summary,
            'monthlyStats' => $monthlyStats,
            'quarterlyStats' => $quarterlyStats,
            'yearlyStats' => $yearlyStats,
            'paymentStats' => $paymentStats,
        ]);
    }

    /**
     * Quản lý Danh mục (Hiển thị DB thực tế)
     */
    public function categories(): void
    {
        $categories = $this->categoryModel->getAllWithProductCount();

        $this->view('admin/categories', [
            'pageCss' => 'admin',
            'categories' => $categories,
        ]);
    }

    /**
     * Quản lý Khách hàng (Hiển thị DB thực tế)
     */
    public function customers(): void
    {
        $customers = $this->customerModel->getAllWithStats();

        $this->view('admin/customers', [
            'pageCss' => 'admin',
            'customers' => $customers,
        ]);
    }

    /**
     * ==========================================
     * CÁC HÀM TIỆN ÍCH HỖ TRỢ ĐƠN GIẢN
     * ==========================================
     */
    private function makeSlug(string $string): string
    {
        $slug = strtolower(trim((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string)));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        return trim($slug, '-') ?: 'san-pham';
    }

    private function handleProductImage(): string
    {
        // 1. Kiểm tra nếu có upload file
        if (isset($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $allowed, true)) {
                $uploadDir = dirname(__DIR__, 3) . '/public/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newFileName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file((string) $file['tmp_name'], $uploadDir . $newFileName)) {
                    return '/public/images/products/' . $newFileName;
                }
            }
        }

        // 2. Nếu có nhập link ảnh trực tiếp
        $customUrl = trim($_POST['custom_image_url'] ?? '');
        if ($customUrl !== '') {
            return $customUrl;
        }

        return '';
    }
}
