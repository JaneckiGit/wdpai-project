<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use PDO;

final class UserRepository
{
    public function __construct(private PDO $database)
    {
    }

    /* E-mail porównujemy bez wielkości liter – tak działa unikalny indeks users_email_unique na LOWER(email). */
    public function findByEmail(string $email): ?User
    {
        $statement = $this->database->prepare(
            'SELECT id, name, email, is_host, is_admin
             FROM users
             WHERE LOWER(email) = LOWER(:email)'
        );
        $statement->execute(['email' => $email]);

        $row = $statement->fetch();

        return $row === false ? null : $this->mapRowToUser($row);
    }

    /** @param array<string, mixed> $row */
    private function mapRowToUser(array $row): User
    {
        return new User(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['email'],
            (bool) $row['is_host'],
            (bool) $row['is_admin'],
        );
    }
}
