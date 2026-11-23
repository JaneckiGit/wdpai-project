<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\AvailabilityWindow;
use App\Model\HourRange;
use App\Model\Venue;
use PDO;

final class VenueRepository
{
    /*
     * Lokal z imieniem gospodarza (JOIN users) oraz celami i udogodnieniami sklejonymi przez string_agg
     * w jeden napis, np. 'work,meetings'. Podzapytania zamiast JOIN-ów, żeby wiersze lokalu się nie mnożyły.
     * array_position ustawia kody w tej samej kolejności co w formularzach.
     */
    private const SELECT = "SELECT venues.id, venues.host_id, users.name AS host_name, venues.name, venues.type,
            venues.address, venues.district, venues.latitude, venues.longitude, venues.description,
            venues.entry_mode, venues.status,
            (SELECT string_agg(purpose, ',' ORDER BY array_position(ARRAY['work', 'meetings', 'shoots', 'events'], purpose::text))
                FROM venue_purposes WHERE venue_id = venues.id) AS purposes,
            (SELECT string_agg(amenity, ',' ORDER BY array_position(ARRAY['wifi', 'sockets', 'daylight', 'projector', 'coffee'], amenity::text))
                FROM venue_amenities WHERE venue_id = venues.id) AS amenities
         FROM venues
         JOIN users ON users.id = venues.host_id";

    public function __construct(private PDO $database)
    {
    }

    /**
     * Odkrywaj: opublikowane lokale, które pozwalają na wybrany cel, mają okno w wybranym trybie
     * i zawierają szukaną frazę w nazwie albo dzielnicy (ILIKE nie rozróżnia wielkości liter).
     *
     * @return list<Venue>
     */
    public function findPublished(string $purpose, string $mode, string $query): array
    {
        $statement = $this->database->prepare(
            self::SELECT . "
             WHERE venues.status = 'published'
               AND EXISTS (SELECT 1 FROM venue_purposes WHERE venue_id = venues.id AND purpose = :purpose)
               AND EXISTS (SELECT 1 FROM availability_windows WHERE venue_id = venues.id AND mode = :mode)
               AND (venues.name || ' ' || COALESCE(venues.district, '')) ILIKE :pattern
             ORDER BY venues.id"
        );
        $statement->execute([
            'purpose' => $purpose,
            'mode' => $mode,
            'pattern' => '%' . $query . '%',
        ]);

        return $this->mapRowsToVenues($statement->fetchAll());
    }

    /** @return list<Venue> lokale gospodarza w każdym statusie (szkic, weryfikacja, opublikowany, odrzucony) */
    public function findByHost(int $hostId): array
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE venues.host_id = :host_id ORDER BY venues.created_at'
        );
        $statement->execute(['host_id' => $hostId]);

        return $this->mapRowsToVenues($statement->fetchAll());
    }

    public function find(int $id): ?Venue
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE venues.id = :id'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();

        return $row === false ? null : $this->mapRowToVenue($row);
    }

    /** @return list<AvailabilityWindow> okna lokalu, najpierw miejsca do pracy, potem wyłączność */
    private function findWindows(int $venueId): array
    {
        $statement = $this->database->prepare(
            "SELECT id, mode, start_hour, end_hour, capacity, price, min_hours,
                (SELECT string_agg(weekday::text, ',' ORDER BY weekday)
                    FROM window_days WHERE window_id = availability_windows.id) AS days
             FROM availability_windows
             WHERE venue_id = :venue_id
             ORDER BY mode DESC, start_hour"
        );
        $statement->execute(['venue_id' => $venueId]);

        $windows = [];
        foreach ($statement->fetchAll() as $row) {
            $windows[] = $this->mapRowToWindow($row);
        }

        return $windows;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<Venue>
     */
    private function mapRowsToVenues(array $rows): array
    {
        $venues = [];
        foreach ($rows as $row) {
            $venues[] = $this->mapRowToVenue($row);
        }

        return $venues;
    }

    /** @param array<string, mixed> $row */
    private function mapRowToVenue(array $row): Venue
    {
        return new Venue(
            (int) $row['id'],
            (int) $row['host_id'],
            (string) $row['host_name'],
            (string) $row['name'],
            $row['type'],
            $row['address'],
            $row['district'],
            // NUMERIC przychodzi z bazy jako napis, np. '50.062300', więc rzutujemy go na liczbę.
            $row['latitude'] === null ? null : (float) $row['latitude'],
            $row['longitude'] === null ? null : (float) $row['longitude'],
            $row['description'],
            (string) $row['entry_mode'],
            (string) $row['status'],
            self::splitCodes($row['purposes']),
            self::splitCodes($row['amenities']),
            $this->findWindows((int) $row['id']),
        );
    }

    /** @param array<string, mixed> $row */
    private function mapRowToWindow(array $row): AvailabilityWindow
    {
        return new AvailabilityWindow(
            (int) $row['id'],
            (string) $row['mode'],
            array_map('intval', self::splitCodes($row['days'])),
            new HourRange((int) $row['start_hour'], (int) $row['end_hour']),
            (int) $row['capacity'],
            (float) $row['price'],
            (int) $row['min_hours'],
        );
    }

    /** @return string[] napis z string_agg, np. 'work,meetings', jako lista; brak wierszy daje NULL i pustą listę */
    private static function splitCodes(?string $codes): array
    {
        return $codes === null ? [] : explode(',', $codes);
    }
}
