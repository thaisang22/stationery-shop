<?php
$adminTitle = 'Khách hàng';
$activeMenu = 'customers';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main"><span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span><h1>Khách hàng</h1><section class="admin-panel"><div class="title-row"><h2>Danh sách quản lý</h2><button class="btn btn-accent">+ Thêm mới</button></div><div class="table-wrap"><table class="table"><thead><tr><th>Khách hàng</th><th>Email</th><th>Đơn hàng</th><th>Tổng chi tiêu</th><th>Thao tác</th></tr></thead><tbody><tr><td>Nguyễn Minh Anh</td><td>minhanh@gmail.com</td><td>8</td><td>1.285.000₫</td><td><button class="text-link">Sửa</button></td></tr><tr><td>Trần Quốc Bảo</td><td>quocbao@gmail.com</td><td>5</td><td>890.000₫</td><td><button class="text-link">Sửa</button></td></tr></tbody></table></div></section></main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
