<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Product extends Model
{

    public function getCategorys(): array
    {
        $sql = 'SELECT * from categories
        WHERE hidden_at IS NULL ORDER BY name ASC';
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getBrands(): array
    {
        $sql = 'SELECT * from brands WHERE hidden_at IS NULL ORDER BY name ASC';
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    private function buildFilter(array $filters): array
    {
        $where = [
            'p.hidden_at IS NULL'
        ];

        $params = [];

        // Category Slug
        if (!empty($filters['category_slug'])) {
            $where[] = 'c.slug = :category_slug';

            $params['category_slug'] = [
                'value' => (string) $filters['category_slug'],
                'type' => \PDO::PARAM_STR
            ];
        }

        // Brand Slug
        if (!empty($filters['brand_slug'])) {
            $where[] = 'b.slug = :brand_slug';

            $params['brand_slug'] = [
                'value' => (string) $filters['brand_slug'],
                'type' => \PDO::PARAM_STR
            ];
        }

        if (!empty($filters['price_range'])) {
            match ($filters['price_range']) {
                '0-50000' => $where[] = 'p.price BETWEEN 0 AND 50000',
                '50000-100000' => $where[] = 'p.price BETWEEN 50000 AND 100000',
                '100000-200000' => $where[] = 'p.price BETWEEN 100000 AND 200000',
                '200000-up' => $where[] = 'p.price >= 200000',
                default => null,
            };
        }

        if (isset($filters['keyword']) && trim($filters['keyword']) !== '') {
            $where[] = 'p.name LIKE :keyword';

            $params['keyword'] = [
                'value' => '%' . trim($filters['keyword']) . '%',
                'type' => \PDO::PARAM_STR
            ];
        }

        return [
            'where' => implode(' AND ', $where),
            'params' => $params
        ];
    }

    private function buildOrder(string $sort): string
    {
        return match ($sort) {
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'oldest' => 'p.created_at ASC',
            'newest' => 'p.created_at DESC',
            'bestseller' => 'total_sold DESC',
            default => 'p.created_at DESC',
        };
    }

    private function bindParams(\PDOStatement $statement, array $params): void
    {
        foreach ($params as $name => $param) {
            $statement->bindValue(
                ':' . $name,
                $param['value'],
                $param['type']
            );
        }
    }

    public function getPaginated(
        array $filters,
        int $page = 1,
        int $limit = 12
    ): array {
        $page = max(1, $page);
        $offset = ($page - 1) * $limit;

        $filter = $this->buildFilter($filters);
        $order = $this->buildOrder($filters['sort'] ?? 'newest');

        $sql = "
            SELECT
                p.id,
                p.name,
                p.slug,
                p.description,
                p.price,
                p.is_best_seller,
                p.category_id,
                p.brand_id,

                pv.id AS variant_id,
                pv.variant_name,
                pv.color_code,
                pv.stock_quantity,
                pv.image_url,
                pv.sold AS variant_sold,
                (SELECT COALESCE(SUM(sold), 0) FROM product_variants WHERE product_id = p.id AND hidden_at IS NULL) AS total_sold

            FROM products p

            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN brands b ON b.id = p.brand_id

            LEFT JOIN product_variants pv
                ON pv.product_id = p.id
                AND pv.hidden_at IS NULL
                AND pv.is_primary = 1

            WHERE {$filter['where']}

            ORDER BY {$order}

            LIMIT :limit OFFSET :offset
        ";

        $statement = $this->db->prepare($sql);

        $this->bindParams($statement, $filter['params']);

        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll();
    }

    public function count(array $filters): int
    {
        $filter = $this->buildFilter($filters);

        $sql = "
            SELECT COUNT(*)
            FROM products p

            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN brands b ON b.id = p.brand_id

            WHERE {$filter['where']}
        ";

        $statement = $this->db->prepare($sql);

        $this->bindParams($statement, $filter['params']);

        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    public function bestSeller(int $limit = 4): array
    {
        $sql = "SELECT 
                p.*, 
                pv_primary.image_url,
                COALESCE(SUM(pv.sold), 0) AS total_sold
            FROM products p
            LEFT JOIN product_variants pv 
                   ON p.id = pv.product_id
            LEFT JOIN product_variants pv_primary 
                   ON p.id = pv_primary.product_id AND pv_primary.is_primary = 1
            WHERE p.hidden_at IS NULL
            GROUP BY p.id, pv_primary.image_url
            ORDER BY total_sold DESC, p.id DESC
            LIMIT :limit";

        $statement = $this->db->prepare($sql);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function productsNew(int $limit = 5): array
    {
        $sql = "SELECT p.*, pv.image_url,
                (SELECT COALESCE(SUM(sold), 0) FROM product_variants WHERE product_id = p.id AND hidden_at IS NULL) AS total_sold
            FROM products p
            LEFT JOIN product_variants pv 
                ON p.id = pv.product_id 
                AND pv.is_primary = 1
            WHERE p.hidden_at IS NULL
            ORDER BY p.created_at DESC
            LIMIT :limit";

        $statement = $this->db->prepare($sql);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT
                p.*,
                pv.image_url,
                b.name AS brand_name,
                c.name AS category_name,
                (SELECT COALESCE(SUM(sold), 0) FROM product_variants WHERE product_id = p.id AND hidden_at IS NULL) AS total_sold
            FROM products p
            LEFT JOIN product_variants pv
                ON p.id = pv.product_id
                AND pv.is_primary = 1
            LEFT JOIN brands b
                ON b.id = p.brand_id
            LEFT JOIN categories c
                ON c.id = p.category_id
            WHERE p.slug = :slug
                AND p.hidden_at IS NULL
            LIMIT 1";

        $statement = $this->db->prepare($sql);

        $statement->bindValue(
            ':slug',
            $slug,
            \PDO::PARAM_STR
        );

        $statement->execute();

        $product = $statement->fetch(\PDO::FETCH_ASSOC);

        return $product ?: null;
    }

    public function getVariantsByProductId(int $productId): array
    {
        $sql = "SELECT 
                id, 
                variant_name, 
                color_code, 
                stock_quantity, 
                sold,
                image_url, 
                is_primary 
            FROM product_variants 
            WHERE product_id = :product_id 
              AND hidden_at IS NULL 
            ORDER BY is_primary DESC, id ASC";

        $statement = $this->db->prepare($sql);
        $statement->bindValue(':product_id', $productId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}