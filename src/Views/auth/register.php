```php
<?php require __DIR__ . '/../layouts/header.php'; ?>

<main class="page">

    <form
        class="form-box auth"
        action="<?= url('/register') ?>"
        method="POST"
        id="registerForm"
        novalidate
    >

        <span class="eyebrow">TÀI KHOẢN MỘC NHIÊN</span>

        <h1>Tạo tài khoản</h1>

        <!-- Họ và tên -->
        <div class="field">
            <label for="full_name">
                Họ và tên *
            </label>

            <input
                type="text"
                id="full_name"
                name="full_name"
            >

            <div class="error" id="fullNameError"></div>
        </div>

        <!-- Email -->
        <div class="field">
            <label for="email">
                Email *
            </label>

            <input
                type="email"
                id="email"
                name="email"
            >

            <div class="error" id="emailError"></div>
        </div>

        <!-- Password -->
        <div class="field">
            <label for="password">
                Mật khẩu *
            </label>

            <input
                type="password"
                id="password"
                name="password"
            >

            <div class="error" id="passwordError"></div>
        </div>

        <button
            type="submit"
            class="btn btn-accent"
            style="width:100%"
        >
            Đăng ký
        </button>

        <div class="auth-switch">
            Đã có tài khoản?

            <a
                class="text-link"
                href="<?= url('/login') ?>"
            >
                Đăng nhập
            </a>
        </div>

    </form>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('registerForm');

    const fullName = document.getElementById('full_name');
    const email = document.getElementById('email');
    const password = document.getElementById('password');

    const fullNameError = document.getElementById('fullNameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    form.addEventListener('submit', function (event) {

        let isValid = true;

        // Reset lỗi
        fullNameError.textContent = '';
        emailError.textContent = '';
        passwordError.textContent = '';

        // Validate họ tên
        if (fullName.value.trim() === '') {
            fullNameError.textContent =
                'Vui lòng nhập họ và tên.';

            isValid = false;
        }

        // Validate email
        if (email.value.trim() === '') {

            emailError.textContent =
                'Vui lòng nhập email.';

            isValid = false;

        } else if (!isValidEmail(email.value.trim())) {

            emailError.textContent =
                'Email không hợp lệ.';

            isValid = false;
        }

        // Validate password
        if (password.value.length < 6) {

            passwordError.textContent =
                'Mật khẩu phải có ít nhất 6 ký tự.';

            isValid = false;
        }

        // Nếu frontend không hợp lệ
        if (!isValid) {
            event.preventDefault();
        }
    });


    function isValidEmail(email) {

        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    }

});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
```
