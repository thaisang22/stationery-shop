<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Model;
use PDO;

class AdminCategoryModel extends Model
{
    /**
     * Lấy danh sách danh mục cùng số lượng sản phẩm đang có trong mỗi danh mục
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllWithProductCount(): array
    {
        $sql = "
            SELECT c.*, 
                   (SELECT COUNT(*) FROM products WHERE category_id = c.id AND hidden_at IS NULL) AS product_count
            FROM categories c
            WHERE c.hidden_at IS NULL
            ORDER BY c.id ASC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
