<?php
$adminTitle = 'Danh mục';
$activeMenu = 'categories';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main"><span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span><h1>Danh mục</h1><section class="admin-panel"><div class="title-row"><h2>Danh sách quản lý</h2><button class="btn btn-accent">+ Thêm mới</button></div><div class="table-wrap"><table class="table"><thead><tr><th>Tên danh mục</th><th>Sản phẩm</th><th>Hiển thị</th><th>Thao tác</th></tr></thead><tbody><tr><td>Bút viết</td><td>128</td><td><span class="status">Đang hiển thị</span></td><td><button class="text-link">Sửa</button></td></tr><tr><td>Vở &amp; sổ tay</td><td>96</td><td><span class="status">Đang hiển thị</span></td><td><button class="text-link">Sửa</button></td></tr></tbody></table></div></section></main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
