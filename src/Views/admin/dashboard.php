<?php
$adminTitle = 'Tổng quan';
$activeMenu = 'dashboard';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>

<main class="admin-main">
    <span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
    <h1>Tổng quan</h1>

    <div class="stats">
        <div class="stat">
            <span>DOANH THU HÔM NAY</span>
            <b><?= number_format((float)($todayRevenue ?? 0), 0, ',', '.') ?>₫</b>
            <i class="trend">Đơn đã thanh toán</i>
        </div>
        <div class="stat">
            <span>TỔNG ĐƠN HÀNG</span>
            <b><?= number_format((int)($totalOrders ?? 0)) ?> đơn</b>
            <i class="trend">Toàn hệ thống</i>
        </div>
        <div class="stat">
            <span>KHÁCH HÀNG</span>
            <b><?= number_format((int)($totalCustomers ?? 0)) ?> người</b>
            <i class="trend">Tài khoản khách hàng</i>
        </div>
        <div class="stat">
            <span>SẢN PHẨM HIỆN CÓ</span>
            <b><?= number_format((int)($totalProducts ?? 0)) ?> món</b>
            <i>Đang kinh doanh</i>
        </div>
    </div>

    <section class="admin-panel" style="margin-top: 24px;">
        <div class="title-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="margin: 0; font-size: 16px;">Đơn hàng mới nhất</h3>
            <a href="<?= url('/admin/orders') ?>" style="font-size: 13px; color: #2e5b38; text-decoration: none; font-weight: 600;">Xem tất cả</a>
        </div>
        
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentOrders)): ?>
                        <?php foreach ($recentOrders as $order): ?>
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
                                    <strong><?= htmlspecialchars($order['recipient_name']) ?></strong>
                                    <div style="font-size: 12px; color: #777;"><?= htmlspecialchars($order['recipient_phone']) ?></div>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime((string)$order['created_at'])) ?></td>
                                <td><strong style="color: #276238;"><?= number_format((float)$order['total_amount'], 0, ',', '.') ?>₫</strong></td>
                                <td>
                                    <span class="status" style="font-size: 11px;">
                                        <?= htmlspecialchars($order['order_status']) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= url('/admin/orders/' . $order['id']) ?>" style="font-size: 13px; color: #2e5b38; text-decoration: none; font-weight: 500;">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="admin-empty-cell">Chưa có đơn hàng nào gần đây.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>