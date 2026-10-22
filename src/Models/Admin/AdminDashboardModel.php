<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Model;
use PDO;

class AdminDashboardModel extends Model
{
    /**
     * Doanh thu hôm nay (chỉ tính đơn đã thanh toán và không bị hủy)
     */
    public function getTodayRevenue(): float
    {
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total_amount), 0) 
            FROM orders 
            WHERE DATE(created_at) = CURDATE() 
              AND order_status != 'CANCELLED' 
              AND payment_status = 'PAID'
        ");
        return (float) $stmt->fetchColumn();
    }

    /**
     * Tổng số lượng đơn hàng toàn hệ thống
     */
    public function getTotalOrders(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM orders");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Tổng số lượng tài khoản khách hàng
     */
    public function getTotalCustomers(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'CUSTOMER'");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Tổng số sản phẩm đang kinh doanh
     */
    public function getTotalProducts(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM products WHERE hidden_at IS NULL");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Danh sách đơn hàng mới nhất
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRecentOrders(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT o.*, u.full_name AS customer_name, p.payment_method
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN payments p ON o.id = p.order_id
            ORDER BY o.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
