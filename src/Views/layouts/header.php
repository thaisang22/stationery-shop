<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mộc Nhiên</title>
    <link rel="stylesheet" href="<?= url('/public/css/base.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/layouts.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/ui.css') ?>">

    <!-- CSS riêng theo trang -->
    <link rel="stylesheet" href="<?= url('/public/css/home.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/catalog.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/checkout.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/login.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/admin.css') ?>">
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
            <form class="search" onsubmit="search(event)">
                <input id="search-input" placeholder="Tìm bút, sổ tay, dụng cụ học tập..." aria-label="Tìm kiếm">

                <button aria-label="Tìm">⌕</button>
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

                <!-- Tất cả sản phẩm -->
                <a href="<?= url('/products') ?>">
                    SẢN PHẨM
                </a>

                <!-- Bút viết -->
                <a href="<?= url('/products?cat=but-viet') ?>">
                    BÚT VIẾT
                </a>

                <!-- Sổ tay -->
                <a href="<?= url('/products?cat=vo-so') ?>">
                    SỔ TAY
                </a>

                <!-- Văn phòng -->
                <a href="<?= url('/products?cat=van-phong') ?>">
                    VĂN PHÒNG
                </a>

                <!-- Bài viết -->
                <a href="<?= url('/posts') ?>">
                    GÓC CẢM HỨNG
                </a>

                <!-- Quản trị
                <a href="<?= url('/admin') ?>">
                    QUẢN TRỊ
                </a> -->

            </div>

        </nav>

    </header>

    <main class="container">
