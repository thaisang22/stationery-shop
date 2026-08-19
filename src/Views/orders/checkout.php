<?php require __DIR__ . '/../layouts/header.php'; ?>

<main class="page container">
    <h1>Thanh toán</h1>
    <div class="checkout">
        <form class="form-box">
            <h2>Thông tin giao hàng</h2>
            <div class="form-grid">
                <div class="field"><label>Họ và tên *</label><input required></div>
                <div class="field"><label>Số điện thoại *</label><input required></div>
            </div>
            <div class="field"><label>Email *</label><input type="email" required></div>
            <div class="field"><label>Địa chỉ nhận hàng *</label><input required></div>
            <div class="form-grid">
                <div class="field"><label>Tỉnh / Thành phố</label><select>
                        <option>Hồ Chí Minh</option>
                        <option>Hà Nội</option>
                        <option>Đà Nẵng</option>
                    </select></div>
                <div class="field"><label>Phương thức thanh toán</label><select>
                        <option>Thanh toán khi nhận hàng</option>
                        <option>Chuyển khoản ngân hàng</option>
                        <option>Ví điện tử MoMo</option>
                    </select></div>
            </div><button class="btn btn-accent">Đặt hàng</button>
        </form>
        <aside class="summary">
            <h3>Đơn hàng (1 sản phẩm)</h3>
            <div class="summary-row"><span>Bút gel Thiên Long TL-027 × 1</span><b>8.500₫</b></div>
            <div class="summary-row summary-total"><span>Tổng cộng</span><span>8.500₫</span></div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>