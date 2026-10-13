<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Model;
use PDO;

class AdminOrderModel extends Model
{
    /**
     * Danh sách toàn bộ đơn hàng (kèm thông tin khách hàng và thanh toán)
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $sql = "
            SELECT o.*, u.full_name AS customer_name, u.email AS customer_email,
                   p.payment_method, p.payment_status AS p_status
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN payments p ON o.id = p.order_id
            ORDER BY o.created_at DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin đơn hàng theo ID
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sqlOrder = "
            SELECT o.*, u.full_name AS customer_name, u.email AS customer_email,
                   p.payment_method, p.payment_status AS p_status, p.transaction_id
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN payments p ON o.id = p.order_id
            WHERE o.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sqlOrder);
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        return $order ?: null;
    }

    /**
     * Lấy danh sách các mặt hàng trong đơn
     *
     * @return array<int, array<string, mixed>>
     */
    public function getItems(int $orderId): array
    {
        $sqlItems = "
            SELECT od.*, pv.image_url
            FROM order_details od
            LEFT JOIN product_variants pv ON od.variant_id = pv.id
            WHERE od.order_id = :id
        ";
        $stmtItems = $this->db->prepare($sqlItems);
        $stmtItems->execute([':id' => $orderId]);
        return $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn hàng và trạng thái thanh toán
     */
    public function updateStatus(int $id, string $orderStatus, string $paymentStatus): bool
    {
        $validStatuses = ['PENDING', 'CONFIRMED', 'PROCESSING', 'SHIPPING', 'DELIVERED', 'CANCELLED'];
        if (!in_array($orderStatus, $validStatuses, true)) {
            $orderStatus = 'PENDING';
        }

        // Cập nhật bảng orders
        $sql = "UPDATE orders SET order_status = :status, payment_status = :payment_status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':status' => $orderStatus,
            ':payment_status' => $paymentStatus,
            ':id' => $id,
        ]);

        // Cập nhật bảng payments
        $paymentFlag = ($paymentStatus === 'PAID') 
            ? 'SUCCESS' 
            : (($orderStatus === 'CANCELLED') ? 'CANCELLED' : 'PENDING');

        $sqlPay = "UPDATE payments SET payment_status = :p_status, updated_at = NOW() WHERE order_id = :id";
        $stmtPay = $this->db->prepare($sqlPay);
        $stmtPay->execute([
            ':p_status' => $paymentFlag,
            ':id' => $id,
        ]);

        return true;
    }
}
