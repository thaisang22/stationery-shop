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
  <link rel="stylesheet" href="<?= url('/public/css/admin.css') ?>">
  <link rel="stylesheet" href="<?= url('/public/css/ui.css') ?>">
</head>
<body>
  <div class="admin">
    <header class="admin-header">
      <a class="logo" href="<?= url('/') ?>">mộc <span>nhiên</span> <small>ADMIN</small></a>
      <span>Xin chào, Quản trị viên · <a href="<?= url('/') ?>">Xem cửa hàng</a></span>
    </header>
    <div class="admin-layout">
