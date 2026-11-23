<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\HostApplication;
use DateTimeImmutable;
use PDO;

final class HostApplicationRepository
{
    // E-mail składającego wniosek pochodzi z konta, więc JOIN users; obie tabele mają id i created_at, stąd przedrostki.
    private const SELECT = 'SELECT host_applications.id, host_applications.user_id, users.email, host_applications.first_name,
            host_applications.last_name, host_applications.phone, host_applications.company_name, host_applications.nip,
            host_applications.venue_name, host_applications.venue_type, host_applications.address, host_applications.message,
            host_applications.status, host_applications.created_at
         FROM host_applications
         JOIN users ON users.id = host_applications.user_id';

    public function __construct(private PDO $database)
    {
    }

    /** @return list<HostApplication> wnioski czekające na decyzję administratora, od najstarszego */
    public function findPending(): array
    {
        $statement = $this->database->query(
            self::SELECT . " WHERE host_applications.status = 'pending' ORDER BY host_applications.created_at"
        );

        $applications = [];
        foreach ($statement->fetchAll() as $row) {
            $applications[] = $this->mapRowToApplication($row);
        }

        return $applications;
    }

    public function find(int $id): ?HostApplication
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE host_applications.id = :id'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();

        return $row === false ? null : $this->mapRowToApplication($row);
    }

    /** @param array<string, mixed> $row */
    private function mapRowToApplication(array $row): HostApplication
    {
        return new HostApplication(
            (int) $row['id'],
            (int) $row['user_id'],
            (string) $row['email'],
            (string) $row['first_name'],
            (string) $row['last_name'],
            (string) $row['phone'],
            (string) $row['company_name'],
            (string) $row['nip'],
            (string) $row['venue_name'],
            (string) $row['venue_type'],
            (string) $row['address'],
            (string) $row['message'],
            (string) $row['status'],
            new DateTimeImmutable((string) $row['created_at']),
        );
    }
}
