<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mộc Nhiên - Cửa hàng văn phòng phẩm xanh</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Georgia:ital,wght@0,400;0,700;1,400&family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <link rel="stylesheet" href="<?= url('/public/css/base.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/layouts.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/ui.css') ?>">

    <!-- load page -->
    <?php if (!empty($pageCss)): ?>
        <?php foreach ((array) $pageCss as $css): ?>
            <link rel="stylesheet" href="<?= url('/public/css/' . $css . '.css') ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <div class="announcement">
        <span>🌿 MIỄN PHÍ GIAO HÀNG TỪ 299.000₫ · ĐỔI TRẢ TRONG 7 NGÀY 🌿</span>
    </div>

    <header class="header">
        <div class="container head-row">
            <!-- Mobile Menu Toggle -->
            <button class="menu-btn" onclick="toggleMobileMenu()" aria-label="Mở menu">
                ☰
            </button>

            <!-- Brand Logo -->
            <a class="logo" href="<?= url('/') ?>">
                <svg class="logo-leaf" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.58,20C9,20.67 11.6,20 13.6,18.4C16.8,15.83 18.6,12 17,8M15.2,16.3C13.8,17.4 12,17.9 10.2,17.5L14.3,13.4C14.7,13 14.7,12.3 14.3,11.9C13.9,11.5 13.2,11.5 12.8,11.9L8.7,16C8.3,14.2 8.8,12.4 9.9,11C11.5,9 13.5,8.2 15.2,8.1C15.9,11 14.5,14 15.2,16.3Z" />
                </svg>
                <div class="logo-text">
                    <span class="logo-title">MỘC NHIÊN</span>
                    <span class="logo-subtitle">Stationery & More</span>
                </div>
            </a>

            <!-- Search Bar -->
            <form class="search" onsubmit="search(event)">
                <input id="search-input" placeholder="Tìm bút, sổ tay, dụng cụ học tập..." aria-label="Tìm kiếm">
                <button type="submit" aria-label="Tìm">⌕</button>
            </form>

            <!-- Action Links -->
            <div class="head-actions">
                <!-- Search trigger for Mobile -->
                <button class="icon-link mobile-search-trigger" onclick="toggleMobileSearch()" aria-label="Tìm kiếm">
                    ⌕
                </button>

                <!-- Tài khoản -->
                <a class="icon-link account-link" href="<?= url('/login') ?>" title="Tài khoản">
                    <span class="account-text">Tài khoản</span>
                    <span class="account-icon">👤</span>
                </a>

                <!-- Yêu thích -->
                <a class="icon-link" href="<?= url('/wishlist') ?>" aria-label="Sản phẩm yêu thích" title="Yêu thích">
                    ♡
                </a>

                <?php
                // Tự động tính số lượng badge trực tiếp từ Model hoặc Session
                $cartCount = 0;

                if (isset($_SESSION['user_id'])) {
                    // Nếu đã đăng nhập: Lấy số lượng từ Model Cart
                    $cartModel = new \App\Models\Cart();
                    $cartCount = $cartModel->getTotalQuantity((int) $_SESSION['user_id']);
                } elseif (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                    // Nếu chưa đăng nhập: Tính tổng số lượng từ Session
                    $cartCount = (int) array_sum($_SESSION['cart']);
                }
                ?>

                <a class="icon-link cart-link" href="<?= url('/cart') ?>" aria-label="Giỏ hàng" title="Giỏ hàng">
                    🛒
                    <?php if ($cartCount > 0): ?>
                        <b class="badge"><?= $cartCount ?></b>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav" id="main-nav">
            <div class="container nav-row">
                <a href="<?= url('/') ?>">Trang chủ</a>
                <a href="<?= url('/products') ?>">Sản phẩm</a>
                <a href="<?= url('/posts') ?>">Bài viết </a>
                <a href="<?= url('/#brand-story') ?>">Về Mộc Nhiên</a>
            </div>
        </nav>
    </header>

    <main class="container">