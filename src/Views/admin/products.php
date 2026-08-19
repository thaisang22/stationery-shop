<?php
$adminTitle = 'Sản phẩm';
$activeMenu = 'products';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main"><span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
    <h1>Sản phẩm</h1>
    <section class="admin-panel">
        <div class="title-row">
            <h2>Danh sách sản phẩm</h2><button class="btn btn-accent">+ Thêm mới</button>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Đã bán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>🖊️ Bút gel Thiên Long TL-027</td>
                        <td>Bút viết</td>
                        <td>8.500₫</td>
                        <td>2,3k</td>
                        <td><span class="status">Đang bán</span></td>
                        <td><button class="text-link">Sửa</button></td>
                    </tr>
                    <tr>
                        <td>📓 Sổ lò xo Campus B5 200 trang</td>
                        <td>Vở &amp; sổ tay</td>
                        <td>38.500₫</td>
                        <td>1,8k</td>
                        <td><span class="status">Đang bán</span></td>
                        <td><button class="text-link">Sửa</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>