<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    /**
     * Lấy danh sách tất cả danh mục đang hoạt động
     *
     * @return array
     */
    public function getAllActive(): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM categories WHERE hidden_at IS NULL ORDER BY name ASC'
        );
        $statement->execute();

        return $statement->fetchAll();
    }
}
