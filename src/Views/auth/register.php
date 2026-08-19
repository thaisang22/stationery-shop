<?php require __DIR__ . '/../layouts/header.php'; ?>


<main class="page">
    <form class="form-box auth"><span class="eyebrow">TÀI KHOẢN MỘC NHIÊN</span>
        <h1>Tạo tài khoản</h1>
        <div class="field"><label>Họ và tên *</label><input required></div>
        <div class="field"><label>Email *</label><input type="email" required></div>
        <div class="field"><label>Mật khẩu *</label><input type="password" minlength="6" required>
            <div class="error">Tối thiểu 6 ký tự</div>
        </div><a class="btn btn-accent" href="<?= url('/profile.html') ?>" style="width:100%">Đăng ký</a>
        <div class="auth-switch">Đã có tài khoản? <a class="text-link" href="<?= url('/login') ?>">Đăng nhập</a></div>
    </form>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
