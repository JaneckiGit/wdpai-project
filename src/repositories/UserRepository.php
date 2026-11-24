<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use DateTimeImmutable;
use PDO;

final class UserRepository
{
    // Wspólny początek zapytań; EXISTS sprawdza, czy konto ma wniosek o rolę gospodarza bez decyzji.
    private const SELECT = "SELECT id, first_name, last_name, email, is_host, is_admin, blocked_at, last_seen_at,
            EXISTS (SELECT 1 FROM host_applications WHERE user_id = users.id AND status = 'pending') AS has_pending_application
         FROM users";

    public function __construct(private PDO $database)
    {
    }

    /* E-mail porównujemy bez wielkości liter – tak działa unikalny indeks users_email_unique na LOWER(email). */
    public function findByEmail(string $email): ?User
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE LOWER(email) = LOWER(:email)'
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
            (string) $row['first_name'],
            (string) $row['last_name'],
            (string) $row['email'],
            (bool) $row['is_host'],
            (bool) $row['is_admin'],
            $row['blocked_at'] === null ? null : new DateTimeImmutable((string) $row['blocked_at']),
            $row['last_seen_at'] === null ? null : new DateTimeImmutable((string) $row['last_seen_at']),
            (bool) $row['has_pending_application'],
        );
    }
}
