<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Quản trị viên | Mộc Nhiên</title>
    <link rel="icon" type="image/svg+xml" href="<?= url('/public/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/base.css') ?>">
    <link rel="stylesheet" href="<?= url('/public/css/admin.css') ?>">
    <style>
        .admin-login-body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef2eb;
            padding: 20px;
            font-family: inherit;
        }

        .admin-login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #d9e2d5;
            padding: 36px 32px;
        }

        .admin-login-brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .admin-login-brand h1 {
            font-size: 24px;
            margin: 8px 0 4px;
            color: #1e3323;
        }

        .admin-login-brand small {
            display: inline-block;
            background: #2e5b38;
            color: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .admin-login-notice {
            background: #fde8e8;
            color: #9b1c1c;
            border-left: 4px solid #9b1c1c;
            padding: 12px 14px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.5;
        }

        .admin-form-group {
            margin-bottom: 18px;
        }

        .admin-form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .admin-form-group input {
            width: 100%;
            height: 44px;
            padding: 10px 14px;
            border: 1px solid #c8d3c5;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color .2s;
        }

        .admin-form-group input:focus {
            outline: none;
            border-color: #2e5b38;
            box-shadow: 0 0 0 3px rgba(46, 91, 56, 0.15);
        }

        .admin-btn-login {
            width: 100%;
            height: 46px;
            background: #2e5b38;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
            margin-top: 6px;
        }

        .admin-btn-login:hover {
            background: #24492d;
        }

        .admin-login-footer {
            margin-top: 22px;
            text-align: center;
            font-size: 13px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 16px;
        }

        .admin-login-footer a {
            color: #2e5b38;
            text-decoration: none;
            font-weight: 600;
        }

        .admin-login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="admin-login-body">
    <div class="admin-login-card">
        <div class="admin-login-brand">
            <h1>MỘC NHIÊN</h1>
            <small>CỔNG QUẢN TRỊ VIÊN</small>
        </div>

        <?php if (!empty($error)): ?>
            <div class="admin-login-notice">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('/admin/login') ?>" method="POST">
            <div class="admin-form-group">
                <label for="email">Tài khoản Email Quản trị:</label>
                <input type="email" id="email" name="email" required placeholder="admin@mocnhien.com" autofocus>
            </div>

            <div class="admin-form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="admin-btn-login">
                Đăng nhập vào Hệ thống
            </button>
        </form>

        <div class="admin-login-footer">
            <a href="<?= url('/') ?>">Quay về trang chủ cửa hàng</a>
        </div>
    </div>
</body>

</html>
