```php
<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$success = $_SESSION['register_success'] ?? null;
unset($_SESSION['register_success']);

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<section class="page">

    <form class="form-box auth" action="<?= url('/login') ?>" method="POST">

        <span class="eyebrow">
            TÀI KHOẢN MỘC NHIÊN
        </span>

        <h1>Chào mừng bạn trở lại</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Email -->
        <div class="field">
            <label for="email">
                Email *
            </label>

            <input type="email" id="email" name="email" required>
        </div>

        <!-- Password -->
        <div class="field">
            <label for="password">
                Mật khẩu *
            </label>

            <input type="password" id="password" name="password" minlength="6" required>

            <div class="error">
                Tối thiểu 6 ký tự
            </div>
        </div>

        <!-- Login -->
        <button type="submit" class="btn btn-accent" style="width:100%">
            Đăng nhập
        </button>

        <div class="auth-switch">
            Chưa có tài khoản?

            <a class="text-link" href="<?= url('/register') ?>">
                Tạo tài khoản
            </a>
        </div>

    </form>

</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
```