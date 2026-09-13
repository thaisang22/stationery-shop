<?php
/**
 * @var array $products
 * @var int $page
 * @var int $totalPages
 * @var int $total
 * @var array $filters
 * @var array $categories
 * @var array $brands
 */
//default header
require __DIR__ . '/../layouts/header.php'; ?>

<main class="home-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-grid">
            <div class="hero-copy">
                <span class="eyebrow">MỘC NHIÊN STATIONERY</span>
                <h1>Những điều nhỏ bé<br>cho một ngày <em>nhiều cảm hứng.</em></h1>
                <p>Văn phòng phẩm được tuyển chọn kỹ lưỡng, hướng tới phong cách tối giản, mộc mạc, giúp mang lại sự thư
                    thái và khơi nguồn sáng tạo cho học tập và làm việc mỗi ngày.</p>
                <div class="hero-actions">
                    <a href="<?= url('/products') ?>" class="btn btn-accent">Khám phá sản phẩm</a>
                </div>
            </div>
            <div class="hero-image-wrapper">
                <img src="<?= url('/public/images/hero_lifestyle.png') ?>" alt="Mộc Nhiên Stationery Lifestyle"
                    class="hero-img">
            </div>
        </div>
    </section>

    <!-- Category Section -->
    <section class="section container categories-section">
        <div class="title-row text-center">
            <span class="eyebrow">KHÁM PHÁ</span>
            <h2>Danh mục được yêu thích</h2>
        </div>
        <div class="categories-grid">
            <?php
            $icons = [
                'but-viet' => '✒️',
                'vo-so-tay' => '📒',
                'giay-cac-loai' => '📄',
                'dung-cu-hoc-tap' => '📐',
                'van-phong-pham' => '🗂️',
            ];
            ?>

            <?php foreach ($categories as $category): ?>
                <a href="<?= url('/products?category_slug=' . $category['slug']) ?>" class="category-card">
                    <div class="cat-icon">
                        <?= $icons[$category['slug']] ?? '📦' ?>
                    </div>
                    <b><?= htmlspecialchars($category['name']) ?></b>
                    <?php if (isset($category['product_count'])): ?>
                        <small><?= $category['product_count'] ?> sản phẩm</small>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Best Seller Section -->
    <section class="section section-tint best-sellers-section">
        <div class="container">
            <div class="title-row">
                <div>
                    <span class="eyebrow">ĐƯỢC YÊU THÍCH</span>
                    <h2>Những sản phẩm bán chạy</h2>
                </div>
                <a href="<?= url('/products') ?>" class="text-link">Xem tất cả →</a>
            </div>

            <div class="product-grid">
                <?php if (empty($productsBestSeller)): ?>
                    <p class="empty-text">Chưa có sản phẩm nổi bật nào.</p>
                <?php else: ?>
                    <?php foreach ($productsBestSeller as $product): ?>
                        <article class="product-card">
                            <div class="product-image">
                                <?php if ($product['id'] % 3 == 0): ?>
                                    <span class="sale-badge">-15%</span>
                                <?php endif; ?>
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?= url($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <span class="product-art">🖊️</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <div class="product-meta">
                                    <span class="sold">Đã bán
                                        <?= number_format((float) ($product['total_sold'] ?? $product['sold'] ?? 0)) ?></span>
                                </div>
                                <a class="product-name" href="<?= url('product/' . $product['slug']) ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                                <div class="price-row">
                                    <span class="price"><?= number_format((float) $product['price'], 0, ',', '.') ?>₫</span>
                                    <?php if ($product['id'] % 3 == 0): ?>
                                        <span
                                            class="old-price"><?= number_format((float) $product['price'] * 1.15, 0, ',', '.') ?>₫</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-actions">
                                    <a class="btn btn-add-cart" href="<?= url('/cart') ?>">Thêm vào giỏ</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Promotion Section -->
    <section class="section container promotions-section">
        <div class="promotions-grid">
            <div class="promo-card promo-sage">
                <span class="promo-eyebrow">MỘC NHIÊN</span>
                <h3>BACK TO SCHOOL</h3>
                <p class="promo-desc">Giảm đến 30% cho tất cả dụng cụ học tập thiết yếu.</p>
                <a href="<?= url('/products') ?>" class="btn btn-accent">Mua ngay</a>
            </div>

            <div class="promo-card promo-beige">
                <span class="promo-eyebrow">WORKSPACE MỚI</span>
                <h3>GÓC BÀN MỚI</h3>
                <p class="promo-desc">Những món đồ nhỏ xinh xắn cho góc làm việc đầy cảm hứng.</p>
                <a href="<?= url('/products') ?>" class="btn btn-outline">Khám phá</a>
            </div>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="section container new-arrivals-section">
        <div class="title-row">
            <div>
                <span class="eyebrow">MỚI LÊN KỆ</span>
                <h2>Sản phẩm mới nhất</h2>
            </div>
            <a href="<?= url('/products') ?>" class="text-link">Xem thêm →</a>
        </div>

        <div class="product-grid">
            <?php if (empty($productsNew)): ?>
                <p class="empty-text">Chưa có sản phẩm mới nào.</p>
            <?php else: ?>
                <?php foreach ($productsNew as $product): ?>
                    <article class="product-card">
                        <div class="product-image">
                            <span class="sale-badge new-badge">MỚI</span>
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= url($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php else: ?>
                                <span class="product-art">📓</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-meta">
                                <!-- <span class="rating">★★★★★</span>  -->
                                <span class="sold">Đã bán
                                    <?= number_format((float) ($product['total_sold'] ?? $product['sold'] ?? 0)) ?></span>
                            </div>
                            <a class="product-name" href="<?= url('product/' . $product['slug']) ?>">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                            <div class="price-row">
                                <span class="price"><?= number_format((float) $product['price'], 0, ',', '.') ?>₫</span>
                            </div>
                            <div class="card-actions">
                                <a class="btn btn-add-cart" href="<?= url('/cart') ?>">Thêm vào giỏ</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Brand Story Section -->
    <section class="section section-tint brand-story-section" id="brand-story">
        <div class="container brand-story-grid">
            <div class="brand-story-copy">
                <span class="eyebrow">VỀ MỘC NHIÊN</span>
                <h2>Những điều giản dị tạo nên cảm hứng mỗi ngày.</h2>
                <p>Mộc Nhiên được tạo ra với mong muốn mang những sản phẩm văn phòng phẩm đẹp, tiện dụng, mộc mạc và gần
                    gũi với thiên nhiên đến mọi người.</p>
                <p>Chúng tôi tin rằng, mỗi cuốn sổ tay, mỗi chiếc bút hay vật dụng trang trí nhỏ trên bàn làm việc không
                    đơn thuần là công cụ hỗ trợ công việc, mà còn là người bạn đồng hành nuôi dưỡng tâm hồn và đánh thức
                    nguồn sáng tạo tiềm ẩn trong bạn.</p>
                <a href="<?= url('/#brand-story') ?>" class="btn btn-outline">Câu chuyện Mộc Nhiên</a>
            </div>
            <div class="brand-story-image">
                <img src="<?= url('/public/images/brand_story.png') ?>" alt="Về Mộc Nhiên" class="story-img">
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="section container benefits-section">
        <div class="benefits-grid">
            <div class="benefit-item">
                <div class="benefit-icon">🚚</div>
                <div class="benefit-text">
                    <h4>Giao hàng nhanh</h4>
                    <span>Toàn quốc</span>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon">🌿</div>
                <div class="benefit-text">
                    <h4>Sản phẩm chọn lọc</h4>
                    <span>Chất lượng tốt</span>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon">↩️</div>
                <div class="benefit-text">
                    <h4>Đổi trả dễ dàng</h4>
                    <span>Trong 7 ngày</span>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon">💚</div>
                <div class="benefit-text">
                    <h4>Hỗ trợ tận tâm</h4>
                    <span>Luôn đồng hành</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="section container blog-section">
        <div class="title-row">
            <div>
                <span class="eyebrow">CẢM HỨNG</span>
                <h2>Góc nhỏ Mộc Nhiên</h2>
            </div>
            <a href="<?= url('/posts') ?>" class="text-link">Đọc tất cả →</a>
        </div>
        <div class="blog-grid">
            <article class="post-card">
                <div class="post-image-wrapper">
                    <span class="post-art">✍️</span>
                </div>
                <div class="post-body">
                    <span class="post-date">12.08.2026</span>
                    <h3>5 cách ghi chú giúp bạn học nhanh hơn</h3>
                    <p>Một vài thói quen đơn giản để biến những trang vở thành công cụ học tập hiệu quả, kích thích sơ
                        đồ tư duy.</p>
                    <a href="<?= url('/posts') ?>" class="text-link">Xem thêm →</a>
                </div>
            </article>

            <article class="post-card">
                <div class="post-image-wrapper beige-art">
                    <span class="post-art">📚</span>
                </div>
                <div class="post-body">
                    <span class="post-date">05.08.2026</span>
                    <h3>Cách chọn sổ tay cho năm học mới</h3>
                    <p>Từ kích thước, định lượng giấy đến cách chia trang, tìm chiếc sổ phù hợp với nhịp sống và thói
                        quen journaling.</p>
                    <a href="<?= url('/posts') ?>" class="text-link">Xem thêm →</a>
                </div>
            </article>

            <article class="post-card">
                <div class="post-image-wrapper sage-art">
                    <span class="post-art">🪴</span>
                </div>
                <div class="post-body">
                    <span class="post-date">29.07.2026</span>
                    <h3>Góc bàn làm việc gọn gàng, tâm trí nhẹ nhàng</h3>
                    <p>Gợi ý sắp xếp các vật dụng nhỏ, hộp bút lưới và khay đựng tài liệu để bạn duy trì sự tập trung
                        tối đa.</p>
                    <a href="<?= url('/posts') ?>" class="text-link">Xem thêm →</a>
                </div>
            </article>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container newsletter-content">
            <span class="eyebrow">NEWSLETTER</span>
            <h2>Một chút cảm hứng gửi vào hộp thư của bạn.</h2>
            <p>Nhận ưu đãi sớm nhất, các bộ sưu tập mới và những câu chuyện nhỏ mộc mạc từ Mộc Nhiên.</p>
            <form class="newsletter-form"
                onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký nhận bản tin!');">
                <input type="email" placeholder="Email của bạn..." required aria-label="Đăng ký email">
                <button type="submit" class="btn btn-accent">Đăng ký</button>
            </form>
        </div>
    </section>
</main>

<?php
//Default footer
require __DIR__ . '/../layouts/footer.php'; ?>