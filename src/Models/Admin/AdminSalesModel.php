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
        $revenue = (float) $this->db->query("
            SELECT COALESCE(SUM(total_amount), 0) 
            FROM orders 
            WHERE order_status != 'CANCELLED' AND payment_status = 'PAID'
        ")->fetchColumn();

        $stats = $this->db->query("
            SELECT 
                COUNT(DISTINCT o.id) AS total_orders,
                COUNT(DISTINCT CASE WHEN o.payment_status = 'PAID' THEN o.id END) AS paid_orders,
                COALESCE(SUM(od.quantity), 0) AS total_items
            FROM orders o
            LEFT JOIN order_details od ON o.id = od.order_id
            WHERE o.order_status != 'CANCELLED'
        ")->fetch(PDO::FETCH_ASSOC);

        return [
            'total_revenue' => $revenue,
            'total_orders'  => (int) ($stats['total_orders'] ?? 0),
            'paid_orders'   => (int) ($stats['paid_orders'] ?? 0),
            'total_items'   => (int) ($stats['total_items'] ?? 0),
        ];
    }

    /**
     * Thống kê doanh thu và đơn hàng theo Tháng (doanh thu chỉ tính đơn đã thanh toán)
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
                COALESCE(SUM(CASE WHEN o.payment_status = 'PAID' THEN od.sub_total ELSE 0 END), 0) AS revenue
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE o.order_status != 'CANCELLED'
            GROUP BY YEAR(o.created_at), MONTH(o.created_at)
            ORDER BY year DESC, month DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thống kê doanh thu và đơn hàng theo Quý (doanh thu chỉ tính đơn đã thanh toán)
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
                COALESCE(SUM(CASE WHEN o.payment_status = 'PAID' THEN od.sub_total ELSE 0 END), 0) AS revenue
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE o.order_status != 'CANCELLED'
            GROUP BY YEAR(o.created_at), QUARTER(o.created_at)
            ORDER BY year DESC, quarter DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thống kê doanh thu và đơn hàng theo Năm (doanh thu chỉ tính đơn đã thanh toán)
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
                COALESCE(SUM(CASE WHEN o.payment_status = 'PAID' THEN od.sub_total ELSE 0 END), 0) AS revenue
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE o.order_status != 'CANCELLED'
            GROUP BY YEAR(o.created_at)
            ORDER BY year DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thống kê doanh thu theo Phương thức thanh toán (doanh thu chỉ tính đơn đã thanh toán)
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPaymentStats(): array
    {
        return $this->db->query("
            SELECT 
                COALESCE(p.payment_method, 'COD') AS payment_method,
                COUNT(DISTINCT o.id) AS order_count,
                COALESCE(SUM(CASE WHEN o.payment_status = 'PAID' THEN o.total_amount ELSE 0 END), 0) AS total_amount
            FROM orders o
            LEFT JOIN payments p ON o.id = p.order_id
            WHERE o.order_status != 'CANCELLED'
            GROUP BY p.payment_method
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
