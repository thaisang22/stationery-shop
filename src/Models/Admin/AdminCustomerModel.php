<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Model;
use PDO;

class AdminCustomerModel extends Model
{
    /**
     * Lấy danh sách khách hàng cùng số đơn và tổng chi tiêu
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllWithStats(): array
    {
        $sql = "
            SELECT u.*,
                   (SELECT COUNT(*) FROM orders WHERE user_id = u.id) AS order_count,
                   (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.id AND order_status != 'CANCELLED' AND payment_status = 'PAID') AS total_spent
            FROM users u
            WHERE u.role = 'CUSTOMER'
            ORDER BY u.id DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
