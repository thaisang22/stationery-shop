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

<?php require __DIR__ . '/../layouts/footer.php'; ?>
