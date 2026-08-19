<?php
$adminTitle = 'Báo cáo doanh thu';
$activeMenu = 'sales';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main"><span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span><h1>Báo cáo doanh thu</h1><div class="stats"><div class="stat"><span>DOANH THU THÁNG 8</span><b>126,5tr</b><i class="trend">↑ 18,4%</i></div><div class="stat"><span>GIÁ TRỊ ĐƠN TB</span><b>218.000₫</b><i class="trend">↑ 5,2%</i></div></div><section class="admin-panel" style="margin-top:18px"><h3>Biểu đồ doanh thu theo tháng</h3><div class="bar-chart"><div class="bar" style="height:42%"><small>T1</small></div><div class="bar" style="height:55%"><small>T2</small></div><div class="bar" style="height:48%"><small>T3</small></div><div class="bar" style="height:69%"><small>T4</small></div><div class="bar" style="height:61%"><small>T5</small></div><div class="bar" style="height:79%"><small>T6</small></div><div class="bar" style="height:93%"><small>T7</small></div><div class="bar" style="height:86%"><small>T8</small></div></div></section></main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
