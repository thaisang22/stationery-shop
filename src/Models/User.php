<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function findById(int $userId): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $userId, \PDO::PARAM_INT);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
