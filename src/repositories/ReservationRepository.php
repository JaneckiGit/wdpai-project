<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;
use PDO;

final class ReservationRepository
{
    // Wspólny początek zapytań odczytu – kolumny wypisane raz zamiast w każdej metodzie.
    // JOIN-y dodają nazwę lokalu (Moje rezerwacje) oraz imię i nazwisko najemcy (prośby w panelu gospodarza).
    private const SELECT = "SELECT reservations.id, reservations.venue_id, reservations.user_id, reservations.mode,
            reservations.purpose, reservations.reservation_date, reservations.start_hour, reservations.end_hour,
            reservations.seats, reservations.status, reservations.created_at, reservations.updated_at,
            venues.name AS venue_name, users.first_name || ' ' || users.last_name AS guest_name
         FROM reservations
         JOIN venues ON venues.id = reservations.venue_id
         JOIN users ON users.id = reservations.user_id";

    public function __construct(private PDO $database)
    {
    }

    /** @return list<Reservation> */
    public function findAll(): array
    {
        $statement = $this->database->query(
            self::SELECT . ' ORDER BY reservations.reservation_date, reservations.start_hour'
        );

        $reservations = [];
        foreach ($statement->fetchAll() as $row) {
            $reservations[] = $this->mapRowToReservation($row);
        }

        return $reservations;
    }

    /** @return list<Reservation> rezerwacje jednego najemcy (Moje rezerwacje) */
    public function findByUser(int $userId): array
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE reservations.user_id = :user_id ORDER BY reservations.reservation_date, reservations.start_hour'
        );
        $statement->execute(['user_id' => $userId]);

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
            self::SELECT . ' WHERE reservations.status = :status ORDER BY reservations.reservation_date, reservations.start_hour'
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
            self::SELECT . ' WHERE reservations.id = :id'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();

        return $row === false ? null : $this->mapRowToReservation($row);
    }

    public function save(Reservation $reservation): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO reservations (venue_id, user_id, mode, purpose, reservation_date, start_hour, end_hour, seats, status)
             VALUES (:venue_id, :user_id, :mode, :purpose, :reservation_date, :start_hour, :end_hour, :seats, :status)
             RETURNING id'
        );
        $statement->execute([
            'venue_id' => $reservation->getVenueId(),
            'user_id' => $reservation->getUserId(),
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
            (int) $row['user_id'],
            (string) $row['mode'],
            (string) $row['purpose'],
            new DateTimeImmutable((string) $row['reservation_date']),
            (int) $row['start_hour'],
            (int) $row['end_hour'],
            $row['seats'] === null ? null : (int) $row['seats'],
            (string) $row['status'],
            new DateTimeImmutable((string) $row['created_at']),
            new DateTimeImmutable((string) $row['updated_at']),
            (string) $row['venue_name'],
            (string) $row['guest_name'],
        );
    }
}
