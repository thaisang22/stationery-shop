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
                ':user_id' => $orderData['user_id'],
                ':total_amount' => $orderData['total_amount'],
                ':recipient_name' => $orderData['recipient_name'],
                ':recipient_phone' => $orderData['recipient_phone'],
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
                $variantId = (int) $item['variant_id'];
                $qty = (int) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
                $subTotal = (float) ($item['subtotal'] ?? ($unitPrice * $qty));
                $productName = $item['product_name'] ?? $item['name'] ?? 'Sản phẩm';
                $variantName = $item['variant_name'] ?? 'Mặc định';
                $stmtStock->execute([
                    ':sub_qty' => $qty,
                    ':add_sold' => $qty,
                    ':variant_id' => $variantId,
                    ':check_stock' => $qty,
                ]);
                $stmtDetail->execute([
                    ':order_id' => $orderId,
                    ':variant_id' => $variantId,
                    ':product_name' => $productName,
                    ':variant_name' => $variantName,
                    ':quantity' => $qty,
                    ':unit_price' => $unitPrice,
                    ':sub_total' => $subTotal,
                ]);
            }
            $sqlPayment = "INSERT INTO payments (order_id, payment_method, amount, payment_status)
                           VALUES (:order_id, :payment_method, :amount, 'PENDING')";
            $stmtPayment = $this->db->prepare($sqlPayment);
            $stmtPayment->execute([
                ':order_id' => $orderId,
                ':payment_method' => strtoupper($paymentMethod),
                ':amount' => $orderData['total_amount'],
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

    public function getOrdersByUserId(int $userId): array
    {
        $sql = "SELECT o.*, p.payment_method, p.payment_status AS p_status
                FROM orders o
                LEFT JOIN payments p ON o.id = p.order_id
                WHERE o.user_id = :user_id
                ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) {
            return [];
        }

        // Fetch items for each order
        $sqlDetails = "SELECT * FROM order_details WHERE order_id = :order_id";
        $stmtDetails = $this->db->prepare($sqlDetails);

        foreach ($orders as &$order) {
            $stmtDetails->execute([':order_id' => $order['id']]);
            $order['items'] = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);
        }

        return $orders;
    }

    public function markAsPaid(int $orderId, string $transactionId, array $gatewayResponse = []): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Update orders table
            $sqlOrder = "UPDATE orders 
                         SET payment_status = 'PAID', 
                             order_status = 'CONFIRMED', 
                             updated_at = NOW() 
                         WHERE id = :id AND payment_status != 'PAID' AND order_status != 'CANCELLED'";
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([':id' => $orderId]);

            if ($stmtOrder->rowCount() === 0) {
                // Đơn hàng đã bị hủy hoặc đã thanh toán trước đó
                $this->db->rollBack();
                return false;
            }

            // 2. Update payments table
            $sqlPayment = "UPDATE payments 
                           SET payment_status = 'SUCCESS', 
                               transaction_id = :tx_id, 
                               gateway_response = :response, 
                               updated_at = NOW() 
                           WHERE order_id = :order_id";
            $stmtPayment = $this->db->prepare($sqlPayment);
            $stmtPayment->execute([
                ':tx_id' => $transactionId,
                ':response' => !empty($gatewayResponse) ? json_encode($gatewayResponse, JSON_UNESCAPED_UNICODE) : null,
                ':order_id' => $orderId,
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Failed to mark Order #{$orderId} as PAID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hủy đơn hàng và hoàn trả tồn kho sản phẩm (Rollback stock)
     */
    public function cancelOrder(int $orderId, ?int $userId = null, string $reason = ''): bool
    {
        try {
            $this->db->beginTransaction();

            $sqlCheck = "SELECT id, user_id, order_status, payment_status FROM orders WHERE id = :id FOR UPDATE";
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute([':id' => $orderId]);
            $order = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                $this->db->rollBack();
                return false;
            }

            // Nếu truyền userId, đảm bảo đơn hàng đúng chủ sở hữu
            if ($userId !== null && (int) $order['user_id'] !== $userId) {
                $this->db->rollBack();
                return false;
            }

            // Chỉ cho phép hủy nếu đơn hàng đang ở trạng thái PENDING và chưa thanh toán
            if ($order['order_status'] !== 'PENDING' || $order['payment_status'] === 'PAID') {
                $this->db->rollBack();
                return false;
            }

            // 1. Lấy danh sách sản phẩm trong đơn để hoàn trả kho (Rollback stock)
            $sqlItems = "SELECT variant_id, quantity FROM order_details WHERE order_id = :order_id";
            $stmtItems = $this->db->prepare($sqlItems);
            $stmtItems->execute([':order_id' => $orderId]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $sqlRestoreStock = "UPDATE product_variants 
                                SET stock_quantity = stock_quantity + :qty, 
                                    sold = GREATEST(0, sold - :sold_qty) 
                                WHERE id = :variant_id";
            $stmtRestore = $this->db->prepare($sqlRestoreStock);

            foreach ($items as $item) {
                $variantId = (int) $item['variant_id'];
                $qty = (int) $item['quantity'];

                $stmtRestore->execute([
                    ':qty' => $qty,
                    ':sold_qty' => $qty,
                    ':variant_id' => $variantId,
                ]);
            }

            // 2. Cập nhật trạng thái đơn hàng sang CANCELLED
            $sqlOrder = "UPDATE orders 
                         SET order_status = 'CANCELLED', 
                             updated_at = NOW() 
                         WHERE id = :id";
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([':id' => $orderId]);

            // 3. Cập nhật thanh toán sang CANCELLED
            $sqlPayment = "UPDATE payments 
                           SET payment_status = 'CANCELLED', 
                               updated_at = NOW() 
                           WHERE order_id = :order_id";
            $stmtPayment = $this->db->prepare($sqlPayment);
            $stmtPayment->execute([':order_id' => $orderId]);

            $this->db->commit();
            error_log("Order #{$orderId} has been CANCELLED. Reason: " . ($reason ?: 'None'));
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Failed to cancel Order #{$orderId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra thời hạn thanh toán của đơn hàng (Mặc định 15 phút)
     * Tự động hủy và hoàn trả kho nếu quá hạn.
     */
    public function checkAndExpireOrder(int $orderId, int $timeoutMinutes = 15): array
    {
        $order = $this->findById($orderId);
        if (!$order) {
            return [
                'exists' => false,
                'is_paid' => false,
                'is_cancelled' => false,
                'is_expired' => false,
                'remaining_seconds' => 0,
            ];
        }

        $isPaid = strtoupper((string) ($order['payment_status'] ?? '')) === 'PAID';
        $isCancelled = strtoupper((string) ($order['order_status'] ?? '')) === 'CANCELLED';

        if ($isPaid) {
            return [
                'exists' => true,
                'is_paid' => true,
                'is_cancelled' => false,
                'is_expired' => false,
                'remaining_seconds' => 0,
            ];
        }

        if ($isCancelled) {
            return [
                'exists' => true,
                'is_paid' => false,
                'is_cancelled' => true,
                'is_expired' => true,
                'remaining_seconds' => 0,
            ];
        }

        $createdAt = strtotime((string) $order['created_at']);
        $timeoutSeconds = $timeoutMinutes * 60;
        $elapsed = max(0, time() - $createdAt);
        $remainingSeconds = min($timeoutSeconds, max(0, $timeoutSeconds - $elapsed));

        if ($elapsed >= $timeoutSeconds) {
            // Đã quá thời hạn thanh toán -> Tự động hủy đơn và nhả tồn kho
            $this->cancelOrder($orderId, null, 'Hết thời hạn thanh toán (' . $timeoutMinutes . ' phút)');
            return [
                'exists' => true,
                'is_paid' => false,
                'is_cancelled' => true,
                'is_expired' => true,
                'remaining_seconds' => 0,
            ];
        }

        return [
            'exists' => true,
            'is_paid' => false,
            'is_cancelled' => false,
            'is_expired' => false,
            'remaining_seconds' => $remainingSeconds,
        ];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM orders
             WHERE id = :id
             LIMIT 1"
        );

        $stmt->execute([
            'id' => $id
        ]);

        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $order ?: null;
    }
}

