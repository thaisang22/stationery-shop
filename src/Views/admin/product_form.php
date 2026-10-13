<?php
$isEdit = !empty($product);
$adminTitle = $actionTitle ?? ($isEdit ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới');
$activeMenu = 'products';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';

$formAction = $isEdit 
    ? url('/admin/products/' . (int)$product['id'] . '/edit') 
    : url('/admin/products/create');
?>

<main class="admin-main">
    <div style="margin-bottom: 20px;">
        <span class="eyebrow">QUẢN TRỊ SẢN PHẨM</span>
        <h1 style="margin: 4px 0 0;"><?= htmlspecialchars($adminTitle) ?></h1>
    </div>

    <?php if (!empty($_SESSION['admin_error'])): ?>
        <div class="admin-notice" style="background:#fde8e8; color:#9b1c1c; border-left: 4px solid #9b1c1c; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
            <?= htmlspecialchars($_SESSION['admin_error']) ?>
            <?php unset($_SESSION['admin_error']); ?>
        </div>
    <?php endif; ?>

    <section class="admin-panel" style="max-width: 900px;">
        <form action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 18px;">
                <!-- Cột trái: Thông tin chính -->
                <div>
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="name">Tên sản phẩm <sup>*</sup></label>
                        <input type="text" id="name" name="name" required 
                               value="<?= htmlspecialchars($product['name'] ?? '') ?>" 
                               placeholder="Ví dụ: Bút bi Thiên Long TL-027" 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label for="category_id">Danh mục</label>
                            <select id="category_id" name="category_id" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (!empty($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="brand_id">Thương hiệu</label>
                            <select id="brand_id" name="brand_id" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                                <option value="">-- Chọn thương hiệu --</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>" <?= (!empty($product['brand_id']) && $product['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($brand['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label for="price">Giá bán (VNĐ) <sup>*</sup></label>
                            <input type="number" id="price" name="price" required min="1000" step="500" 
                                   value="<?= htmlspecialchars((string)($product['price'] ?? '')) ?>" 
                                   placeholder="Ví dụ: 15000" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        </div>

                        <div class="form-group">
                            <label for="stock_quantity">Số lượng tồn kho <sup>*</sup></label>
                            <input type="number" id="stock_quantity" name="stock_quantity" required min="0" 
                                   value="<?= htmlspecialchars((string)($product['stock_quantity'] ?? '100')) ?>" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="description">Mô tả sản phẩm</label>
                        <textarea id="description" name="description" rows="5" 
                                  placeholder="Nhập giới thiệu, chất liệu, xuất xứ sản phẩm..." 
                                  style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="is_best_seller" value="1" <?= (!empty($product['is_best_seller'])) ? 'checked' : '' ?>>
                            <span>Đánh dấu là <strong>Sản phẩm nổi bật / Bán chạy (Best Seller)</strong></span>
                        </label>
                    </div>
                </div>

                <!-- Cột phải: Hình ảnh sản phẩm -->
                <div>
                    <div class="form-group" style="background: #fafbf9; padding: 16px; border: 1px solid #e2e8df; border-radius: 6px;">
                        <label>Hình ảnh sản phẩm</label>
                        
                        <?php if ($isEdit && !empty($product['image_url'])): ?>
                            <div style="margin: 10px 0; text-align: center;">
                                <p style="font-size: 12px; color: #666; margin: 0 0 6px;">Ảnh hiện tại:</p>
                                <img src="<?= htmlspecialchars(str_starts_with($product['image_url'], 'http') ? $product['image_url'] : url($product['image_url'])) ?>" 
                                     alt="Ảnh sản phẩm" 
                                     style="max-width: 100%; max-height: 140px; object-fit: contain; border-radius: 4px; border: 1px solid #ddd;">
                            </div>
                        <?php endif; ?>

                        <div style="margin-top: 10px;">
                            <label for="image" style="font-size: 13px; font-weight: normal;">Tải ảnh từ máy tính:</label>
                            <input type="file" id="image" name="image" accept="image/*" style="width: 100%; font-size: 13px; margin-top: 4px;">
                        </div>

                        <div style="margin-top: 14px;">
                            <label for="custom_image_url" style="font-size: 13px; font-weight: normal;">Hoặc dán Link URL ảnh:</label>
                            <input type="text" id="custom_image_url" name="custom_image_url" 
                                   value="<?= htmlspecialchars($product['image_url'] ?? '') ?>" 
                                   placeholder="https://... hoặc /public/images/..." 
                                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; margin-top: 4px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút submit -->
            <div style="display: flex; gap: 12px; border-top: 1px solid #eee; padding-top: 18px;">
                <button type="submit" class="btn btn-accent" style="padding: 11px 24px; font-weight: 600; border-radius: 5px; cursor: pointer;">
                    <?= $isEdit ? 'Lưu thay đổi' : 'Thêm sản phẩm' ?>
                </button>
                <a href="<?= url('/admin/products') ?>" style="padding: 11px 20px; background: #eee; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
