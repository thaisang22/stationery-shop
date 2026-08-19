<?php require __DIR__ . '/../layouts/header.php'; ?>

<main class="page container">
    <h1>Giỏ hàng của bạn</h1>
    <div class="cart-layout">
        <div class="cart-items">
            <div class="cart-item">
                <div class="cart-art" style="background:#f4dfd7">🖊️</div>
                <div><b>Bút gel Thiên Long TL-027</b>
                    <div class="price">8.500₫</div>
                    <div class="qty"><button>−</button><span>1</span><button>+</button></div><button
                        class="text-link">Xóa</button>
                </div><strong>8.500₫</strong>
            </div>
        </div>
        <aside class="summary">
            <h3>Tóm tắt đơn hàng</h3>
            <div class="summary-row"><span>Tạm tính</span><b>8.500₫</b></div>
            <div class="summary-row"><span>Vận chuyển</span><span>Tính ở bước sau</span></div>
            <div class="summary-row summary-total"><span>Tổng cộng</span><span>8.500₫</span></div><a
                class="btn btn-accent" href="<?= url('/checkout') ?>">Tiến hành thanh toán</a>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
