<?php
$adminTitle = 'Tổng quan';
$activeMenu = 'dashboard';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main"><span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
    <h1>Tổng quan</h1>
    <div class="stats">
        <div class="stat"><span>DOANH THU HÔM NAY</span><b>4.860.000₫</b><i class="trend">↑ 12,5% so với hôm qua</i>
        </div>
        <div class="stat"><span>ĐƠN HÀNG MỚI</span><b>36</b><i class="trend">↑ 8,2% so với hôm qua</i></div>
        <div class="stat"><span>KHÁCH HÀNG</span><b>1.284</b><i class="trend">↑ 16 khách mới</i></div>
        <div class="stat"><span>SẢN PHẨM SẮP HẾT</span><b>8</b><i>Cần nhập thêm hàng</i></div>
    </div>
    <div class="admin-grid">
        <section class="admin-panel">
            <h3>Doanh thu 7 ngày qua</h3>
            <div class="bar-chart">
                <div class="bar" style="height:65%"><small>T2</small></div>
                <div class="bar" style="height:48%"><small>T3</small></div>
                <div class="bar" style="height:77%"><small>T4</small></div>
                <div class="bar" style="height:58%"><small>T5</small></div>
                <div class="bar" style="height:92%"><small>T6</small></div>
                <div class="bar" style="height:73%"><small>T7</small></div>
                <div class="bar" style="height:85%"><small>CN</small></div>
            </div>
        </section>
    </div>
</main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>