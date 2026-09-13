<?php require __DIR__ . '/../layouts/header.php'; ?>

<section class="page">
    <form class="form-box auth"><span class="eyebrow">TÀI KHOẢN MỘC NHIÊN</span>
        <h1>Chào mừng bạn trở lại</h1>
        <div class="field"><label>Email *</label><input type="email" required></div>
        <div class="field"><label>Mật khẩu *</label><input type="password" minlength="6" required>
            <div class="error">Tối thiểu 6 ký tự</div>
        </div><a class="btn btn-accent" href="<?= url('/profile.html') ?>" style="width:100%">Đăng nhập</a>
        <div class="auth-switch">Chưa có tài khoản? <a class="text-link" href="<?= url('/register') ?>">Tạo tài khoản</a>
        </div>
    </form>
</section>
<?php if (!empty($error)): ?>
    <div style="padding: 10px; background: #ffebe9; color: #d03538; border-radius: 4px; margin-bottom: 15px;">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Form đăng nhập thực tế (đang phát triển) -->
<form action="<?= url('/login') ?>" method="post">
    <!-- các input username / password -->
</form>

<hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

<!-- Nút giả lập đăng nhập 1-click -->
<form action="<?= url('/login/mock') ?>" method="post">
    <button type="submit" class="btn" style="width: 100%; background: #4caf50; color: #fff; padding: 10px; border-radius: 4px; border: none; cursor: pointer;">
        ⚡ Đăng nhập giả lập (Test Checkout)
    </button>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
