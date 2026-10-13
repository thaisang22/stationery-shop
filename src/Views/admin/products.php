<?php
$adminTitle = 'Quản lý sản phẩm';
$activeMenu = 'products';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';

$hasActiveFilters = !empty($filters['keyword']) 
    || !empty($filters['category_id']) 
    || !empty($filters['brand_id']) 
    || !empty($filters['price_range']) 
    || !empty($filters['stock_status'])
    || (!empty($filters['sort']) && $filters['sort'] !== 'newest');
?>

<main class="admin-main">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
            <h1 style="margin: 4px 0 0;">Quản lý Sản phẩm</h1>
        </div>
        <a href="<?= url('/admin/products/create') ?>" class="btn btn-accent" style="text-decoration:none; display:inline-block; padding: 10px 18px; border-radius: 6px; font-weight: 600;">
            + Thêm sản phẩm mới
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="admin-notice" style="background:#e7f4e8; color:#276238; border-left: 4px solid #276238; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="admin-notice" style="background:#fde8e8; color:#9b1c1c; border-left: 4px solid #9b1c1c; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- KHỐI BỘ LỌC TÌM KIẾM SẢN PHẨM (TƯƠNG TỰ PHÍA USER) -->
    <section class="admin-panel" style="margin-bottom: 20px; padding: 18px 20px;">
        <form method="GET" action="<?= url('/admin/products') ?>" id="admin-product-filter">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; border-bottom: 1px solid #f0f3eb; padding-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2e5b38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <strong style="font-size: 14px; color: #1e3323;">Bộ lọc & Tìm kiếm sản phẩm</strong>
                    <?php if ($hasActiveFilters): ?>
                        <span style="font-size: 11px; background: #eaf4ec; color: #2e5b38; padding: 2px 7px; border-radius: 999px; font-weight: 600;">Đang lọc</span>
                    <?php endif; ?>
                </div>

                <?php if ($hasActiveFilters): ?>
                    <a href="<?= url('/admin/products') ?>" style="font-size: 13px; color: #9b1c1c; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                        ✕ Xóa bộ lọc
                    </a>
                <?php endif; ?>
            </div>

            <!-- Hàng các trường lọc -->
            <div class="admin-filter-grid" style="display: grid; grid-template-columns: 2fr 1.3fr 1.3fr 1.3fr 1.2fr 1.3fr auto; gap: 10px; align-items: end;">
                <!-- 1. Từ khóa -->
                <div class="form-group" style="margin: 0;">
                    <label style="font-size: 12px; margin-bottom: 4px; font-weight: 600;">Tên sản phẩm:</label>
                    <input 
                        type="text" 
                        name="keyword" 
                        value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" 
                        placeholder="Tìm theo tên..." 
                        style="height: 38px; min-height: 38px; padding: 6px 10px; font-size: 13px; border: 1px solid #ccd5c7; border-radius: 5px; width: 100%;"
                    >
                </div>

                <!-- 2. Danh mục -->
                <div class="form-group" style="margin: 0;">
                    <label style="font-size: 12px; margin-bottom: 4px; font-weight: 600;">Danh mục:</label>
                    <select name="category_id" style="height: 38px; min-height: 38px; padding: 6px 10px; font-size: 13px; border: 1px solid #ccd5c7; border-radius: 5px; width: 100%;">
                        <option value="">-- Tất cả danh mục --</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= ((int)($filters['category_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 3. Thương hiệu -->
                <div class="form-group" style="margin: 0;">
                    <label style="font-size: 12px; margin-bottom: 4px; font-weight: 600;">Thương hiệu:</label>
                    <select name="brand_id" style="height: 38px; min-height: 38px; padding: 6px 10px; font-size: 13px; border: 1px solid #ccd5c7; border-radius: 5px; width: 100%;">
                        <option value="">-- Tất cả thương hiệu --</option>
                        <?php if (!empty($brands)): ?>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= (int)$b['id'] ?>" <?= ((int)($filters['brand_id'] ?? 0) === (int)$b['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 4. Khoảng giá -->
                <div class="form-group" style="margin: 0;">
                    <label style="font-size: 12px; margin-bottom: 4px; font-weight: 600;">Khoảng giá:</label>
                    <select name="price_range" style="height: 38px; min-height: 38px; padding: 6px 10px; font-size: 13px; border: 1px solid #ccd5c7; border-radius: 5px; width: 100%;">
                        <option value="">-- Tất cả mức giá --</option>
                        <option value="0-50000" <?= ($filters['price_range'] ?? '') === '0-50000' ? 'selected' : '' ?>>Dưới 50.000₫</option>
                        <option value="50000-100000" <?= ($filters['price_range'] ?? '') === '50000-100000' ? 'selected' : '' ?>>50.000₫ - 100.000₫</option>
                        <option value="100000-200000" <?= ($filters['price_range'] ?? '') === '100000-200000' ? 'selected' : '' ?>>100.000₫ - 200.000₫</option>
                        <option value="200000-up" <?= ($filters['price_range'] ?? '') === '200000-up' ? 'selected' : '' ?>>Trên 200.000₫</option>
                    </select>
                </div>

                <!-- 5. Tồn kho -->
                <div class="form-group" style="margin: 0;">
                    <label style="font-size: 12px; margin-bottom: 4px; font-weight: 600;">Tồn kho:</label>
                    <select name="stock_status" style="height: 38px; min-height: 38px; padding: 6px 10px; font-size: 13px; border: 1px solid #ccd5c7; border-radius: 5px; width: 100%;">
                        <option value="">-- Tất cả tồn kho --</option>
                        <option value="in" <?= ($filters['stock_status'] ?? '') === 'in' ? 'selected' : '' ?>>Còn hàng</option>
                        <option value="low" <?= ($filters['stock_status'] ?? '') === 'low' ? 'selected' : '' ?>>Sắp hết (≤ 5)</option>
                        <option value="out" <?= ($filters['stock_status'] ?? '') === 'out' ? 'selected' : '' ?>>Hết hàng (0)</option>
                    </select>
                </div>

                <!-- 6. Sắp xếp -->
                <div class="form-group" style="margin: 0;">
                    <label style="font-size: 12px; margin-bottom: 4px; font-weight: 600;">Sắp xếp:</label>
                    <select name="sort" style="height: 38px; min-height: 38px; padding: 6px 10px; font-size: 13px; border: 1px solid #ccd5c7; border-radius: 5px; width: 100%;">
                        <option value="newest" <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="oldest" <?= ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                        <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="stock_asc" <?= ($filters['sort'] ?? '') === 'stock_asc' ? 'selected' : '' ?>>Tồn kho ít nhất</option>
                        <option value="stock_desc" <?= ($filters['sort'] ?? '') === 'stock_desc' ? 'selected' : '' ?>>Tồn kho nhiều nhất</option>
                        <option value="bestseller" <?= ($filters['sort'] ?? '') === 'bestseller' ? 'selected' : '' ?>>Bán chạy nhất</option>
                    </select>
                </div>

                <!-- Nút Lọc -->
                <div style="margin: 0;">
                    <button type="submit" class="btn btn-accent" style="height: 38px; padding: 0 16px; border-radius: 5px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                        Lọc
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- BẢNG DANH SÁCH SẢN PHẨM -->
    <section class="admin-panel">
        <div class="title-row" style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 16px; margin: 0; color: #1e3323;">
                Danh sách sản phẩm (<?= (int)($total ?? count($products ?? [])) ?> sản phẩm)
            </h2>
            <?php if (!empty($total) && $total > 0): ?>
                <small style="color: #666;">
                    Đang hiển thị <?= count($products ?? []) ?> sản phẩm trên trang <?= (int)($page ?? 1) ?>
                </small>
            <?php endif; ?>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th>Giá bán</th>
                        <th>Tồn kho</th>
                        <th>Đã bán</th>
                        <th style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <?php 
                                        $img = $p['image_url'] ?? '';
                                        if (empty($img)) {
                                            $img = url('/public/images/no-image.png');
                                        } elseif (!str_starts_with($img, 'http')) {
                                            $img = url($img);
                                        }
                                    ?>
                                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($p['name']) ?></strong>
                                    <?php if (!empty($p['is_best_seller'])): ?>
                                        <span style="font-size: 11px; background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 4px; margin-left: 5px;">Hot</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['category_name'] ?? 'Chưa phân loại') ?></td>
                                <td><?= htmlspecialchars($p['brand_name'] ?? '—') ?></td>
                                <td><strong style="color: #276238;"><?= number_format((float)$p['price'], 0, ',', '.') ?>₫</strong></td>
                                <td>
                                    <?php $stock = (int)($p['stock_quantity'] ?? 0); ?>
                                    <span style="<?= $stock <= 5 ? 'color: red; font-weight: bold;' : '' ?>">
                                        <?= $stock ?>
                                    </span>
                                </td>
                                <td><?= (int)($p['sold_count'] ?? 0) ?></td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="<?= url('/admin/products/' . $p['id'] . '/edit') ?>" style="display: inline-block; padding: 5px 10px; background: #f0f3eb; color: #2e5b38; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 500; margin-right: 6px;">
                                        Sửa
                                    </a>
                                    <form action="<?= url('/admin/products/' . $p['id'] . '/delete') ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                        <button type="submit" style="padding: 5px 10px; background: #fde8e8; color: #9b1c1c; border: none; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer;">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="admin-empty-cell">
                                <?= $hasActiveFilters ? 'Không tìm thấy sản phẩm nào khớp với bộ lọc.' : 'Chưa có sản phẩm nào trong hệ thống.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PHÂN TRANG -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <?php 
                $queryParams = $_GET;
                $currentPage = (int)($page ?? 1);
            ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eef2ea; flex-wrap: wrap; gap: 10px;">
                <span style="font-size: 13px; color: #666;">
                    Trang <strong><?= $currentPage ?></strong> / <strong><?= $totalPages ?></strong> (Tổng <?= (int)$total ?> sản phẩm)
                </span>
                <div style="display: flex; gap: 5px;">
                    <?php if ($currentPage > 1): ?>
                        <?php $queryParams['page'] = $currentPage - 1; ?>
                        <a href="?<?= http_build_query($queryParams) ?>" style="padding: 6px 12px; border: 1px solid #d9dfd5; border-radius: 4px; text-decoration: none; color: #333; font-size: 13px; background: #fff;">&laquo; Trước</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                            <?php $queryParams['page'] = $i; ?>
                            <a href="?<?= http_build_query($queryParams) ?>" style="padding: 6px 12px; border: 1px solid <?= $i === $currentPage ? '#2e5b38' : '#d9dfd5' ?>; background: <?= $i === $currentPage ? '#2e5b38' : '#fff' ?>; color: <?= $i === $currentPage ? '#fff' : '#333' ?>; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: <?= $i === $currentPage ? '600' : 'normal' ?>;">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                            <span style="padding: 6px 6px; color: #999; font-size: 13px;">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <?php $queryParams['page'] = $currentPage + 1; ?>
                        <a href="?<?= http_build_query($queryParams) ?>" style="padding: 6px 12px; border: 1px solid #d9dfd5; border-radius: 4px; text-decoration: none; color: #333; font-size: 13px; background: #fff;">Sau &raquo;</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>