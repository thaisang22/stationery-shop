<?php require __DIR__ . '/../layouts/header.php'; ?>

<section class="section container blog-section">
    <div class="title-row">
        <div>
            <span class="eyebrow">CẢM HỨNG</span>
            <h2>Góc nhỏ Mộc Nhiên</h2>
        </div>
        <a href="<?= url('/posts') ?>" class="text-link">Đọc tất cả →</a>
    </div>

    <div class="blog-grid">
        <?php if (!empty($listPosts)): ?>
            <?php foreach ($listPosts as $post): ?>
                <article class="post-card">
                    <div class="post-image-wrapper">
                        <?php if (!empty($post['img_post'])): ?><img src="<?= url('/public/' . ltrim((string) $post['img_post'], '/')) ?>" alt="<?= htmlspecialchars((string) $post['title']) ?>"><?php else: ?><span class="post-art">✍️</span><?php endif; ?>
                    </div>
                    <div class="post-body">
                        <span class="post-date">
                            <?= date('d.m.Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                        </span>

                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <p>
                            <?= htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 100)) ?>...
                        </p>

                        <a href="<?= url('/posts/' . $post['slug']) ?>" class="text-link">Xem thêm →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Chưa có bài viết nào được đăng tải.</p>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
