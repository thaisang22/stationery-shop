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
            p.is_best_seller,

            MIN(pv.price) AS price,

            pi.image_url

        FROM Products p

        LEFT JOIN Product_Variants pv
            ON pv.product_id = p.id
            AND pv.hidden_at IS NULL

        LEFT JOIN Product_Images pi
            ON pi.product_id = p.id
            AND pi.is_primary = TRUE

        WHERE p.hidden_at IS NULL

        GROUP BY
            p.id,
            p.name,
            p.slug,
            p.description,
            p.is_best_seller,
            pi.image_url

        ORDER BY p.created_at DESC
    ";

        $statement = $this->db->prepare($sql);

        $statement->execute();

        return $statement->fetchAll();
    }

}
