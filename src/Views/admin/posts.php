<?php
$adminTitle = 'Bài viết';
$activeMenu = 'posts';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main">
    <span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span>
    <div class="title-row admin-title-row">
        <h1>Bài viết</h1><a class="btn btn-accent" href="<?= url('/admin/posts/create') ?>">+ Thêm bài viết</a>
    </div>
    <?php if (!empty($success)): ?>
        <p class="admin-notice"><?= htmlspecialchars((string) $success) ?></p><?php endif; ?>
    <section class="admin-panel">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Ngày đăng</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($posts)):
                        foreach ($posts as $post): ?>
                            <tr>
                                <td><?php if (!empty($post['img_post'])): ?><img class="admin-post-thumb"
                                            src="<?= url('/public/' . ltrim((string) $post['img_post'], '/')) ?>"
                                            alt=""><?php else: ?><span
                                            class="admin-post-thumb admin-post-thumb--empty">✍️</span><?php endif; ?></td>
                                <td><a class="text-link" href="<?= url('/posts/' . rawurlencode((string) $post['slug'])) ?>"><?= htmlspecialchars((string) $post['title']) ?></a></td>
                                <td><?= htmlspecialchars((string) $post['post_type']) ?></td>
                                <td><?= !empty($post['published_at']) ? date('d.m.Y', strtotime((string) $post['published_at'])) : '—' ?>
                                </td>
                                <td><span
                                        class="status <?= empty($post['hidden_at']) ? '' : 'status--draft' ?>"><?= empty($post['hidden_at']) ? 'Đã xuất bản' : 'Bản nháp' ?></span>
                                </td>
                                <td><a class="text-link" href="<?= url('/admin/posts/' . $post['id'] . '/edit') ?>">Sửa</a></td>
                                <td>
                                    <form class="post-row-action" method="post" action="<?= url('/admin/posts/' . $post['id'] . '/hidden') ?>">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <button class="btn-action-post" type="submit"><?= empty($post['hidden_at']) ? 'Ẩn' : 'Hiện' ?></button>
                                    </form>
                                </td>
                                <td>
                                    <form class="post-row-action" method="post" action="<?= url('/admin/posts/' . $post['id'] . '/delete') ?>" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <button class="btn-action-post" type="submit">Xóa</button>
                                    </form>
                                </td>
                            </tr><?php endforeach; else: ?>
                        <tr>
                            <td colspan="6" class="admin-empty-cell">Chưa có bài viết nào. Hãy tạo bài viết đầu tiên.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
