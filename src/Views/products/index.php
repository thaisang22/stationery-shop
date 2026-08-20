<?php require __DIR__ . '/../layouts/header.php'; ?>

<main class="page container">

    <div class="breadcrumb">
        Trang chủ / Sản phẩm
    </div>

    <h1>Tất cả sản phẩm</h1>

    <div class="listing">

        <!-- FILTER -->
        <aside class="filter">

            <h3>Bộ lọc</h3>

            <div class="filter-group">

                <b>Danh mục</b>

                <label>
                    <input type="radio" name="cat">
                    Bút viết
                </label>

                <label>
                    <input type="radio" name="cat">
                    Vở & sổ tay
                </label>

                <label>
                    <input type="radio" name="cat">
                    Giấy các loại
                </label>

                <label>
                    <input type="radio" name="cat">
                    Dụng cụ học tập
                </label>

                <label>
                    <input type="radio" name="cat">
                    Văn phòng phẩm
                </label>

                <label>
                    <input
                        type="radio"
                        name="cat"
                        checked
                    >
                    Tất cả
                </label>

            </div>


            <div class="filter-group">

                <b>Khoảng giá</b>

                <label>
                    <input type="checkbox">
                    Dưới 50.000₫
                </label>

                <label>
                    <input type="checkbox">
                    50.000₫ – 100.000₫
                </label>

                <label>
                    <input type="checkbox">
                    Trên 100.000₫
                </label>

            </div>

        </aside>


        <!-- PRODUCTS -->
        <div>

            <!-- TOOLBAR -->
            <div class="toolbar">

                <span>
                    <?= count($products) ?> sản phẩm
                </span>

                <select>

                    <option>
                        Sắp xếp mặc định
                    </option>

                    <option>
                        Giá: thấp đến cao
                    </option>

                    <option>
                        Giá: cao đến thấp
                    </option>

                </select>

            </div>


            <!-- PRODUCT GRID -->
            <div class="product-grid">

                <?php if (empty($products)): ?>

                    <p>
                        Không có sản phẩm nào.
                    </p>

                <?php else: ?>

                    <?php foreach ($products as $product): ?>

                        <article class="product-card">


                            <!-- IMAGE -->
                            <div class="product-image">

                                <?php if (!empty($product['image_url'])): ?>

                                    <img
                                        src="<?= url( $product['image_url']
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $product['name']
                                        ) ?>"
                                    >

                                <?php else: ?>

                                    <span class="product-art">
                                        🖊️
                                    </span>

                                <?php endif; ?>
                            </div>


                            <!-- INFO -->
                            <div class="product-info">


                                <!-- RATING -->
                                <div>

                                    <span class="rating">
                                        ★★★★★
                                    </span>

                                    <span class="sold">
                                        Đã bán 0
                                    </span>

                                </div>


                                <!-- NAME -->
                                <a
                                    class="product-name"
                                    href="<?= url(
                                        '/product-detail?id=' .
                                        $product['id']
                                    ) ?>"
                                >
                                    <?= htmlspecialchars(
                                        $product['name']
                                    ) ?>
                                </a>


                                <!-- PRICE -->
                                <div class="price">

                                    <?= number_format(
                                        (float) $product['price'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>₫

                                </div>


                                <!-- CART -->
                                <div class="card-actions">

                                    <a
                                        class="btn"
                                        href="<?= url('/cart') ?>"
                                    >
                                        Thêm giỏ
                                    </a>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>


            <!-- PAGINATION -->
            <div class="pagination">

                <button class="current">
                    1
                </button>

                <button>
                    2
                </button>

                <button>
                    →
                </button>

            </div>

        </div>

    </div>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>