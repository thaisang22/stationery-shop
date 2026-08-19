<?php
/** @var string $activeMenu Key của menu đang được chọn. */
$activeMenu = $activeMenu ?? 'dashboard';
$adminMenus = [
  'dashboard' => ['◫ Tổng quan', 'dashboard'],
  'products' => ['□ Sản phẩm', 'products'],
  'categories' => ['▤ Danh mục', 'categories'],
  'orders' => ['▱ Đơn hàng', 'orders'],
  'customers' => ['♙ Khách hàng', 'customers'],
  'posts' => ['▧ Bài viết', 'posts'],
  'sales' => ['▥ Báo cáo', 'sales'],
];
?>
<nav class="admin-nav">
  <?php foreach ($adminMenus as $key => [$label, $url]): ?>
    <a class="<?= $key === $activeMenu ? 'active' : '' ?>" href="<?= url('/admin/' . $url) ?>"><?= $label ?></a>
  <?php endforeach; ?>
</nav>
