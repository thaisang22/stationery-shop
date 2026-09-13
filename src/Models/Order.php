<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use Exception;
use PDO;

class Order extends Model
{
    public function createOrder(array $orderData, array $cartItems, string $paymentMethod): int
    {
        try {
            $this->db->beginTransaction();
            $sqlOrder = "INSERT INTO orders (user_id, total_amount, recipient_name, recipient_phone, shipping_address, order_status, payment_status)
                         VALUES (:user_id, :total_amount, :recipient_name, :recipient_phone, :shipping_address, 'PENDING', 'UNPAID')";
            
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([
                ':user_id'          => $orderData['user_id'],
                ':total_amount'     => $orderData['total_amount'],
                ':recipient_name'   => $orderData['recipient_name'],
                ':recipient_phone'  => $orderData['recipient_phone'],
                ':shipping_address' => $orderData['shipping_address'],
            ]);

            $orderId = (int) $this->db->lastInsertId();
            $sqlDetail = "INSERT INTO order_details (order_id, variant_id, product_name, variant_name, quantity, unit_price, sub_total)
                          VALUES (:order_id, :variant_id, :product_name, :variant_name, :quantity, :unit_price, :sub_total)";
            $stmtDetail = $this->db->prepare($sqlDetail);
            $sqlStock = "UPDATE product_variants 
                         SET stock_quantity = stock_quantity - :sub_qty, 
                             sold = sold + :add_sold 
                         WHERE id = :variant_id AND stock_quantity >= :check_stock";
            $stmtStock = $this->db->prepare($sqlStock);

            foreach ($cartItems as $item) {
                $variantId   = (int) $item['variant_id'];
                $qty         = (int) ($item['quantity'] ?? 1);
                $unitPrice   = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
                $subTotal    = (float) ($item['subtotal'] ?? ($unitPrice * $qty));
                $productName = $item['product_name'] ?? $item['name'] ?? 'Sản phẩm';
                $variantName = $item['variant_name'] ?? 'Mặc định';
                $stmtStock->execute([
                    ':sub_qty'     => $qty,
                    ':add_sold'    => $qty,
                    ':variant_id'  => $variantId,
                    ':check_stock' => $qty,
                ]);
                $stmtDetail->execute([
                    ':order_id'     => $orderId,
                    ':variant_id'   => $variantId,
                    ':product_name' => $productName,
                    ':variant_name' => $variantName,
                    ':quantity'     => $qty,
                    ':unit_price'   => $unitPrice,
                    ':sub_total'    => $subTotal,
                ]);
            }
            $sqlPayment = "INSERT INTO payments (order_id, payment_method, amount, payment_status)
                           VALUES (:order_id, :payment_method, :amount, 'PENDING')";
            $stmtPayment = $this->db->prepare($sqlPayment);
            $stmtPayment->execute([
                ':order_id'       => $orderId,
                ':payment_method' => strtoupper($paymentMethod),
                ':amount'         => $orderData['total_amount'],
            ]);
            $sqlClearCart = "DELETE FROM shopping_cart WHERE user_id = :user_id";
            $stmtClearCart = $this->db->prepare($sqlClearCart);
            $stmtClearCart->execute([':user_id' => $orderData['user_id']]);

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getOrderWithDetails(int $orderId, int $userId): ?array
    {
        $sql = "SELECT o.*, p.payment_method, p.payment_status AS p_status 
                FROM orders o
                LEFT JOIN payments p ON o.id = p.order_id
                WHERE o.id = :order_id AND o.user_id = :user_id LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            return null;
        }

        $sqlDetails = "SELECT * FROM order_details WHERE order_id = :order_id";
        $stmtDetails = $this->db->prepare($sqlDetails);
        $stmtDetails->execute([':order_id' => $orderId]);
        $order['items'] = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }
}