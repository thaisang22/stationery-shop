<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Model;
use PDO;
use Throwable;

class AdminProductModel extends Model
{
    /**
     * Xây dựng mệnh đề WHERE và params lọc sản phẩm cho admin
     *
     * @param array<string, mixed> $filters
     * @return array{where: string, params: array<string, mixed>}
     */
    private function buildFilter(array $filters): array
    {
        $where = ['p.hidden_at IS NULL'];
        $params = [];

        // 1. Keyword (tìm theo tên sản phẩm)
        if (!empty($filters['keyword'])) {
            $where[] = 'p.name LIKE :keyword';
            $params[':keyword'] = '%' . trim((string)$filters['keyword']) . '%';
        }

        // 2. Category ID hoặc Category Slug
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :category_id';
            $params[':category_id'] = (int)$filters['category_id'];
        } elseif (!empty($filters['category_slug'])) {
            $where[] = 'c.slug = :category_slug';
            $params[':category_slug'] = (string)$filters['category_slug'];
        }

        // 3. Brand ID hoặc Brand Slug
        if (!empty($filters['brand_id'])) {
            $where[] = 'p.brand_id = :brand_id';
            $params[':brand_id'] = (int)$filters['brand_id'];
        } elseif (!empty($filters['brand_slug'])) {
            $where[] = 'b.slug = :brand_slug';
            $params[':brand_slug'] = (string)$filters['brand_slug'];
        }

        // 4. Khoảng giá (Price range)
        if (!empty($filters['price_range'])) {
            match ($filters['price_range']) {
                '0-50000' => $where[] = 'p.price BETWEEN 0 AND 50000',
                '50000-100000' => $where[] = 'p.price BETWEEN 50000 AND 100000',
                '100000-200000' => $where[] = 'p.price BETWEEN 100000 AND 200000',
                '200000-up' => $where[] = 'p.price >= 200000',
                default => null,
            };
        }

        // 5. Tình trạng tồn kho (Stock status)
        if (!empty($filters['stock_status'])) {
            match ($filters['stock_status']) {
                'out' => $where[] = 'COALESCE(pv.stock_quantity, 0) = 0',
                'low' => $where[] = 'COALESCE(pv.stock_quantity, 0) > 0 AND COALESCE(pv.stock_quantity, 0) <= 5',
                'in' => $where[] = 'COALESCE(pv.stock_quantity, 0) > 0',
                default => null,
            };
        }

