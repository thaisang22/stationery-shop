<?php
$adminTitle = 'Khách hàng';
$activeMenu = 'customers';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>

<main class="admin-main">
    <span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
    <h1>Khách hàng</h1>

    <section class="admin-panel">
        <div class="title-row" style="margin-bottom: 16px;">
            <h2 style="font-size: 16px; margin: 0;">Danh sách khách hàng (<?= count($customers ?? []) ?>)</h2>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Số đơn hàng</th>
                        <th>Tổng chi tiêu</th>
                        <th>Ngày đăng ký</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td>#<?= (int)$c['id'] ?></td>
                                <td><strong><?= htmlspecialchars($c['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['phone_number'] ?? '—') ?></td>
                                <td><?= (int)($c['order_count'] ?? 0) ?> đơn</td>
                                <td><strong style="color: #276238;"><?= number_format((float)($c['total_spent'] ?? 0), 0, ',', '.') ?>₫</strong></td>
                                <td><?= date('d/m/Y', strtotime((string)$c['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="admin-empty-cell">Chưa có khách hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
