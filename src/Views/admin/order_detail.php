<?php
$adminTitle = 'Chi tiết đơn hàng #DH' . (int)$order['id'];
$activeMenu = 'orders';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>

<main class="admin-main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <span class="eyebrow">QUẢN TRỊ ĐƠN HÀNG</span>
            <?php $isNew = (time() - strtotime((string)$order['created_at'])) < 86400; ?>
            <h1 style="margin: 4px 0 0; display: flex; align-items: center; gap: 8px;">
                Chi tiết đơn hàng #DH<?= (int)$order['id'] ?>
                <?php if ($isNew): ?>
                    <span style="display: inline-flex; align-items: center; gap: 5px; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 4px;">
                        <span style="display: inline-block; width: 6px; height: 6px; background: #16a34a; border-radius: 50%;"></span>
                        NEW
                    </span>
                <?php endif; ?>
            </h1>
            <small style="color: #666;">Ngày đặt: <?= date('d/m/Y H:i:s', strtotime((string)$order['created_at'])) ?></small>
        </div>
        <a href="<?= url('/admin/orders') ?>" style="padding: 8px 16px; background: #e2e8df; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">
            Quay lại danh sách
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="admin-notice" style="background:#e7f4e8; color:#276238; border-left: 4px solid #276238; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        <!-- Cột trái: Thông tin người nhận & Danh sách sản phẩm -->
        <div>
            <!-- Box 1: Thông tin giao hàng -->
            <section class="admin-panel" style="margin-bottom: 20px;">
                <h3 style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    Thông tin người nhận & Địa chỉ giao hàng
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 14px; line-height: 1.6;">
                    <div>
                        <p style="margin: 4px 0;"><strong>Họ và tên:</strong> <?= htmlspecialchars($order['recipient_name']) ?></p>
                        <p style="margin: 4px 0;"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['recipient_phone']) ?></p>
                        <p style="margin: 4px 0;"><strong>Tài khoản đặt:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'Khách hàng') ?> (<?= htmlspecialchars($order['customer_email'] ?? '') ?>)</p>
                    </div>
                    <div>
                        <p style="margin: 4px 0;"><strong>Địa chỉ giao:</strong></p>
                        <p style="margin: 4px 0; background: #f9f9f9; padding: 8px 10px; border-radius: 4px; border: 1px solid #eee;">
                            <?= htmlspecialchars($order['shipping_address']) ?>
                        </p>
                    </div>
                </div>
            </section>

            <!-- Box 2: Danh sách sản phẩm trong đơn -->
            <section class="admin-panel">
                <h3 style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    Các sản phẩm đã đặt (<?= count($items) ?> món)
                </h3>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th style="text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td style="width: 50px;">
                                        <?php 
                                            $img = $item['image_url'] ?? '';
                                            if (empty($img)) {
                                                $img = url('/public/images/no-image.png');
                                            } elseif (!str_starts_with($img, 'http')) {
                                                $img = url($img);
                                            }
                                        ?>
                                        <img src="<?= htmlspecialchars($img) ?>" alt="" style="width: 44px; height: 44px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                        <?php if (!empty($item['variant_name']) && $item['variant_name'] !== 'Mặc định'): ?>
                                            <div style="font-size: 12px; color: #666;">Phân loại: <?= htmlspecialchars($item['variant_name']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format((float)$item['unit_price'], 0, ',', '.') ?>₫</td>
                                    <td>x<?= (int)$item['quantity'] ?></td>
                                    <td style="text-align: right;">
                                        <strong style="color: #2e5b38;"><?= number_format((float)$item['sub_total'], 0, ',', '.') ?>₫</strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: bold; font-size: 15px; padding-top: 16px;">
                                    TỔNG TIỀN ĐƠN HÀNG:
                                </td>
                                <td style="text-align: right; font-weight: bold; font-size: 18px; color: #276238; padding-top: 16px;">
                                    <?= number_format((float)$order['total_amount'], 0, ',', '.') ?>₫
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        </div>

        <!-- Cột phải: Form XỬ LÝ ĐƠN HÀNG -->
        <div>
            <section class="admin-panel" style="border: 2px solid #2e5b38; background: #fff;">
                <h3 style="margin-top: 0; font-size: 16px; color: #2e5b38; border-bottom: 2px solid #2e5b38; padding-bottom: 8px;">
                    Xử lý đơn hàng
                </h3>

                <form action="<?= url('/admin/orders/' . (int)$order['id'] . '/status') ?>" method="POST">
                    <!-- Trạng thái đơn -->
                    <div style="margin-bottom: 16px;">
                        <label for="order_status" style="display: block; font-weight: 600; margin-bottom: 6px;">
                            Trạng thái đơn hàng:
                        </label>
                        <select name="order_status" id="order_status" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-weight: 500;">
                            <option value="PENDING" <?= $order['order_status'] === 'PENDING' ? 'selected' : '' ?>>Chờ xử lý (Pending)</option>
                            <option value="CONFIRMED" <?= $order['order_status'] === 'CONFIRMED' ? 'selected' : '' ?>>Đã xác nhận (Confirmed)</option>
                            <option value="PROCESSING" <?= $order['order_status'] === 'PROCESSING' ? 'selected' : '' ?>>Đang đóng gói (Processing)</option>
                            <option value="SHIPPING" <?= $order['order_status'] === 'SHIPPING' ? 'selected' : '' ?>>Đang giao hàng (Shipping)</option>
                            <option value="DELIVERED" <?= $order['order_status'] === 'DELIVERED' ? 'selected' : '' ?>>Đã giao thành công (Delivered)</option>
                            <option value="CANCELLED" <?= $order['order_status'] === 'CANCELLED' ? 'selected' : '' ?>>Hủy đơn hàng (Cancelled)</option>
                        </select>
                    </div>

                    <!-- Trạng thái thanh toán -->
                    <div style="margin-bottom: 16px;">
                        <label for="payment_status" style="display: block; font-weight: 600; margin-bottom: 6px;">
                            Trạng thái thanh toán:
                        </label>
                        <select name="payment_status" id="payment_status" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-weight: 500;">
                            <option value="UNPAID" <?= $order['payment_status'] === 'UNPAID' ? 'selected' : '' ?>>Chưa thanh toán (Unpaid)</option>
                            <option value="PAID" <?= $order['payment_status'] === 'PAID' ? 'selected' : '' ?>>Đã thanh toán (Paid)</option>
                            <option value="REFUNDED" <?= $order['payment_status'] === 'REFUNDED' ? 'selected' : '' ?>>Đã hoàn tiền (Refunded)</option>
                        </select>
                    </div>

                    <div style="background: #f8faf7; padding: 12px; border-radius: 5px; margin-bottom: 18px; font-size: 13px;">
                        <p style="margin: 3px 0;"><strong>Phương thức:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'COD') ?></p>
                        <?php if (!empty($order['transaction_id'])): ?>
                            <p style="margin: 3px 0;"><strong>Mã GD SePay:</strong> <?= htmlspecialchars($order['transaction_id']) ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-accent" style="width: 100%; padding: 12px; font-size: 14px; font-weight: bold; border-radius: 5px; cursor: pointer;">
                        CẬP NHẬT TRẠNG THÁI
                    </button>
                </form>
            </section>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
