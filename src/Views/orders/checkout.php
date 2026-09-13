<?php require __DIR__ . '/../layouts/header.php'; ?>
<!-- Thêm đoạn này để hiển thị lỗi nếu có -->
<?php if (isset($_SESSION['error'])): ?>
    <div style="background-color: #ffebe9; color: #d03538; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f8c4c3;">
        ⚠️ <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<main class="page container">
    <h1>Thanh toán</h1>
    
    <div class="checkout">
        <form action="<?= url('/checkout/process') ?>" method="post" class="form-box">
            <h2>Thông tin giao hàng</h2>
            
            <div class="form-grid">
                <div class="field">
                    <label for="fullname">Họ và tên *</label>
                    <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label for="phone">Số điện thoại *</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                </div>
            </div>

            <div class="field">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>

            <div class="field">
                <label for="address">Địa chỉ nhận hàng *</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="city">Tỉnh / Thành phố *</label>
                    <select id="city" name="city" required>
                        <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                        <option value="Hà Nội">Hà Nội</option>
                        <option value="Đà Nẵng">Đà Nẵng</option>
                    </select>
                </div>
                <div class="field">
                    <label for="payment_method">Phương thức thanh toán *</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                        <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                        <option value="momo">Ví điện tử MoMo</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-accent" style="margin-top: 15px;">Đặt hàng</button>
        </form>

        <!-- summary -->
        <aside class="summary">
            <h3>Đơn hàng (<?= count($cartItems ?? []) ?> sản phẩm)</h3>
            
            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="summary-row" style="align-items: flex-start;">
                        <div>
                            <span><?= htmlspecialchars($item['product_name'] ?? $item['name'] ?? '') ?></span>
                            <?php if (!empty($item['variant_name']) && $item['variant_name'] !== 'Mặc định'): ?>
                                <div style="font-size: 12px; color: #666;">
                                    Phân loại: <?= htmlspecialchars($item['variant_name']) ?>
                                </div>
                            <?php endif; ?>
                            <small style="color: #888;">× <?= (int) ($item['quantity'] ?? 1) ?></small>
                        </div>
                        <b><?= number_format((float) ($item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1))), 0, ',', '.') ?>₫</b>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="summary-row summary-total" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                <span>Tổng cộng:</span>
                <span style="font-size: 18px; color: #e53935; font-weight: bold;"><?= number_format((float) ($totalAmount ?? 0), 0, ',', '.') ?>₫</span>
            </div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>