        return [
            'where' => implode(' AND ', $where),
            'params' => $params,
        ];
    }

    private function buildOrder(string $sort): string
    {
        return match ($sort) {
            'oldest' => 'p.id ASC',
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'stock_asc' => 'pv.stock_quantity ASC',
            'stock_desc' => 'pv.stock_quantity DESC',
            'bestseller' => 'sold_count DESC',
            default => 'p.id DESC',
        };
    }

    /**
     * Lấy danh sách sản phẩm theo bộ lọc và phân trang
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $filter = $this->buildFilter($filters);
        $order = $this->buildOrder((string)($filters['sort'] ?? 'newest'));

        $sql = "
            SELECT p.*, c.name AS category_name, b.name AS brand_name,
                   pv.id AS variant_id, pv.stock_quantity, pv.image_url,
                   COALESCE(pv.sold, 0) AS sold_count
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_primary = 1
            WHERE {$filter['where']}
            ORDER BY {$order}
        ";

        if ($limit > 0) {
            $offset = max(0, ($page - 1) * $limit);
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        foreach ($filter['params'] as $name => $value) {
            $stmt->bindValue($name, $value);
        }

        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số sản phẩm khớp bộ lọc (phục vụ phân trang)
     *
     * @param array<string, mixed> $filters
     */
    public function count(array $filters = []): int
    {
        $filter = $this->buildFilter($filters);

        $sql = "
            SELECT COUNT(DISTINCT p.id)
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_primary = 1
            WHERE {$filter['where']}
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($filter['params'] as $name => $value) {
            $stmt->bindValue($name, $value);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Tìm thông tin sản phẩm và biến thể chính theo ID
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT p.*, pv.stock_quantity, pv.image_url
            FROM products p
            LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_primary = 1
            WHERE p.id = :id AND p.hidden_at IS NULL
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product ?: null;
    }

    /**
     * Lấy danh sách danh mục đang hoạt động
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllCategories(): array
    {
        return $this->db->query("
            SELECT * FROM categories WHERE hidden_at IS NULL ORDER BY name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách thương hiệu đang hoạt động
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllBrands(): array
    {
        return $this->db->query("
            SELECT * FROM brands WHERE hidden_at IS NULL ORDER BY name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra xem slug sản phẩm đã tồn tại hay chưa
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM products WHERE slug = :slug";
        $params = [':slug' => $slug];
        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Thêm mới sản phẩm và biến thể chính mặc định (Transaction)
     *
     * @param array<string, mixed> $productData
     */
    public function create(array $productData, int $stock, string $imageUrl): int
    {
        try {
            $this->db->beginTransaction();

            $sqlProduct = "INSERT INTO products (category_id, brand_id, name, slug, description, price, is_best_seller, created_at, updated_at)
                           VALUES (:category_id, :brand_id, :name, :slug, :description, :price, :is_best_seller, NOW(), NOW())";
            $stmt = $this->db->prepare($sqlProduct);
            $stmt->execute([
                ':category_id' => $productData['category_id'],
                ':brand_id' => $productData['brand_id'],
                ':name' => $productData['name'],
                ':slug' => $productData['slug'],
                ':description' => $productData['description'],
                ':price' => $productData['price'],
                ':is_best_seller' => $productData['is_best_seller'],
            ]);
            $productId = (int) $this->db->lastInsertId();

            $sqlVariant = "INSERT INTO product_variants (product_id, variant_name, color_code, stock_quantity, sold, image_url, is_primary, created_at, updated_at)
                           VALUES (:product_id, 'Mặc định', NULL, :stock, 0, :image_url, 1, NOW(), NOW())";
            $stmtVariant = $this->db->prepare($sqlVariant);
            $stmtVariant->execute([
                ':product_id' => $productId,
                ':stock' => $stock,
                ':image_url' => $imageUrl,
            ]);

            $this->db->commit();
            return $productId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Cập nhật thông tin sản phẩm và biến thể chính (Transaction)
     *
     * @param array<string, mixed> $productData
     */
    public function update(int $id, array $productData, int $stock, string $imageUrl): bool
    {
        try {
            $this->db->beginTransaction();

            $sqlProduct = "UPDATE products 
                           SET category_id = :category_id, brand_id = :brand_id, name = :name,
                               description = :description, price = :price, is_best_seller = :is_best_seller, updated_at = NOW()
                           WHERE id = :id";
            $stmt = $this->db->prepare($sqlProduct);
            $stmt->execute([
                ':category_id' => $productData['category_id'],
                ':brand_id' => $productData['brand_id'],
                ':name' => $productData['name'],
                ':description' => $productData['description'],
                ':price' => $productData['price'],
                ':is_best_seller' => $productData['is_best_seller'],
                ':id' => $id,
            ]);

            $sqlVariant = "UPDATE product_variants 
                           SET stock_quantity = :stock, updated_at = NOW()" .
                           ($imageUrl !== '' ? ", image_url = :image_url" : "") .
                           " WHERE product_id = :product_id AND is_primary = 1";
            $stmtVariant = $this->db->prepare($sqlVariant);
            $paramsVariant = [
                ':stock' => $stock,
                ':product_id' => $id,
            ];
            if ($imageUrl !== '') {
                $paramsVariant[':image_url'] = $imageUrl;
            }
            $stmtVariant->execute($paramsVariant);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Ẩn sản phẩm và các biến thể liên quan (Xóa mềm)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET hidden_at = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $stmtVariant = $this->db->prepare("UPDATE product_variants SET hidden_at = NOW() WHERE product_id = :id");
        $stmtVariant->execute([':id' => $id]);

        return true;
    }
}
