<?php require __DIR__ . '/../layouts/header.php'; ?>

<main class="page container">
    <h1>Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)): ?>
        <div style="text-align: center; padding: 50px 0;">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">Giỏ hàng của bạn đang trống.</p>
            <a href="<?= url('/products') ?>" class="btn btn-accent">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <!-- list products -->
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <!-- img prodcut -->
                        <div class="cart-art">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?= url($item['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                📦
                            <?php endif; ?>
                        </div>

                        <!-- infor product -->
                        <div>
                            <b><?= htmlspecialchars($item['product_name']) ?></b>
                            
                            <?php if (!empty($item['variant_name']) && $item['variant_name'] !== 'Mặc định'): ?>
                                <div style="font-size: 13px; color: #666; margin-top: 2px;">
                                    Phân loại: <?= htmlspecialchars($item['variant_name']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="price"><?= number_format((float) $item['price'], 0, ',', '.') ?>₫</div>

                            <!-- action quantity product -->
                            <form action="<?= url('/cart/update') ?>" method="post" style="margin-top: 10px;">
                                <input type="hidden" name="variant_id" value="<?= (int) $item['variant_id'] ?>">
                                
                                <div class="qty" style="display: inline-flex; align-items: center;">
                                    <!-- decrease product -->
                                    <button type="submit" name="action" value="decrease" 
                                            style="width: 28px; height: 28px; border: 1px solid #ccc; background: #fff; border-radius: 4px 0 0 4px; cursor: pointer; line-height: 1;"
                                            <?= $item['quantity'] <= 1 ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>>−</button>
                                    
                                    <!-- input quantity -->
                                    <input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" min="1" 
                                           style="width: 45px; height: 28px; text-align: center; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; border-left: none; border-right: none; font-size: 14px;" 
                                           onchange="this.form.submit()">
                                           
                                    <!-- increase products -->
                                    <button type="submit" name="action" value="increase" 
                                            style="width: 28px; height: 28px; border: 1px solid #ccc; background: #fff; border-radius: 0 4px 4px 0; cursor: pointer; line-height: 1;">+</button>
                                </div>
                            </form>

                            <!-- delete product -->
                            <form action="<?= url('/cart/remove') ?>" method="post" style="margin-top: 8px;">
                                <input type="hidden" name="variant_id" value="<?= (int) $item['variant_id'] ?>">
                                <button type="submit" class="text-link" style="background: none; border: none; padding: 0; cursor: pointer; color: #e53935; font-size: 13px;" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                    Xóa
                                </button>
                            </form>
                        </div>

                        <!-- total price -->
                        <strong><?= number_format((float) $item['subtotal'], 0, ',', '.') ?>₫</strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- summary -->
            <aside class="summary">
                <h3>Tóm tắt đơn hàng</h3>
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <b><?= number_format((float) $totalAmount, 0, ',', '.') ?>₫</b>
                </div>
                <div class="summary-row">
                    <span>Vận chuyển</span>
                    <span>Tính ở bước sau</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Tổng cộng</span>
                    <span><?= number_format((float) $totalAmount, 0, ',', '.') ?>₫</span>
                </div>
                <a class="btn btn-accent" href="<?= url('/checkout') ?>">Tiến hành thanh toán</a>
            </aside>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>