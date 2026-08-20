<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    public function getAll(): array
    {
        $sql = "
        SELECT
            p.id,
            p.name,
            p.slug,
            p.description,
            p.price,
            p.is_best_seller,

            pv.image_url

        FROM products p

        LEFT JOIN product_variants pv
            ON pv.product_id = p.id
            AND pv.is_primary = TRUE
            AND pv.hidden_at IS NULL

        WHERE p.hidden_at IS NULL

        ORDER BY p.created_at DESC
    ";

        $statement = $this->db->prepare($sql);

        $statement->execute();

        return $statement->fetchAll();
    }

}
