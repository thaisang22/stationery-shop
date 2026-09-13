<?php 
/** 
 * @var array $order; 
 */

require __DIR__ . '/../layouts/header.php'; ?>

<main class="page container">
    <div style="max-width: 650px; margin: 40px auto; text-align: center; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h1 style="color: #2e7d32;">🎉 Đặt hàng thành công!</h1>
        <p>Cảm ơn bạn đã mua hàng. Mã đơn hàng của bạn là <b>#<?= (int) $order['id'] ?></b>.</p>
        
        <div style="text-align: left; margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
            <h3>Thông tin đơn hàng</h3>
            <p><b>Người nhận:</b> <?= htmlspecialchars($order['recipient_name']) ?> (<?= htmlspecialchars($order['recipient_phone']) ?>)</p>
            <p><b>Địa chỉ nhận hàng:</b> <?= htmlspecialchars($order['shipping_address']) ?></p>
            <p><b>Phương thức thanh toán:</b> <?= htmlspecialchars($order['payment_method']) ?></p>
            <p><b>Trạng thái đơn hàng:</b> <span style="color: #ed6c02; font-weight: bold;"><?= htmlspecialchars($order['order_status']) ?></span></p>

            <h4 style="margin-top: 20px;">Sản phẩm đã đặt:</h4>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($order['items'] as $item): ?>
                    <li style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee;">
                        <span>
                            <?= htmlspecialchars($item['product_name']) ?> 
                            <small style="color: #777;">(<?= htmlspecialchars($item['variant_name']) ?>) x <?= (int) $item['quantity'] ?></small>
                        </span>
                        <b><?= number_format((float) $item['sub_total'], 0, ',', '.') ?>₫</b>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div style="display: flex; justify-content: space-between; font-size: 18px; margin-top: 15px; font-weight: bold;">
                <span>Tổng thanh toán:</span>
                <span style="color: #e53935;"><?= number_format((float) $order['total_amount'], 0, ',', '.') ?>₫</span>
            </div>
        </div>

        <a href="<?= url('/') ?>" class="btn btn-accent" style="display: inline-block; margin-top: 25px; text-decoration: none; padding: 10px 20px;">
            Tiếp tục mua sắm
        </a>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>