<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Model;
use PDO;

class AdminSalesModel extends Model
{
    /**
     * Thống kê tổng quan doanh thu, số đơn, số mặt hàng đã bán
     *
     * @return array<string, mixed>
     */
    public function getSummary(): array
    {
        $res = $this->db->query("
            SELECT 
                COALESCE(SUM(o.total_amount), 0) AS total_revenue,
                COUNT(DISTINCT o.id) AS total_orders,
                COALESCE(SUM(od.quantity), 0) AS total_items
            FROM orders o
            LEFT JOIN order_details od ON o.id = od.order_id
            WHERE o.order_status != 'CANCELLED'
        ")->fetch(PDO::FETCH_ASSOC);

        return $res ?: ['total_revenue' => 0, 'total_orders' => 0, 'total_items' => 0];
    }

    /**
     * Thống kê doanh thu và đơn hàng theo Tháng
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMonthlyStats(): array
    {
        return $this->db->query("
            SELECT 
                YEAR(o.created_at) AS year,
                MONTH(o.created_at) AS month,
                COUNT(DISTINCT o.id) AS order_count,
                COALESCE(SUM(od.quantity), 0) AS item_count,
                COALESCE(SUM(od.sub_total), 0) AS revenue
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE o.order_status != 'CANCELLED'
            GROUP BY YEAR(o.created_at), MONTH(o.created_at)
            ORDER BY year DESC, month DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thống kê doanh thu và đơn hàng theo Quý
     *
     * @return array<int, array<string, mixed>>
     */
    public function getQuarterlyStats(): array
    {
        return $this->db->query("
            SELECT 
                YEAR(o.created_at) AS year,
                QUARTER(o.created_at) AS quarter,
                COUNT(DISTINCT o.id) AS order_count,
                COALESCE(SUM(od.quantity), 0) AS item_count,
                COALESCE(SUM(od.sub_total), 0) AS revenue
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE o.order_status != 'CANCELLED'
            GROUP BY YEAR(o.created_at), QUARTER(o.created_at)
            ORDER BY year DESC, quarter DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thống kê doanh thu và đơn hàng theo Năm
     *
     * @return array<int, array<string, mixed>>
     */
    public function getYearlyStats(): array
    {
        return $this->db->query("
            SELECT 
                YEAR(o.created_at) AS year,
                COUNT(DISTINCT o.id) AS order_count,
                COALESCE(SUM(od.quantity), 0) AS item_count,
                COALESCE(SUM(od.sub_total), 0) AS revenue
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE o.order_status != 'CANCELLED'
            GROUP BY YEAR(o.created_at)
            ORDER BY year DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thống kê doanh thu theo Phương thức thanh toán
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPaymentStats(): array
    {
        return $this->db->query("
            SELECT 
                COALESCE(p.payment_method, 'COD') AS payment_method,
                COUNT(DISTINCT o.id) AS order_count,
                COALESCE(SUM(o.total_amount), 0) AS total_amount
            FROM orders o
            LEFT JOIN payments p ON o.id = p.order_id
            WHERE o.order_status != 'CANCELLED'
            GROUP BY p.payment_method
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
