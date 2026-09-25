<?php
$isEditing = is_array($post ?? null);
$adminTitle = $isEditing ? 'Chỉnh sửa bài viết' : 'Tạo bài viết';
$activeMenu = 'posts';
$value = static fn(string $key, string $default = ''): string => htmlspecialchars((string) (($old[$key] ?? null) ?? ($post[$key] ?? $default)));
$content = (string) (($old['content'] ?? null) ?? ($post['content'] ?? ''));
$published = isset($old['is_published']) || ($isEditing && empty($post['hidden_at']));
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="admin-main"><a class="text-link" href="<?= url('/admin/posts') ?>">← Quay lại danh sách bài viết</a>
    <h1><?= $isEditing ? 'Chỉnh sửa bài viết' : 'Tạo bài viết mới' ?></h1>
    <form class="admin-panel post-form" action="<?= url($isEditing ? '/admin/posts/' . $post['id'] : '/admin/posts') ?>"
        method="post" enctype="multipart/form-data"><input type="hidden" name="_token"
            value="<?= htmlspecialchars($csrfToken) ?>">
        <div class="post-form-grid">
            <div class="form-group post-form-wide"><label for="title">Tiêu đề <sup>*</sup></label><input id="title"
                    name="title" value="<?= $value('title') ?>" required maxlength="255"
                    placeholder="Ví dụ: 5 mẹo học tập hiệu quả"><?php if (!empty($errors['title'])): ?><small
                        class="form-error"><?= htmlspecialchars($errors['title']) ?></small><?php endif; ?></div>
            <div class="form-group"><label for="post_type">Loại bài viết</label><select id="post_type"
                    name="post_type"><?php foreach (['BLOG' => 'Blog', 'NEWS' => 'Tin tức', 'ABOUT' => 'Giới thiệu'] as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $value('post_type', 'BLOG') === $key ? 'selected' : '' ?>><?= $label ?>
                        </option><?php endforeach; ?>
                </select></div>
            <div class="form-group"><label for="img_post">Ảnh đại diện</label><input id="img_post" name="img_post"
                    type="file" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG hoặc WebP · tối đa 5
                    MB.</small><?php if (!empty($errors['img_post'])): ?><small
                        class="form-error"><?= htmlspecialchars($errors['img_post']) ?></small><?php endif; ?><?php if ($isEditing && !empty($post['img_post'])): ?><img
                        class="post-image-preview" src="<?= url('/public/' . $post['img_post']) ?>"
                        alt="Ảnh đại diện hiện tại"><?php endif; ?></div>
            <div class="form-group post-form-wide"><label for="editor">Nội dung <sup>*</sup></label>
                <div class="editor-toolbar" role="toolbar"><button type="button" data-command="formatBlock"
                        data-value="h2">H2</button><button type="button" data-command="formatBlock"
                        data-value="h3">H3</button><button type="button" data-command="bold"><b>B</b></button><button
                        type="button" data-command="italic"><i>I</i></button><button type="button"
                        data-command="underline"><u>U</u></button><button type="button"
                        data-command="insertUnorderedList">• Danh sách</button><button type="button"
                        data-command="insertOrderedList">1. Danh sách</button><button type="button"
                        data-command="formatBlock" data-value="blockquote">❝ Trích dẫn</button><button type="button"
                        id="add-link">↗ Liên kết</button><button type="button" data-command="removeFormat">Xóa định
                        dạng</button></div>
                <div id="editor" class="rich-editor" contenteditable="true"
                    data-placeholder="Viết nội dung bài viết tại đây…"><?= $content ?></div><textarea name="content"
                    id="content"
                    hidden><?= htmlspecialchars($content) ?></textarea><?php if (!empty($errors['content'])): ?><small
                        class="form-error"><?= htmlspecialchars($errors['content']) ?></small><?php endif; ?>
            </div>
        </div>
        <label class="publish-check"><input type="checkbox" name="is_published" <?= $published ? 'checked' : '' ?>> Xuất
            bản ngay (bỏ chọn để lưu bản nháp)</label>
        <div class="post-form-actions"><a class="btn btn-outline" href="<?= url('/admin/posts') ?>">Hủy</a><button
                class="btn btn-accent" type="submit"><?= $isEditing ? 'Lưu thay đổi' : 'Tạo bài viết' ?></button></div>
    </form>
</main>
<script>const editor = document.getElementById('editor'), contentInput = document.getElementById('content'); document.querySelectorAll('[data-command]').forEach(button => button.addEventListener('click', () => { editor.focus(); document.execCommand(button.dataset.command, false, button.dataset.value || null); contentInput.value = editor.innerHTML; })); document.getElementById('add-link').addEventListener('click', () => { const href = prompt('Nhập đường dẫn liên kết:'); if (href) { editor.focus(); document.execCommand('createLink', false, href); contentInput.value = editor.innerHTML; } }); editor.addEventListener('input', () => contentInput.value = editor.innerHTML); document.querySelector('.post-form').addEventListener('submit', () => contentInput.value = editor.innerHTML);</script>
<?php require_once __DIR__ . '/partials/footer.php'; ?>