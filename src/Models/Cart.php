<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use Exception;
use PDO;

class Cart extends Model
{

    public function addToCart(int $userId, int $variantId, int $quantity): bool
    {
        $sql = "INSERT INTO shopping_cart (user_id, variant_id, quantity, created_at, updated_at)
                VALUES (:user_id, :variant_id, :quantity, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                    quantity = quantity + VALUES(quantity),
                    updated_at = NOW()";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            ':user_id' => $userId,
            ':variant_id' => $variantId,
            ':quantity' => $quantity,
        ]);
    }


    public function mergeSessionCartToDb(int $userId, array $sessionCart): bool
    {
        if (empty($sessionCart)) {
            return true;
        }

        $sql = "INSERT INTO shopping_cart (user_id, variant_id, quantity, created_at, updated_at)
                VALUES (:user_id, :variant_id, :quantity, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                    quantity = quantity + VALUES(quantity),
                    updated_at = NOW()";

        $statement = $this->db->prepare($sql);

        try {
            $this->db->beginTransaction();

            foreach ($sessionCart as $variantId => $quantity) {
                $statement->execute([
                    ':user_id' => $userId,
                    ':variant_id' => (int) $variantId,
                    ':quantity' => (int) $quantity,
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }


    public function getTotalQuantity(int $userId): int
    {
        $sql = "SELECT COALESCE(SUM(quantity), 0) FROM shopping_cart WHERE user_id = :user_id";
        $statement = $this->db->prepare($sql);
        $statement->execute([':user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }


    public function getCartByUserId(int $userId): array
    {
        $sql = "SELECT 
                    sc.id AS cart_id,
                    sc.variant_id,
                    sc.quantity,
                    pv.variant_name,
                    pv.color_code,
                    pv.stock_quantity,
                    COALESCE(pv.image_url, '') AS image_url,
                    p.id AS product_id,
                    p.name AS product_name,
                    p.slug AS product_slug,
                    p.price,
                    (p.price * sc.quantity) AS subtotal
                FROM shopping_cart sc
                JOIN product_variants pv ON sc.variant_id = pv.id
                JOIN products p ON pv.product_id = p.id
                WHERE sc.user_id = :user_id
                  AND p.hidden_at IS NULL
                  AND pv.hidden_at IS NULL
                ORDER BY sc.updated_at DESC";

        $statement = $this->db->prepare($sql);
        $statement->execute([':user_id' => $userId]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết thông tin sản phẩm từ danh sách mảng variant_id (dùng cho Session Cart)
     */
    public function getVariantsByIds(array $variantIds): array
    {
        if (empty($variantIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($variantIds), '?'));

        $sql = "SELECT 
                pv.id AS variant_id,
                pv.variant_name,
                pv.color_code,
                pv.stock_quantity,
                COALESCE(pv.image_url, '') AS image_url,
                p.id AS product_id,
                p.name AS product_name,
                p.slug AS product_slug,
                p.price
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            WHERE pv.id IN ($placeholders)
              AND p.hidden_at IS NULL
              AND pv.hidden_at IS NULL";

        $statement = $this->db->prepare($sql);
        $statement->execute(array_values($variantIds));

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateQuantity(int $userId, int $variantId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $variantId);
        }

        $sql = "UPDATE shopping_cart 
                SET quantity = :quantity, updated_at = NOW() 
                WHERE user_id = :user_id AND variant_id = :variant_id";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            ':quantity' => $quantity,
            ':user_id' => $userId,
            ':variant_id' => $variantId,
        ]);
    }

    /**
     * Tăng số lượng sản phẩm trong giỏ hàng
     */
    public function incrementQuantity(int $userId, int $variantId, int $step = 1): bool
    {
        $sql = "UPDATE shopping_cart 
                SET quantity = quantity + :step 
                WHERE user_id = :user_id AND variant_id = :variant_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':step' => $step,
            ':user_id' => $userId,
            ':variant_id' => $variantId,
        ]);
    }

    /**
     * Giảm số lượng sản phẩm (Đảm bảo tối thiểu là 1, không giảm xuống 0)
     */
    public function decrementQuantity(int $userId, int $variantId, int $step = 1): bool
    {
        $sql = "UPDATE shopping_cart 
                SET quantity = GREATEST(1, quantity - :step) 
                WHERE user_id = :user_id AND variant_id = :variant_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':step' => $step,
            ':user_id' => $userId,
            ':variant_id' => $variantId,
        ]);
    }

    public function removeItem(int $userId, int $variantId): bool
    {
        $sql = "DELETE FROM shopping_cart WHERE user_id = :user_id AND variant_id = :variant_id";
        $statement = $this->db->prepare($sql);

        return $statement->execute([
            ':user_id' => $userId,
            ':variant_id' => $variantId,
        ]);
    }
    public function clearCart(int $userId): bool
    {
        $sql = "DELETE FROM shopping_cart WHERE user_id = :user_id";
        $statement = $this->db->prepare($sql);

        return $statement->execute([':user_id' => $userId]);
    }
}