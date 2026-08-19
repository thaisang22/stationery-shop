<?php
$adminTitle = 'Bài viết';
$activeMenu = 'posts';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main"><span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span><h1>Bài viết</h1><section class="admin-panel"><div class="title-row"><h2>Danh sách quản lý</h2><button class="btn btn-accent">+ Thêm mới</button></div><div class="table-wrap"><table class="table"><thead><tr><th>Tiêu đề</th><th>Ngày đăng</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody><tr><td>5 cách ghi chú giúp bạn học nhanh hơn</td><td>12.08.2026</td><td>Đã xuất bản</td><td><button class="text-link">Sửa</button></td></tr><tr><td>Chọn sổ tay nào cho năm học mới?</td><td>05.08.2026</td><td>Đã xuất bản</td><td><button class="text-link">Sửa</button></td></tr></tbody></table></div></section></main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
