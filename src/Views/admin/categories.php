<?php
$adminTitle = 'Danh mục';
$activeMenu = 'categories';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>

<main class="admin-main">
    <span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
    <h1>Danh mục sản phẩm</h1>

    <section class="admin-panel">
        <div class="title-row" style="margin-bottom: 16px;">
            <h2 style="font-size: 16px; margin: 0;">Danh sách danh mục (<?= count($categories ?? []) ?>)</h2>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Số lượng sản phẩm</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>#<?= (int)$cat['id'] ?></td>
                                <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                                <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                                <td><?= (int)($cat['product_count'] ?? 0) ?> sản phẩm</td>
                                <td><span class="status">Đang hiển thị</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="admin-empty-cell">Chưa có danh mục nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
