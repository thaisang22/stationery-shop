<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    public function findById(int $userId): ?array
    {
        $sql = "SELECT *
                FROM users
                WHERE id = :id
                LIMIT 1";

        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT *
                FROM users
                WHERE email = :email
                LIMIT 1";

        $statement = $this->db->prepare($sql);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function register(
        string $fullName,
        string $email,
        string $password,
        ?string $phoneNumber = null
    ): int {
        $sql = "INSERT INTO users (
                    role,
                    full_name,
                    email,
                    password_hash,
                    phone_number,
                    created_at,
                    updated_at
                ) VALUES (
                    :role,
                    :full_name,
                    :email,
                    :password_hash,
                    :phone_number,
                    NOW(),
                    NOW()
                )";

        $statement = $this->db->prepare($sql);

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $statement->bindValue(':role', 'customer', PDO::PARAM_STR);
        $statement->bindValue(':full_name', $fullName, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $statement->bindValue(
            ':phone_number',
            $phoneNumber,
            $phoneNumber === null ? PDO::PARAM_NULL : PDO::PARAM_STR
        );

        $statement->execute();

        return (int) $this->db->lastInsertId();
    }

    public function login(string $email, string $password): ?array
    {
        $sql = "SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1";

        $statement = $this->db->prepare($sql);

        $statement->bindValue(
            ':email',
            $email,
            PDO::PARAM_STR
        );

        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        return $user;
    }
}
