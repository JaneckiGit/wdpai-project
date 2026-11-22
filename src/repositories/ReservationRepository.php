<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;
use PDO;

final class ReservationRepository
{
    // Wspólny początek zapytań odczytu – kolumny wypisane raz zamiast w każdej metodzie.
    private const SELECT = 'SELECT id, venue_id, mode, purpose, reservation_date, start_hour, end_hour, seats, status, created_at, updated_at
         FROM reservations';

    public function __construct(private PDO $database)
    {
    }

    /** @return list<Reservation> */
    public function findAll(): array
    {
        $statement = $this->database->query(
            self::SELECT . ' ORDER BY reservation_date, start_hour'
        );

        $reservations = [];
        foreach ($statement->fetchAll() as $row) {
            $reservations[] = $this->mapRowToReservation($row);
        }

        return $reservations;
    }

    /** @return list<Reservation> */
    public function findByStatus(string $status): array
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE status = :status ORDER BY reservation_date, start_hour'
        );
        $statement->execute(['status' => $status]);

        $reservations = [];
        foreach ($statement->fetchAll() as $row) {
            $reservations[] = $this->mapRowToReservation($row);
        }

        return $reservations;
    }

    public function find(int $id): ?Reservation
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE id = :id'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();

        return $row === false ? null : $this->mapRowToReservation($row);
    }

    public function save(Reservation $reservation): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO reservations (venue_id, mode, purpose, reservation_date, start_hour, end_hour, seats, status)
             VALUES (:venue_id, :mode, :purpose, :reservation_date, :start_hour, :end_hour, :seats, :status)
             RETURNING id'
        );
        $statement->execute([
            'venue_id' => $reservation->getVenueId(),
            'mode' => $reservation->getMode(),
            'purpose' => $reservation->getPurpose(),
            'reservation_date' => $reservation->getDate()->format('Y-m-d'),
            'start_hour' => $reservation->getStartHour(),
            'end_hour' => $reservation->getEndHour(),
            'seats' => $reservation->getSeats(),
            'status' => $reservation->getStatus(),
        ]);

        $reservation->setId((int) $statement->fetchColumn());
    }

    /* Po złożeniu prośby aplikacja zmienia już tylko jej status (akceptacja, odrzucenie, anulowanie). */
    public function update(Reservation $reservation): void
    {
        if ($reservation->getId() === null) {
            throw new \InvalidArgumentException('Nie można zaktualizować rezerwacji bez id.');
        }

        $statement = $this->database->prepare(
            'UPDATE reservations
             SET status = :status,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $reservation->getId(),
            'status' => $reservation->getStatus(),
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->database->prepare(
            'DELETE FROM reservations WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
    }

    /** @param array<string, mixed> $row */
    private function mapRowToReservation(array $row): Reservation
    {
        return new Reservation(
            (int) $row['id'],
            (int) $row['venue_id'],
            (string) $row['mode'],
            (string) $row['purpose'],
            new DateTimeImmutable((string) $row['reservation_date']),
            (int) $row['start_hour'],
            (int) $row['end_hour'],
            $row['seats'] === null ? null : (int) $row['seats'],
            (string) $row['status'],
            new DateTimeImmutable((string) $row['created_at']),
            new DateTimeImmutable((string) $row['updated_at']),
        );
    }
}
