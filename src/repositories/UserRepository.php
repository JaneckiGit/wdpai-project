<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use DateTimeImmutable;
use PDO;

final class UserRepository
{
    // Wspólny początek zapytań; EXISTS sprawdza, czy konto ma wniosek o rolę gospodarza bez decyzji.
    private const SELECT = "SELECT id, name, email, is_host, is_admin, blocked_at, last_seen_at,
            EXISTS (SELECT 1 FROM host_applications WHERE user_id = users.id AND status = 'pending') AS has_pending_application
         FROM users";

    public function __construct(private PDO $database)
    {
    }

    /**
     * Tabela w panelu administratora: konta bez administratorów (ich nie blokuje się z panelu),
     * zawężone do gospodarzy albo zablokowanych i do frazy w imieniu lub e-mailu.
     *
     * @return list<User>
     */
    public function findForAdmin(bool $onlyHosts, bool $onlyBlocked, string $query): array
    {
        $statement = $this->database->prepare(
            self::SELECT . "
             WHERE NOT is_admin
               AND (NOT :only_hosts OR is_host)
               AND (NOT :only_blocked OR blocked_at IS NOT NULL)
               AND (name || ' ' || email) ILIKE :pattern
             ORDER BY last_seen_at DESC NULLS LAST"
        );
        // Wartości logiczne trzeba oznaczyć PARAM_BOOL – inaczej PDO wysłałoby false jako pusty napis.
        $statement->bindValue(':only_hosts', $onlyHosts, PDO::PARAM_BOOL);
        $statement->bindValue(':only_blocked', $onlyBlocked, PDO::PARAM_BOOL);
        $statement->bindValue(':pattern', '%' . $query . '%');
        $statement->execute();

        $users = [];
        foreach ($statement->fetchAll() as $row) {
            $users[] = $this->mapRowToUser($row);
        }

        return $users;
    }

    public function find(int $id): ?User
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE id = :id'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();

        return $row === false ? null : $this->mapRowToUser($row);
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

    /* Administrator zmienia tylko rolę gospodarza i blokadę; dane konta zmieni jego właściciel (Laboratorium 9). */
    public function update(User $user): void
    {
        $statement = $this->database->prepare(
            'UPDATE users
             SET is_host = :is_host,
                 blocked_at = :blocked_at
             WHERE id = :id'
        );
        $statement->bindValue(':is_host', $user->isHost(), PDO::PARAM_BOOL);
        $statement->bindValue(':blocked_at', $user->getBlockedAt()?->format(DATE_ATOM));
        $statement->bindValue(':id', $user->getId(), PDO::PARAM_INT);
        $statement->execute();
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
            $row['blocked_at'] === null ? null : new DateTimeImmutable((string) $row['blocked_at']),
            $row['last_seen_at'] === null ? null : new DateTimeImmutable((string) $row['last_seen_at']),
            (bool) $row['has_pending_application'],
        );
    }
}
