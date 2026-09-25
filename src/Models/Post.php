<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Post extends Model
{
    public function getAllForAdmin(): array
    {
        $stmt = $this->db->query('SELECT * FROM posts ORDER BY created_at DESC');

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getLatestPosts($limit = 3)
    {
        $sql = "SELECT * FROM posts WHERE hidden_at IS NULL ORDER BY published_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getListPosts()
    {
        $sql = "SELECT * FROM posts WHERE hidden_at IS NULL ORDER BY published_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findBySlug(string $slug): array|false
    {
        $sql = "SELECT * FROM posts
                WHERE slug = :slug AND hidden_at IS NULL
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM posts WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM posts WHERE slug = :slug';
        $params = ['slug' => $slug];
        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $exceptId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO posts (author_id, title, slug, content, post_type, hidden_at, published_at, img_post)
             VALUES (:author_id, :title, :slug, :content, :post_type, :hidden_at, :published_at, :img_post)'
        );
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE posts SET title = :title, slug = :slug, content = :content, post_type = :post_type,
             hidden_at = :hidden_at, published_at = :published_at, img_post = :img_post WHERE id = :id'
        );
        $stmt->execute($data);
    }

    public function setHidden(int $id, bool $hidden): void
    {
        $stmt = $this->db->prepare(
            'UPDATE posts SET hidden_at = :hidden_at WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'hidden_at' => $hidden ? date('Y-m-d H:i:s') : null,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
