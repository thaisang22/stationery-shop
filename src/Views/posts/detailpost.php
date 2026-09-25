<?php
/** @var array<string, mixed> $post */

$publishedAt = $post['published_at'] ?? $post['created_at'] ?? null;
$publishedDate = $publishedAt ? date('d.m.Y', strtotime((string) $publishedAt)) : '';
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>

<section class="section post-detail">
    <nav class="post-detail__breadcrumb" aria-label="Breadcrumb">
        <a href="<?= url('/') ?>">Trang chủ</a>
        <span>/</span>
        <a href="<?= url('/posts') ?>">Góc cảm hứng</a>
        <span>/</span>
        <span><?= htmlspecialchars((string) $post['title']) ?></span>
    </nav>

    <article class="post-detail__article">
        <header class="post-detail__header">
            <?php if ($publishedDate !== ''): ?>
                <span class="eyebrow"><?= htmlspecialchars((string) $post['post_type']) ?> · <?= $publishedDate ?></span>
            <?php endif; ?>
            <h1><?= htmlspecialchars((string) $post['title']) ?></h1>
        </header>

        <div class="post-detail__image">
            <?php if (!empty($post['img_post'])): ?>
                <img src="<?= url('/public/' . ltrim((string) $post['img_post'], '/')) ?>" alt="<?= htmlspecialchars((string) $post['title']) ?>">
            <?php else: ?>✍️<?php endif; ?>
        </div>

        <div class="post-detail__content">
            <?= (string) $post['content'] ?>
        </div>

        <div class="post-detail__actions">
            <a class="text-link" href="<?= url('/posts') ?>">← Quay lại Góc cảm hứng</a>
            <a class="btn btn-outline" href="<?= url('/products') ?>">Khám phá sản phẩm</a>
        </div>
    </article>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
