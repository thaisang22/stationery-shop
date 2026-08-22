
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
require __DIR__ . '/../layouts/header.php'; ?>

<?php
$queryParams = $_GET;
?>

<main class="page container">

    <div class="breadcrumb">
        Trang chủ / Sản phẩm
    </div>

    <h1>Tất cả sản phẩm</h1>

    <!-- FORM filters -->
    <form method="GET" action="<?= url('/products') ?>" id="filter-form">

        <div class="listing">

            <!-- FILTER SIDEBAR -->
            <aside class="filter">
                <div class="filter-header">
                    <h3>Bộ lọc</h3>
                    <a href="<?= url('/products') ?>" class="btn-reset-filter" title="Tải lại bộ lọc">
                        <svg class="icon-reload" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/>
                            <path d="M21 3v5h-5"/>
                        </svg>
                        <span>Đặt lại</span>
                    </a>
                </div>

                <!-- search -->
                <b>Tìm kiếm</b>
    <div class="search-box">
        <input 
            type="text" 
            name="keyword" 
            value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" 
            placeholder="Từ khóa..."
            autocomplete="off"
        >
        <button type="submit" class="btn-search" title="Tìm kiếm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
    </div>

                <!-- category-->
                <div class="filter-group">
                    <b>Danh mục</b>

                    <label>
                        <input 
                            type="radio" 
                            name="category" 
                            value="" 
                            <?= empty($filters['category_slug']) ? 'checked' : '' ?>
                            onchange="this.form.submit()"
                        >
                        Tất cả
                    </label>

                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <label>
                                <input 
                                    type="radio" 
                                    name="category" 
                                    value="<?= htmlspecialchars($cat['slug']) ?>"
                                    <?= ($filters['category_slug'] ?? '') === $cat['slug'] ? 'checked' : '' ?>
                                    onchange="this.form.submit()"
                                >
                                <?= htmlspecialchars($cat['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <label>
                            <input type="radio" name="category" value="but-viet" <?= ($filters['category_slug'] ?? '') === 'but-viet' ? 'checked' : '' ?> onchange="this.form.submit()">
                            Bút viết
                        </label>
                        <label>
                            <input type="radio" name="category" value="vo-so-tay" <?= ($filters['category_slug'] ?? '') === 'vo-so-tay' ? 'checked' : '' ?> onchange="this.form.submit()">
                            Vở & sổ tay
                        </label>
                        <label>
                            <input type="radio" name="category" value="giay-cac-loai" <?= ($filters['category_slug'] ?? '') === 'giay-cac-loai' ? 'checked' : '' ?> onchange="this.form.submit()">
                            Giấy các loại
                        </label>
                    <?php endif; ?>
                </div>

                <!--  Brand Slug -->
                <div class="filter-group">
                    <b>Thương hiệu</b>

                    <label>
                        <input 
                            type="radio" 
                            name="brand" 
                            value="" 
                            <?= empty($filters['brand_slug']) ? 'checked' : '' ?>
                            onchange="this.form.submit()"
                        >
                        Tất cả thương hiệu
                    </label>

                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $b): ?>
                            <label>
                                <input 
                                    type="radio" 
                                    name="brand" 
                                    value="<?= htmlspecialchars($b['slug']) ?>"
                                    <?= ($filters['brand_slug'] ?? '') === $b['slug'] ? 'checked' : '' ?>
                                    onchange="this.form.submit()"
                                >
                                <?= htmlspecialchars($b['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <label>
                            <input type="radio" name="brand" value="thien-long" <?= ($filters['brand_slug'] ?? '') === 'thien-long' ? 'checked' : '' ?> onchange="this.form.submit()">
                            Thiên Long
                        </label>
                        <label>
                            <input type="radio" name="brand" value="hong-ha" <?= ($filters['brand_slug'] ?? '') === 'hong-ha' ? 'checked' : '' ?> onchange="this.form.submit()">
                            Hồng Hà
                        </label>
                        <label>
                            <input type="radio" name="brand" value="campus" <?= ($filters['brand_slug'] ?? '') === 'campus' ? 'checked' : '' ?> onchange="this.form.submit()">
                            Campus
                        </label>
                    <?php endif; ?>
                </div>

                <!-- price -->
                <div class="filter-group">
                    <b>Khoảng giá</b>

                    <label>
                        <input 
                            type="radio" 
                            name="price_range" 
                            value="" 
                            <?= empty($filters['price_range']) ? 'checked' : '' ?>
                            onchange="this.form.submit()"
                        >
                        Tất cả mức giá
                    </label>

                    <label>
                        <input 
                            type="radio" 
                            name="price_range" 
                            value="0-50000" 
                            <?= ($filters['price_range'] ?? '') === '0-50000' ? 'checked' : '' ?>
                            onchange="this.form.submit()"
                        >
                        Dưới 50.000đ
                    </label>

                    <label>
                        <input 
                            type="radio" 
                            name="price_range" 
                            value="50000-100000" 
                            <?= ($filters['price_range'] ?? '') === '50000-100000' ? 'checked' : '' ?>
                            onchange="this.form.submit()"
                        >
                        50.000đ - 100.000đ
                    </label>

                    <label>
                        <input 
                            type="radio" 
                            name="price_range" 
                            value="100000-200000" 
                            <?= ($filters['price_range'] ?? '') === '100000-200000' ? 'checked' : '' ?>
                            onchange="this.form.submit()"
                        >
                        100.000đ - 200.000đ
                    </label>

                    <label>
                        <input 
                            type="radio" 
                            name="price_range" 
                            value="200000-up" 
                            <?= ($filters['price_range'] ?? '') === '200000-up' ? 'checked' : '' ?>
                            onchange="this.form.submit()"
                        >
                        Trên 200.000đ
                    </label>
                </div>

            </aside>


            <!-- PRODUCTS DISPLAY SECTION -->
            <div>

                <!-- TOOLBAR -->
                <div class="toolbar">

                    <span>
                        <?= $total ?? count($products) ?> sản phẩm
                    </span>

                    <select name="sort" onchange="this.form.submit()">
                        <option value="newest" <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>
                            Mới nhất
                        </option>
                        <option value="oldest" <?= ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>
                            Cũ nhất
                        </option>
                        <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>
                            Giá: thấp đến cao
                        </option>
                        <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>
                            Giá: cao đến thấp
                        </option>
                    </select>

                </div>


                <!-- PRODUCT GRID -->
                <div class="product-grid">

                    <?php if (empty($products)): ?>

                        <p>Không có sản phẩm nào phù hợp.</p>

                    <?php else: ?>

                        <?php foreach ($products as $product): ?>

                            <article class="product-card">

                                <!-- IMAGE -->
                                <div class="product-image">
                                    <?php if (!empty($product['image_url'])): ?>
                                        <img
                                            src="<?= url($product['image_url']) ?>"
                                            alt="<?= htmlspecialchars($product['name']) ?>"
                                        >
                                    <?php else: ?>
                                        <span class="product-art">🖊️</span>
                                    <?php endif; ?>
                                </div>

                                <!-- INFO -->
                                <div class="product-info">

                                    <!-- RATING -->
                                    <div>
                                        <span class="rating">★★★★★</span>
                                        <span class="sold">Đã bán 0</span>
                                    </div>

                                    <!-- NAME -->
                                    <a
                                        class="product-name"
                                        href="<?= url('/product-detail?id=' . $product['id']) ?>"
                                    >
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>

                                    <!-- PRICE -->
                                    <div class="price">
                                        <?= number_format((float) $product['price'], 0, ',', '.') ?>₫
                                    </div>

                                    <!-- CART -->
                                    <div class="card-actions">
                                        <a class="btn" href="<?= url('/cart') ?>">
                                            Thêm giỏ
                                        </a>
                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>


                <!-- PAGINATION  -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <div class="pagination">
                        
                        <!-- prv -->
                        <?php if (($page ?? 1) > 1): ?>
                            <?php $queryParams['page'] = ($page - 1); ?>
                            <a href="?<?= http_build_query($queryParams) ?>">&laquo;</a>
                        <?php endif; ?>

                        <!-- page number -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php $queryParams['page'] = $i; ?>
                            <a 
                                href="?<?= http_build_query($queryParams) ?>" 
                                class="<?= ($i === ($page ?? 1)) ? 'current' : '' ?>"
                            >
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- next -->
                        <?php if (($page ?? 1) < $totalPages): ?>
                            <?php $queryParams['page'] = ($page + 1); ?>
                            <a href="?<?= http_build_query($queryParams) ?>">&rarr;</a>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>

        </div>

    </form>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>