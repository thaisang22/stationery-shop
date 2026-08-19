<?php
$adminTitle = 'Đơn hàng';
$activeMenu = 'orders';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main"><span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span><h1>Đơn hàng</h1><section class="admin-panel"><div class="title-row"><h2>Danh sách quản lý</h2><button class="btn btn-accent">+ Thêm mới</button></div><div class="table-wrap"><table class="table"><thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Ngày</th><th>Tổng tiền</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody><tr><td>#MN24081</td><td>Nguyễn Minh Anh</td><td>10/08/2026</td><td>166.500₫</td><td><span class="status">Đã giao</span></td><td><button class="text-link">Sửa</button></td></tr><tr><td>#MN24080</td><td>Trần Quốc Bảo</td><td>10/08/2026</td><td>295.000₫</td><td><span class="status">Đang giao</span></td><td><button class="text-link">Sửa</button></td></tr></tbody></table></div></section></main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
