<?php
//default header
require __DIR__ . '/../layouts/header.php'; ?>

<main>
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-copy"><span class="eyebrow">BACK TO SCHOOL 2026</span>
                <h1>Năm học mới,<br>dụng cụ <em>mới.</em></h1>
                <p>Chọn ngay đồ dùng học tập và làm việc để bạn thực hiện việc ngay hôm nay.</p><a
                    href="<?= url('/products') ?>" class="btn btn-accent">Khám phá bộ sưu tập →</a>
            </div>
            <div class="hero-art" aria-hidden="true">
                <div class="paper-stack"></div>
                <div class="pencil"></div>
                <div class="circle-clip"></div>
            </div>
        </div>
    </section>
    <section class="section container">
        <div class="title-row">
            <div><span class="eyebrow">MUA THEO NHU CẦU</span>
                <h2>Danh mục yêu thích</h2>
            </div><a href="<?= url('/products') ?>" class="text-link">Xem tất cả →</a>
        </div>
        <div class="categories">
            <a href="<?= url('/products?cat=but-viet') ?>" class="category">
                <div class="cat-icon">✒️</div><b>Bút viết</b><small>128 sản phẩm</small>
            </a><a href="<?= url('/products?cat=vo-so') ?>" class="category">
                <div class="cat-icon">📒</div><b>Vở &amp; sổ tay</b><small>96 sản phẩm</small>
            </a><a href="<?= url('/products?cat=giay') ?>" class="category">
                <div class="cat-icon">📄</div><b>Giấy các loại</b><small>72 sản phẩm</small>
            </a><a href="<?= url('/products?cat=dung-cu-hoc-tap') ?>" class="category">
                <div class="cat-icon">📐</div><b>Dụng cụ học tập</b><small>84 sản phẩm</small>
            </a><a href="<?= url('/products?cat=van-phong') ?>" class="category">
                <div class="cat-icon">🗂️</div><b>Văn phòng phẩm</b><small>105 sản phẩm</small>
            </a><a href="<?= url('/products?cat=ban-hoc') ?>" class="category">
                <div class="cat-icon">🪴</div><b>Bàn học xinh</b><small>49 sản phẩm</small>
            </a>
        </div>
    </section>
    <section class="section section-tint">
        <div class="container">
            <div class="title-row">
                <div><span class="eyebrow">ĐƯỢC YÊU THÍCH NHẤT</span>
                    <h2>Best seller tháng này</h2>
                </div><a href="<?= url('/products') ?>" class="text-link">Xem thêm →</a>
            </div>
            <div class="product-grid">
                <article class="product-card">
                    <div class="product-image" style="background:#f4dfd7"><span class="sale">-15%</span><span
                            class="product-art">🖊️</span></div>
                    <div class="product-info">
                        <div><span class="rating">★★★★★</span> <span class="sold">Đã bán 2,3k</span></div><a
                            class="product-name" href="<?= url('/products/1') ?>">Bút gel Thiên Long TL-027</a>
                        <div class="price">8.500₫ <span class="old-price">10.000₫</span></div>
                        <div class="card-actions"><a class="btn" href="<?= url('/products/1') ?>">Xem sản phẩm</a></div>
                    </div>
                </article>
                <article class="product-card">
                    <div class="product-image" style="background:#d8e5d6"><span class="sale">-14%</span><span
                            class="product-art">📓</span></div>
                    <div class="product-info">
                        <div><span class="rating">★★★★★</span> <span class="sold">Đã bán 1,8k</span></div><a
                            class="product-name" href="<?= url('/products/2') ?>">Sổ lò xo Campus B5 200 trang</a>
                        <div class="price">38.500₫ <span class="old-price">45.000₫</span></div>
                        <div class="card-actions"><a class="btn" href="<?= url('/products/2') ?>">Xem sản phẩm</a></div>
                    </div>
                </article>
                <article class="product-card">
                    <div class="product-image" style="background:#f9e2ad"><span class="sale">-13%</span><span
                            class="product-art">🖍️</span></div>
                    <div class="product-info">
                        <div><span class="rating">★★★★★</span> <span class="sold">Đã bán 856</span></div><a
                            class="product-name" href="<?= url('/products/3') ?>">Bộ bút highlight Pastel 6 màu</a>
                        <div class="price">69.000₫ <span class="old-price">79.000₫</span></div>
                        <div class="card-actions"><a class="btn" href="<?= url('/products/3') ?>">Xem sản phẩm</a></div>
                    </div>
                </article>
                <article class="product-card">
                    <div class="product-image" style="background:#f4d9c7"><span class="sale">MỚI</span><span
                            class="product-art">🗒️</span></div>
                    <div class="product-info">
                        <div><span class="rating">★★★★★</span> <span class="sold">Đã bán 1,2k</span></div><a
                            class="product-name" href="<?= url('/products/4') ?>">Giấy note dán 76 × 76 mm</a>
                        <div class="price">28.500₫</div>
                        <div class="card-actions"><a class="btn" href="<?= url('/products/4') ?>">Xem sản phẩm</a></div>
                    </div>
                </article>
            </div>
        </div>
    </section>
    <section class="section container">
        <div class="promo">
            <div class="promo-card"><span class="eyebrow">ƯU ĐÃI TỰU TRƯỜNG</span>
                <h3>Giảm đến 25%<br>đồ dùng học tập</h3><a href="<?= url('/products?cat=dung-cu-hoc-tap') ?>"
                    class="btn btn-outline">Mua
                    ngay</a>
            </div>
            <div class="promo-card"><span class="eyebrow">GÓC BÀN MỚI</span>
                <h3>Từ 49.000₫<br>cho bàn thật xinh</h3><a href="<?= url('/products?cat=ban-hoc') ?>"
                    class="btn btn-outline">Khám
                    phá</a>
            </div>
        </div>
    </section>
    <section class="section container">
        <div class="title-row">
            <div><span class="eyebrow">VỪA VỀ KỆ</span>
                <h2>Sản phẩm mới</h2>
            </div><a href="<?= url('/products') ?>" class="text-link">Xem thêm →</a>
        </div>
        <div class="product-grid">
            <article class="product-card">
                <div class="product-image" style="background:#eee0c1"><span class="sale">-13%</span><span
                        class="product-art">✏️</span></div>
                <div class="product-info">
                    <div><span class="rating">★★★★★</span> <span class="sold">Đã bán 788</span></div><a
                        class="product-name" href="<?= url('/products/9') ?>">Bút chì gỗ 2B Deli hộp 12</a>
                    <div class="price">52.000₫ <span class="old-price">60.000₫</span></div>
                    <div class="card-actions"><a class="btn" href="<?= url('/products/9') ?>">Xem sản phẩm</a></div>
                </div>
            </article>
            <article class="product-card">
                <div class="product-image" style="background:#e2ead9"><span class="sale">MỚI</span><span
                        class="product-art">📔</span></div>
                <div class="product-info">
                    <div><span class="rating">★★★★★</span> <span class="sold">Đã bán 264</span></div><a
                        class="product-name" href="<?= url('/products/10') ?>">Sổ kế hoạch tuần Undated A5</a>
                    <div class="price">99.000₫</div>
                    <div class="card-actions"><a class="btn" href="<?= url('/products/10') ?>">Xem sản phẩm</a></div>
                </div>
            </article>
            <article class="product-card">
                <div class="product-image" style="background:#e7d9d5"><span class="sale">-14%</span><span
                        class="product-art">📎</span></div>
                <div class="product-info">
                    <div><span class="rating">★★★★★</span> <span class="sold">Đã bán 375</span></div><a
                        class="product-name" href="<?= url('/products/11') ?>">Bấm kim mini Deli 10#</a>
                    <div class="price">36.000₫ <span class="old-price">42.000₫</span></div>
                    <div class="card-actions"><a class="btn" href="<?= url('/products/11') ?>">Xem sản phẩm</a></div>
                </div>
            </article>
            <article class="product-card">
                <div class="product-image" style="background:#dce5df"><span class="sale">-14%</span><span
                        class="product-art">🗄️</span></div>
                <div class="product-info">
                    <div><span class="rating">★★★★★</span> <span class="sold">Đã bán 182</span></div><a
                        class="product-name" href="<?= url('/products/12') ?>">Khay để bàn lưới kim loại</a>
                    <div class="price">125.000₫ <span class="old-price">145.000₫</span></div>
                    <div class="card-actions"><a class="btn" href="<?= url('/products/12') ?>">Xem sản phẩm</a></div>
                </div>
            </article>
        </div>
    </section>
    <section class="section section-tint">
        <div class="container">
            <div class="benefits">
                <div class="benefit"><i>🚚</i>
                    <div><b>Giao hàng tận nơi</b><span>Nhanh toàn quốc</span></div>
                </div>
                <div class="benefit"><i>↩</i>
                    <div><b>Đổi trả dễ dàng</b><span>Trong vòng 7 ngày</span></div>
                </div>
                <div class="benefit"><i>▣</i>
                    <div><b>Đóng gói cẩn thận</b><span>Như một món quà</span></div>
                </div>
                <div class="benefit"><i>☏</i>
                    <div><b>Hỗ trợ tận tâm</b><span>8:00 – 21:00 mỗi ngày</span></div>
                </div>
            </div>
        </div>
    </section>
    <section class="section container">
        <div class="title-row">
            <div><span class="eyebrow">GÓC CẢM HỨNG</span>
                <h2>Chuyện của giấy và bút</h2>
            </div><a href="<?= url('/blog') ?>" class="text-link">Đọc tất cả →</a>
        </div>
        <div class="blog-grid">
            <article class="post">
                <div class="post-image">✍️</div>
                <div class="post-body"><span class="eyebrow">12.08.2026</span>
                    <h3>5 cách ghi chú giúp bạn học nhanh hơn</h3>
                    <p>Một vài thói quen đơn giản để biến những trang vở thành công cụ học tập hiệu quả.</p><a
                        href="<?= url('/blog/1') ?>" class="text-link">Đọc bài viết →</a>
                </div>
            </article>
            <article class="post">
                <div class="post-image">📚</div>
                <div class="post-body"><span class="eyebrow">05.08.2026</span>
                    <h3>Chọn sổ tay nào cho năm học mới?</h3>
                    <p>Từ kích thước, loại giấy đến cách chia trang, tìm chiếc sổ phù hợp với nhịp sống của bạn.</p><a
                        href="<?= url('/blog/2') ?>" class="text-link">Đọc bài viết →</a>
                </div>
            </article>
            <article class="post">
                <div class="post-image">🪴</div>
                <div class="post-body"><span class="eyebrow">29.07.2026</span>
                    <h3>Góc bàn làm việc gọn gàng, tâm trí nhẹ nhàng</h3>
                    <p>Gợi ý sắp xếp các vật dụng nhỏ để bạn tập trung hơn mỗi ngày.</p><a href="<?= url('/blog/3') ?>"
                        class="text-link">Đọc
                        bài viết →</a>
                </div>
            </article>
        </div>
    </section>
</main>

<?php
//Default footer
require __DIR__ . '/../layouts/footer.php'; ?>