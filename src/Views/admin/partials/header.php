<?php
/** @var string $adminTitle Tiêu đề hiển thị trong thẻ title. */
$adminTitle = $adminTitle ?? 'Quản trị';
?>
<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($adminTitle) ?> | Mộc Nhiên</title>
  <link rel="stylesheet" href="<?= url('/public/css/base.css') ?>">
  <link rel="stylesheet" href="<?= url('/public/css/admin.css') ?>?>">
  <link rel="stylesheet" href="<?= url('/public/css/ui.css') ?>">
  <!-- load page -->
  <?php if (!empty($pageCss)): ?>
    <?php foreach ((array) $pageCss as $css): ?>
      <link rel="stylesheet" href="<?= url('/public/css/' . $css . '.css') ?>">
    <?php endforeach; ?>
  <?php endif; ?>
</head>

<body>
  <div class="admin">
    <header class="admin-header">
      <a class="logo" href="<?= url('/') ?>">mộc <span>nhiên</span> <small>ADMIN</small></a>
      <span>
        Xin chào, <strong><?= htmlspecialchars($_SESSION['user']['full_name'] ?? 'Quản trị viên') ?></strong> · 
        <a href="<?= url('/') ?>" target="_blank">Xem cửa hàng</a> · 
        <a href="<?= url('/admin/logout') ?>" style="color: #c53030; font-weight: 600; text-decoration: none; margin-left: 6px;">Đăng xuất</a>
      </span>
    </header>
    <div class="admin-layout">