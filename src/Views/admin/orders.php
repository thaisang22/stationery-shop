<?php
$adminTitle = 'Quản lý đơn hàng';
$activeMenu = 'orders';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';

// Helper dịch trạng thái sang tiếng Việt
function getOrderStatusBadge(string $status): string {
    return match (strtoupper($status)) {
        'PENDING' => '<span style="background:#fef3c7; color:#92400e; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;">Chờ xử lý</span>',
        'CONFIRMED' => '<span style="background:#e0e7ff; color:#3730a3; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;">Đã xác nhận</span>',
        'PROCESSING' => '<span style="background:#f3e8ff; color:#6b21a8; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;">Đang đóng gói</span>',
        'SHIPPING' => '<span style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;">Đang giao hàng</span>',
        'DELIVERED' => '<span style="background:#dcfce7; color:#15803d; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;">Đã giao thành công</span>',
        'CANCELLED' => '<span style="background:#fee2e2; color:#b91c1c; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;">Đã hủy đơn</span>',
        default => htmlspecialchars($status),
    };
}

function getPaymentBadge(string $pStatus): string {
    return match (strtoupper($pStatus)) {
        'PAID', 'SUCCESS' => '<span style="color:#16a34a; font-weight:600; font-size:12px;">Đã thanh toán</span>',
        default => '<span style="color:#d97706; font-weight:600; font-size:12px;">Chưa thanh toán</span>',
    };
}
?>

<main class="admin-main">
    <div style="margin-bottom: 20px;">
        <span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
        <h1 style="margin: 4px 0 0;">Quản lý & Xử lý Đơn hàng</h1>
    </div>

    <?php if (!empty($success)): ?>
        <div class="admin-notice" style="background:#e7f4e8; color:#276238; border-left: 4px solid #276238; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <section class="admin-panel">
        <div class="title-row" style="margin-bottom: 16px;">
            <h2 style="font-size: 16px; margin: 0;">Danh sách tất cả đơn hàng (<?= count($orders ?? []) ?>)</h2>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Người nhận</th>
                        <th>Số điện thoại</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái đơn</th>
                        <th style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <?php $isNew = (time() - strtotime((string)$order['created_at'])) < 86400; ?>
                            <tr <?= $isNew ? 'style="background: #f4faf5;"' : '' ?>>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <?php if ($isNew): ?>
                                            <span style="display: inline-block; width: 8px; height: 8px; background: #16a34a; border-radius: 50%; margin-right: 6px; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.25); flex-shrink: 0;" title="Đơn hàng mới trong 24h"></span>
                                        <?php endif; ?>
                                        <strong style="color: #2e5b38;">#DH<?= (int)$order['id'] ?></strong>
                                        <?php if ($isNew): ?>
                                            <span style="display: inline-block; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-size: 10px; font-weight: 700; padding: 1px 5px; border-radius: 4px; margin-left: 6px; letter-spacing: 0.5px;">NEW</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div><strong><?= htmlspecialchars($order['recipient_name']) ?></strong></div>
                                    <small style="color: #666;"><?= htmlspecialchars($order['customer_name'] ?? '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($order['recipient_phone']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime((string)$order['created_at'])) ?></td>
                                <td>
                                    <strong style="color: #276238;"><?= number_format((float)$order['total_amount'], 0, ',', '.') ?>₫</strong>
                                    <div style="font-size: 11px; color: #777;">
                                        <?= htmlspecialchars($order['payment_method'] ?? 'COD') ?>
                                    </div>
                                </td>
                                <td>
                                    <?= getPaymentBadge($order['payment_status'] ?? 'UNPAID') ?>
                                </td>
                                <td>
                                    <?= getOrderStatusBadge($order['order_status']) ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= url('/admin/orders/' . $order['id']) ?>" 
                                       style="display: inline-block; padding: 6px 14px; background: #2e5b38; color: #fff; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 500;">
                                        Xem & Xử lý
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="admin-empty-cell">Chưa có đơn hàng nào trong hệ thống.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
