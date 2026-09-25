<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Post;
class PostControllerAdmin extends Controller
{
    public function posts(): void
    {
        $this->view('admin/posts', [
            'pageCss' => 'admin',
            'posts' => (new Post())->getAllForAdmin(),
            'success' => $this->flash('post_success'),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function createPost(): void
    {
        $this->showPostForm();
    }

    public function editPost(string $id): void
    {
        $post = (new Post())->findById((int) $id);
        if ($post === false) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        $this->showPostForm($post);
    }

    public function deletePost(string $id): void
    {
        $this->verifyCsrf();
        $post = $this->findPostOr404($id);
        if ($post === null) {
            return;
        }

        (new Post())->delete((int) $post['id']);
        $this->setFlash('post_success', 'Đã xóa bài viết.');
        $this->redirect(url('/admin/posts'));
    }

    public function toggleHidden(string $id): void
    {
        $this->verifyCsrf();
        $post = $this->findPostOr404($id);
        if ($post === null) {
            return;
        }

        $isHidden = !empty($post['hidden_at']);
        (new Post())->setHidden((int) $post['id'], !$isHidden);
        $this->setFlash('post_success', $isHidden ? 'Đã hiện bài viết.' : 'Đã ẩn bài viết.');
        $this->redirect(url('/admin/posts'));
    }

    public function storePost(): void
    {
        $this->savePost();
    }

    public function updatePost(string $id): void
    {
        $this->savePost((int) $id);
    }

    /** @param array<string, mixed>|null $post */
    private function showPostForm(?array $post = null): void
    {
        $this->view('admin/post_form', [
            'pageCss' => 'admin',
            'post' => $post,
            'csrfToken' => $this->csrfToken(),
            'errors' => $this->flash('post_errors') ?? [],
            'old' => $this->flash('post_old') ?? [],
        ]);
    }

    private function savePost(?int $id = null): void
    {
        $this->verifyCsrf();

        $existing = $id ? (new Post())->findById($id) : null;
        if ($id !== null && $existing === false) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $content = $this->sanitizeHtml((string) ($_POST['content'] ?? ''));
        $postType = (string) ($_POST['post_type'] ?? 'BLOG');
        $isPublished = isset($_POST['is_published']);
        $errors = [];
        if ($title === '') {
            $errors['title'] = 'Vui lòng nhập tiêu đề bài viết.';
        }
        if (trim(strip_tags($content)) === '') {
            $errors['content'] = 'Nội dung bài viết không được để trống.';
        }
        if (!in_array($postType, ['ABOUT', 'BLOG', 'NEWS'], true)) {
            $postType = 'BLOG';
        }

        $postModel = new Post();
        $slug = $this->uniqueSlug($title, $postModel, $id);
        $image = is_array($existing) ? (string) ($existing['img_post'] ?? '') : '';
        if (!$errors && isset($_FILES['img_post']) && ($_FILES['img_post']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            try {
                $image = $this->storeImage($_FILES['img_post']);
            } catch (\RuntimeException $exception) {
                $errors['img_post'] = $exception->getMessage();
            }
        }

        if ($errors) {
            $this->setFlash('post_errors', $errors);
            $this->setFlash('post_old', $_POST);
            $this->redirect(url($id ? '/admin/posts/' . $id . '/edit' : '/admin/posts/create'));
        }

        $now = date('Y-m-d H:i:s');
        $data = [
            'author_id' => $_SESSION['user_id'] ?? null,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'post_type' => $postType,
            'hidden_at' => $isPublished ? null : $now,
            'published_at' => $isPublished ? ((is_array($existing) && !empty($existing['published_at'])) ? $existing['published_at'] : $now) : null,
            'img_post' => $image,
        ];
        if ($id === null) {
            $postModel->create($data);
        } else {
            unset($data['author_id']);
            $postModel->update($id, $data);
        }
        $this->setFlash('post_success', $id === null ? 'Đã tạo bài viết.' : 'Đã cập nhật bài viết.');
        $this->redirect(url('/admin/posts'));
    }

    private function uniqueSlug(string $title, Post $postModel, ?int $id): string
    {
        $slug = strtolower(trim((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title)));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-') ?: 'bai-viet';
        $base = $slug;
        $suffix = 2;
        while ($postModel->slugExists($slug, $id)) {
            $slug = $base . '-' . $suffix++;
        }
        return $slug;
    }

    /** @param array<string, mixed> $file */
    private function storeImage(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
            throw new \RuntimeException('Không thể tải ảnh lên. Vui lòng thử lại.');
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException('Ảnh đại diện tối đa 5 MB.');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensions[$mime])) {
            throw new \RuntimeException('Chỉ hỗ trợ ảnh JPG, PNG hoặc WebP.');
        }
        $directory = dirname(__DIR__, 3) . '/public/images/posts';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException('Không thể tạo thư mục lưu ảnh.');
        }
        $filename = 'post-' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
        if (!move_uploaded_file((string) $file['tmp_name'], $directory . '/' . $filename)) {
            throw new \RuntimeException('Không thể lưu ảnh đại diện.');
        }
        return 'images/posts/' . $filename;
    }

    private function sanitizeHtml(string $html): string
    {
        $allowed = '<p><br><h2><h3><h4><strong><b><em><i><u><s><ul><ol><li><blockquote><a><img>';
        $html = strip_tags($html, $allowed);
        $html = preg_replace('/\s(?:on\w+|style)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        $html = preg_replace('/(?:javascript|data):/i', '', $html) ?? '';
        return trim($html);
    }

    private function csrfToken(): string
    {
        return $_SESSION['post_csrf_token'] ??= bin2hex(random_bytes(32));
    }

    private function verifyCsrf(): void
    {
        if (!hash_equals($this->csrfToken(), (string) ($_POST['_token'] ?? ''))) {
            http_response_code(419);
            exit('Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.');
        }
    }

    /** @return array<string, mixed>|null */
    private function findPostOr404(string $id): ?array
    {
        if (!ctype_digit($id) || (int) $id < 1) {
            http_response_code(404);
            $this->view('errors/404');
            return null;
        }

        $post = (new Post())->findById((int) $id);
        if ($post === false) {
            http_response_code(404);
            $this->view('errors/404');
            return null;
        }

        return $post;
    }

    private function setFlash(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    private function flash(string $key): mixed
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }
}
