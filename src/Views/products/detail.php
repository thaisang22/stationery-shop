<?php require __DIR__ . '/../layouts/header.php'; ?>

<main class="page container">
    <div class="breadcrumb">Trang chủ / Sản phẩm / Bút gel Thiên Long TL-027</div>
    <div class="detail">
        <div>
            <div class="gallery-main" style="background:#f4dfd7">🖊️</div>
            <div class="thumbs"><button class="thumb selected">🖊️</button><button class="thumb">📦</button><button
                    class="thumb">✦</button></div>
        </div>
        <div><span class="eyebrow">Bút viết</span>
            <h1>Bút gel Thiên Long TL-027</h1>
            <div><span class="rating">★★★★★</span> <span class="sold">4.9/5 · Đã bán 2,3k</span></div>
            <div class="detail-price">8.500₫ <span class="old-price">10.000₫</span></div>
            <p class="detail-desc">Mực gel ra đều, nét viết 0.5 mm sắc nét. Phù hợp ghi chép mỗi ngày.</p>
            <p><b>Vận chuyển:</b> Giao tiêu chuẩn từ 20.000₫ · Miễn phí đơn từ 299.000₫</p>
            <div>
                <div class="qty"><button>−</button><span>1</span><button>+</button></div><a class="btn btn-accent"
                    href="<?= url('/cart.html') ?>">Thêm vào giỏ</a> <a class="btn btn-outline" href="<?= url('/wishlist.html') ?>">♡ Yêu
                    thích</a>
            </div>
        </div>
    </div>
    <div class="tabs">
        <div class="tab-buttons"><button class="active">Mô tả sản phẩm</button><button>Giao hàng &amp; đổi
                trả</button><button>Đánh giá</button></div>
        <div class="tab-content">Mực gel ra đều, nét viết 0.5 mm sắc nét. Sản phẩm được chọn lọc kỹ, phù hợp cho học
            tập, công việc và những góc sáng tạo riêng của bạn.</div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
