<?php
/**
 * @var array $productDetail
 * @var array $variants
 */
require __DIR__ . '/../layouts/header.php';
?>

<main class="page container">

    <div class="breadcrumb">
        Trang chủ /
        <?= htmlspecialchars($productDetail['category_name'] ?? 'Sản phẩm') ?> /
        <?= htmlspecialchars($productDetail['name']) ?>
    </div>

    <div class="detail">

        <!-- GALLERY -->
        <div>
            <div class="gallery-main" style="background:#f4dfd7">
                <?php if (!empty($productDetail['image_url'])): ?>
                    <img id="main-product-image" src="<?= url($productDetail['image_url']) ?>"
                        alt="<?= htmlspecialchars($productDetail['name']) ?>">
                <?php else: ?>
                    <span class="product-art">🖊️</span>
                <?php endif; ?>
            </div>

            <!-- THUMBNAILS -->
            <div class="thumbs">
                <?php if (!empty($variants)): ?>
                    <?php foreach ($variants as $variant): ?>
                        <?php if (!empty($variant['image_url'])): ?>
                            <button type="button" class="thumb <?= !empty($variant['is_primary']) ? 'selected' : '' ?>"
                                data-variant-id="<?= $variant['id'] ?>" 
                                data-image-url="<?= url($variant['image_url']) ?>"
                                data-variant-name="<?= htmlspecialchars($variant['variant_name'] ?? '') ?>"
                                data-stock="<?= (int)($variant['stock_quantity'] ?? 0) ?>">
                                <img src="<?= url($variant['image_url']) ?>"
                                    alt="<?= htmlspecialchars($variant['variant_name'] ?? $productDetail['name']) ?>">
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <button type="button" class="thumb selected">🖊️</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- PRODUCT INFO -->
        <div>
            <?php if (!empty($productDetail['category_name'])): ?>
                <span class="eyebrow">
                    <?= htmlspecialchars($productDetail['category_name']) ?>
                </span>
            <?php endif; ?>

            <h1><?= htmlspecialchars($productDetail['name']) ?></h1>

            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; font-size: 14px;">
                <span class="rating">★★★★★</span>
                <span class="sold">Đã bán <?= number_format((float)($productDetail['total_sold'] ?? 0)) ?></span>
                <span style="color: #ccc;">|</span>
                <span id="stock-badge">
                    Tồn kho: <b id="stock-count"><?= number_format((float)($variants[0]['stock_quantity'] ?? $productDetail['total_stock'] ?? 0)) ?></b>
                </span>
            </div>

            <div class="detail-price">
                <?= number_format((float) $productDetail['price'], 0, ',', '.') ?>₫
            </div>

            <?php if (!empty($productDetail['description'])): ?>
                <p class="detail-desc">
                    <?= nl2br(htmlspecialchars($productDetail['description'])) ?>
                </p>
            <?php endif; ?>

            <!-- COLOR VARIANTS -->
            <?php
            $hasColorVariants = array_filter($variants ?? [], function ($v) {
                return !empty($v['variant_name']) && $v['variant_name'] !== 'Mặc định';
            });
            ?>
            <form action="<?= url('/cart/add') ?>" method="post" id="add-to-cart-form">
                <input type="hidden" name="variant_id" id="selected-variant-id"
                    value="<?= htmlspecialchars($variants[0]['id'] ?? '') ?>">

                <?php if (!empty($hasColorVariants)): ?>
                    <div class="variant-selection" style="margin: 20px 0;">
                        <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                            Màu sắc: <span id="selected-color-name" style="font-weight: normal; color: #555;"></span>
                        </label>
                        <div class="color-options" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <?php foreach ($variants as $variant): ?>
                                <?php if ($variant['variant_name'] !== 'Mặc định'): ?>
                                    <button type="button" class="color-opt <?= !empty($variant['is_primary']) ? 'active' : '' ?>"
                                        data-variant-id="<?= $variant['id'] ?>"
                                        data-variant-name="<?= htmlspecialchars($variant['variant_name']) ?>"
                                        data-image-url="<?= url($variant['image_url']) ?>"
                                        data-stock="<?= (int)($variant['stock_quantity'] ?? 0) ?>"
                                        title="<?= htmlspecialchars($variant['variant_name']) ?>" style="
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 6px;
                                        padding: 6px 14px;
                                        border-radius: 20px;
                                        border: 2px solid <?= !empty($variant['is_primary']) ? '#000' : '#ddd' ?>;
                                        background: #fff;
                                        cursor: pointer;
                                        font-size: 14px;
                                    ">
                                        <?php if (!empty($variant['color_code'])): ?>
                                            <span style="
                                            width: 14px; 
                                            height: 14px; 
                                            border-radius: 50%; 
                                            background-color: <?= htmlspecialchars($variant['color_code']) ?>;
                                            border: 1px solid rgba(0,0,0,0.2);
                                        "></span>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($variant['variant_name']) ?></span>
                                    </button>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <p>
                    <b>Vận chuyển:</b>
                    Giao tiêu chuẩn từ 20.000₫ ·
                    Miễn phí đơn từ 299.000₫
                </p>

                <!-- ACTION -->
                <div style="margin-top: 20px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div class="qty" style="display: flex; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                        <button type="button" id="btn-decrease" style="padding: 8px 12px; background: #f8f9fa; border: none; cursor: pointer;">−</button>
                        <input type="number" name="quantity" id="cart-quantity" value="1" min="1" max="1"
                            style="width: 50px; text-align: center; border: none; outline: none;">
                        <button type="button" id="btn-increase" style="padding: 8px 12px; background: #f8f9fa; border: none; cursor: pointer;">+</button>
                    </div>

                    <button type="submit" id="btn-add-to-cart" class="btn btn-accent">
                        Thêm vào giỏ
                    </button>

                    <a class="btn btn-outline" href="<?= url('/wishlist') ?>">
                        ♡ Yêu thích
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- PRODUCT DESCRIPTION TABS -->
    <div class="tabs" style="margin-top: 40px;">
        <div class="tab-buttons">
            <button class="active">Mô tả sản phẩm</button>
            <button>Giao hàng &amp; đổi trả</button>
            <button>Đánh giá</button>
        </div>

        <div class="tab-content" style="padding: 20px 0;">
            <?php if (!empty($productDetail['description'])): ?>
                <?= nl2br(htmlspecialchars($productDetail['description'])) ?>
            <?php else: ?>
                Chưa có mô tả cho sản phẩm này.
            <?php endif; ?>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImg = document.getElementById('main-product-image');
    const hiddenVariantInput = document.getElementById('selected-variant-id');
    const colorNameLabel = document.getElementById('selected-color-name');
    const colorButtons = document.querySelectorAll('.color-opt');
    const thumbButtons = document.querySelectorAll('.thumb');
    
    const stockCountEl = document.getElementById('stock-count');
    const stockBadgeEl = document.getElementById('stock-badge');
    const btnAddToCart = document.getElementById('btn-add-to-cart');

    const btnDecrease = document.getElementById('btn-decrease');
    const btnIncrease = document.getElementById('btn-increase');
    const inputQuantity = document.getElementById('cart-quantity');

    // Cập nhật biến thể, tồn kho và trạng thái nút bấm
    function updateSelectedVariant(variantId, imageUrl, variantName, stockQuantity) {
        stockQuantity = parseInt(stockQuantity) || 0;

        if (hiddenVariantInput && variantId) {
            hiddenVariantInput.value = variantId;
        }

        if (mainImg && imageUrl) {
            mainImg.src = imageUrl;
        }

        if (colorNameLabel && variantName) {
            colorNameLabel.textContent = variantName;
        }

        // Cập nhật tồn kho
        if (stockCountEl) {
            stockCountEl.textContent = stockQuantity > 0 ? stockQuantity.toLocaleString('vi-VN') : 'Hết hàng';
        }

        if (stockBadgeEl) {
            stockBadgeEl.style.color = stockQuantity > 0 ? '#2e7d32' : '#d32f2f';
            stockBadgeEl.style.fontWeight = '600';
        }

        // Cập nhật số lượng tối đa được chọn
        if (inputQuantity) {
            inputQuantity.max = stockQuantity;
            if (stockQuantity > 0) {
                inputQuantity.disabled = false;
                if (parseInt(inputQuantity.value) > stockQuantity) {
                    inputQuantity.value = stockQuantity;
                } else if (parseInt(inputQuantity.value) < 1) {
                    inputQuantity.value = 1;
                }
            } else {
                inputQuantity.value = 0;
                inputQuantity.disabled = true;
            }
        }

        // Cập nhật trạng thái nút mua hàng
        if (btnAddToCart) {
            if (stockQuantity > 0) {
                btnAddToCart.disabled = false;
                btnAddToCart.textContent = 'Thêm vào giỏ';
                btnAddToCart.style.opacity = '1';
                btnAddToCart.style.cursor = 'pointer';
            } else {
                btnAddToCart.disabled = true;
                btnAddToCart.textContent = 'Hết hàng';
                btnAddToCart.style.opacity = '0.6';
                btnAddToCart.style.cursor = 'not-allowed';
            }
        }

        // Cập nhật giao diện nút chọn màu
        colorButtons.forEach(btn => {
            const isActive = btn.dataset.variantId === String(variantId);
            btn.classList.toggle('active', isActive);
            btn.style.borderColor = isActive ? '#000' : '#ddd';
        });

        // Cập nhật thumbnail active
        thumbButtons.forEach(thumb => {
            thumb.classList.toggle('selected', thumb.dataset.variantId === String(variantId));
        });
    }

    // Tăng/giảm số lượng
    function setupQuantityControl() {
        if (!btnDecrease || !btnIncrease || !inputQuantity) return;

        btnDecrease.addEventListener('click', function () {
            let currentValue = parseInt(inputQuantity.value) || 1;
            const min = parseInt(inputQuantity.min) || 1;
            if (currentValue > min) {
                inputQuantity.value = currentValue - 1;
            }
        });

        btnIncrease.addEventListener('click', function () {
            let currentValue = parseInt(inputQuantity.value) || 1;
            const max = parseInt(inputQuantity.max) || 1;
            if (currentValue < max) {
                inputQuantity.value = currentValue + 1;
            }
        });

        inputQuantity.addEventListener('change', function () {
            let currentValue = parseInt(this.value) || 1;
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 1;
            
            if (currentValue < min) this.value = min;
            else if (currentValue > max) this.value = max;
            else this.value = currentValue;
        });
    }

    setupQuantityControl();

    // Sự kiện click nút Màu sắc
    colorButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            updateSelectedVariant(
                this.dataset.variantId,
                this.dataset.imageUrl,
                this.dataset.variantName,
                this.dataset.stock
            );
        });
    });

    // Sự kiện click nút Thumbnail
    thumbButtons.forEach(thumb => {
        thumb.addEventListener('click', function () {
            updateSelectedVariant(
                this.dataset.variantId,
                this.dataset.imageUrl,
                this.dataset.variantName,
                this.dataset.stock
            );
        });
    });

    // Khởi tạo trạng thái ban đầu
    const activeBtn = document.querySelector('.color-opt.active') || document.querySelector('.thumb.selected');
    if (activeBtn) {
        updateSelectedVariant(
            activeBtn.dataset.variantId,
            activeBtn.dataset.imageUrl,
            activeBtn.dataset.variantName,
            activeBtn.dataset.stock
        );
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>