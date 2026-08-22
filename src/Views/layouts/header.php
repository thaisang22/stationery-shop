<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mộc Nhiên</title>
    <link rel="stylesheet" href="<?= url('/public/css/base.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/layouts.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/ui.css') ?>">

    <!-- load page -->
    <?php if (!empty($pageCss)): ?>
    <?php foreach ((array)$pageCss as $css): ?>
        <link rel="stylesheet" href="<?= url('/public/css/' . $css . '.css') ?>">
    <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <div class="announcement">
        MIỄN PHÍ GIAO HÀNG TỪ 299.000₫ · ĐỔI TRẢ TRONG 7 NGÀY
    </div>

    <header class="header">

        <div class="container head-row">

            <button class="menu-btn" onclick="$('.header').classList.toggle('searching')" aria-label="Mở tìm kiếm">
                ☰
            </button>

            <!-- Trang chủ -->
            <a class="logo" href="<?= url('/') ?>">
                mộc <span>nhiên</span>
            </a>

            <!-- Tìm kiếm -->
            <form class="search" action="<?= url('/products') ?>" method="GET">
                <input type="text" name="keyword" id="search-input"
                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                    placeholder="Tìm bút, sổ tay, dụng cụ học tập..." aria-label="Tìm kiếm">

                <button type="submit" class="btn-search" title="Tìm kiếm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </form>

            <div class="head-actions">

                <!-- Tài khoản -->
                <a class="icon-link account-text" href="<?= url('/login') ?>">
                    Tài khoản
                </a>

                <!-- Yêu thích -->
                <a class="icon-link" href="<?= url('/wishlist') ?>" aria-label="Sản phẩm yêu thích">
                    ♡
                </a>

                <!-- Giỏ hàng -->
                <a class="icon-link" href="<?= url('/cart') ?>" aria-label="Giỏ hàng">
                    🛒
                    <!-- <b class="badge">${count()}</b> -->
                </a>

            </div>
        </div>

        <nav class="nav">
            <div class="container nav-row">

                <a href="<?= url('/products') ?>">
                    SẢN PHẨM
                </a>

                <a href="<?= url('/products?category=but-viet') ?>">
                    BÚT VIẾT
                </a>
                <a href="<?= url('/products?category=vo-so-tay') ?>">
                    TẬP/VỞ
                </a>
                <a href="<?= url('/products?category=van-phong-pham') ?>">
                    VĂN PHÒNG
                </a>
                <a href="<?= url('/posts') ?>">
                    GÓC CẢM HỨNG
                </a>

            </div>
        </nav>

    </header>

    <main class="container">