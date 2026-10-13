<?php

declare(strict_types=1);

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    if (isset($_SESSION['user_role'])) {
        return strtoupper((string) $_SESSION['user_role']) === 'ADMIN';
    }
    if (isset($_SESSION['user']['role'])) {
        return strtoupper((string) $_SESSION['user']['role']) === 'ADMIN';
    }
    if (isset($_SESSION['user_id'])) {
        try {
            $db = \App\Core\Database::connection();
            $stmt = $db->prepare("SELECT role FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => (int) $_SESSION['user_id']]);
            $role = (string) $stmt->fetchColumn();
            $_SESSION['user_role'] = $role;
            return strtoupper($role) === 'ADMIN';
        } catch (\Throwable $e) {
            return false;
        }
    }
    return false;
}


