<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\HostApplication;
use DateTimeImmutable;
use PDO;

final class HostApplicationRepository
{
    // Imię, nazwisko i e-mail wnioskodawcy są na koncie (JOIN users), a nazwa firmy – w tabeli companies (JOIN po NIP-ie).
    private const SELECT = "SELECT host_applications.id, host_applications.user_id,
            users.first_name || ' ' || users.last_name AS applicant_name, users.email, host_applications.phone,
            host_applications.company_nip, companies.name AS company_name, host_applications.venue_name,
            host_applications.venue_type, host_applications.street, host_applications.building_number,
            host_applications.unit_number, host_applications.postal_code, host_applications.city,
            host_applications.message, host_applications.status, host_applications.created_at
         FROM host_applications
         JOIN users ON users.id = host_applications.user_id
         JOIN companies ON companies.nip = host_applications.company_nip";

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
            (string) $row['applicant_name'],
            (string) $row['email'],
            (string) $row['phone'],
            (string) $row['company_nip'],
            (string) $row['company_name'],
            (string) $row['venue_name'],
            (string) $row['venue_type'],
            (string) $row['street'],
            (string) $row['building_number'],
            $row['unit_number'],
            (string) $row['postal_code'],
            (string) $row['city'],
            (string) $row['message'],
            (string) $row['status'],
            new DateTimeImmutable((string) $row['created_at']),
        );
    }
}
