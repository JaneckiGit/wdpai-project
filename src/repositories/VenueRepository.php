<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\AvailabilityWindow;
use App\Model\Venue;
use DateTimeImmutable;
use PDO;

final class VenueRepository
{
    /*
     * Lokal z imieniem i nazwiskiem gospodarza (JOIN users) oraz kodami celów sklejonymi przez string_agg w jeden napis,
     * np. 'work,meetings', w kolejności ze słownika (JOIN purposes, sort_order). Cele są w podzapytaniu, a nie w JOIN-ie,
     * żeby wiersze lokalu się nie mnożyły. Okna i udogodnienia wczytują osobne, proste zapytania.
     */
    private const SELECT = "SELECT venues.id, venues.host_id, users.first_name || ' ' || users.last_name AS host_name,
            venues.name, venues.type, venues.street, venues.building_number, venues.unit_number, venues.postal_code,
            venues.city, venues.district, venues.latitude, venues.longitude, venues.description, venues.entry_mode,
            venues.entry_instructions, venues.status, venues.created_at,
            (SELECT string_agg(venue_purposes.purpose, ',' ORDER BY purposes.sort_order)
                FROM venue_purposes
                JOIN purposes ON purposes.code = venue_purposes.purpose
                WHERE venue_purposes.venue_id = venues.id) AS purposes
         FROM venues
         JOIN users ON users.id = venues.host_id";

    public function __construct(private PDO $database)
    {
    }

    /**
     * Odkrywaj: opublikowane lokale, które pozwalają na wybrany cel, mają okno w wybranym trybie
     * i zawierają szukaną frazę w nazwie, dzielnicy, ulicy albo mieście (ILIKE nie rozróżnia wielkości liter).
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
               AND concat_ws(' ', venues.name, venues.district, venues.street, venues.city) ILIKE :pattern
             ORDER BY venues.id"
        );
        $statement->execute([
            'purpose' => $purpose,
            'mode' => $mode,
            // % i _ mają w ILIKE specjalne znaczenie, więc w szukanej frazie poprzedzamy je ukośnikiem.
            'pattern' => '%' . addcslashes($query, '%_\\') . '%',
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

    /** @return list<Venue> lokale w danym statusie, od najstarszego (panel administratora: 'pending') */
    public function findByStatus(string $status): array
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE venues.status = :status ORDER BY venues.created_at'
        );
        $statement->execute(['status' => $status]);

        return $this->mapRowsToVenues($statement->fetchAll());
    }

    /* Lokal w każdym statusie; o tym, kto może go zobaczyć, decyduje kontroler. */
    public function find(int $id): ?Venue
    {
        $statement = $this->database->prepare(
            self::SELECT . ' WHERE venues.id = :id'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();

        return $row === false ? null : $this->mapRowToVenue($row);
    }

    /*
     * Nowy lokal z formularza „Dodaj lokal”: wiersz venues, jego cele, okna dostępności i dni okien – cztery tabele.
     * Wszystkie INSERT-y idą w jednej transakcji, więc gdy któryś się nie uda, w bazie nie zostanie lokal bez okien.
     */
    public function save(Venue $venue): void
    {
        $this->database->beginTransaction();

        try {
            $statement = $this->database->prepare(
                'INSERT INTO venues (host_id, name, type, street, building_number, unit_number, postal_code, city,
                     district, latitude, longitude, description, entry_mode, entry_instructions, status)
                 VALUES (:host_id, :name, :type, :street, :building_number, :unit_number, :postal_code, :city,
                     :district, :latitude, :longitude, :description, :entry_mode, :entry_instructions, :status)
                 RETURNING id'
            );
            $statement->execute([
                'host_id' => $venue->getHostId(),
                'name' => $venue->getName(),
                'type' => $venue->getType(),
                'street' => $venue->getStreet(),
                'building_number' => $venue->getBuildingNumber(),
                'unit_number' => $venue->getUnitNumber(),
                'postal_code' => $venue->getPostalCode(),
                'city' => $venue->getCity(),
                'district' => $venue->getDistrict(),
                'latitude' => $venue->getLatitude(),
                'longitude' => $venue->getLongitude(),
                'description' => $venue->getDescription(),
                'entry_mode' => $venue->getEntryMode(),
                'entry_instructions' => $venue->getEntryInstructions(),
                'status' => $venue->getStatus(),
            ]);
            $venue->setId((int) $statement->fetchColumn());

            $purpose = $this->database->prepare(
                'INSERT INTO venue_purposes (venue_id, purpose) VALUES (:venue_id, :purpose)'
            );
            foreach ($venue->getPurposes() as $code) {
                $purpose->execute(['venue_id' => $venue->getId(), 'purpose' => $code]);
            }

            foreach ($venue->getWindows() as $window) {
                $this->saveWindow($venue->getId(), $window);
            }

            $this->database->commit();
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            throw $exception;
        }
    }

    /* Okno dostępności i jego dni tygodnia (osobne wiersze window_days); wywoływane wewnątrz transakcji save(). */
    private function saveWindow(int $venueId, AvailabilityWindow $window): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO availability_windows (venue_id, mode, start_hour, end_hour, capacity, price, min_hours)
             VALUES (:venue_id, :mode, :start_hour, :end_hour, :capacity, :price, :min_hours)
             RETURNING id'
        );
        $statement->execute([
            'venue_id' => $venueId,
            'mode' => $window->getMode(),
            'start_hour' => $window->getStartHour(),
            'end_hour' => $window->getEndHour(),
            'capacity' => $window->getCapacity(),
            'price' => $window->getPrice(),
            'min_hours' => $window->getMinHours(),
        ]);
        $windowId = (int) $statement->fetchColumn();

        $day = $this->database->prepare(
            'INSERT INTO window_days (window_id, weekday) VALUES (:window_id, :weekday)'
        );
        foreach ($window->getDays() as $weekday) {
            $day->execute(['window_id' => $windowId, 'weekday' => $weekday]);
        }
    }

    /* Administrator zmienia tylko status lokalu; updated_at ustawia wyzwalacz venues_set_updated_at. */
    public function update(Venue $venue): void
    {
        $statement = $this->database->prepare(
            'UPDATE venues SET status = :status WHERE id = :id'
        );
        $statement->execute([
            'id' => $venue->getId(),
            'status' => $venue->getStatus(),
        ]);
    }

    /** @return list<AvailabilityWindow> okna lokalu: najpierw miejsca do pracy, potem wyłączność, każde z dniami tygodnia */
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

    /** @return array<string, ?string> kod udogodnienia => dopisek gospodarza, w kolejności ze słownika */
    private function findAmenities(int $venueId): array
    {
        $statement = $this->database->prepare(
            'SELECT venue_amenities.amenity, venue_amenities.note
             FROM venue_amenities
             JOIN amenities ON amenities.code = venue_amenities.amenity
             WHERE venue_amenities.venue_id = :venue_id
             ORDER BY amenities.sort_order'
        );
        $statement->execute(['venue_id' => $venueId]);

        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);
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
            $row['street'],
            $row['building_number'],
            $row['unit_number'],
            $row['postal_code'],
            $row['city'],
            $row['district'],
            // NUMERIC przychodzi z bazy jako napis, np. '50.062300', więc rzutujemy go na liczbę.
            $row['latitude'] === null ? null : (float) $row['latitude'],
            $row['longitude'] === null ? null : (float) $row['longitude'],
            $row['description'],
            (string) $row['entry_mode'],
            $row['entry_instructions'],
            (string) $row['status'],
            new DateTimeImmutable((string) $row['created_at']),
            self::splitList($row['purposes']),
            $this->findAmenities((int) $row['id']),
            $this->findWindows((int) $row['id']),
        );
    }

    /** @param array<string, mixed> $row */
    private function mapRowToWindow(array $row): AvailabilityWindow
    {
        return new AvailabilityWindow(
            (int) $row['id'],
            (string) $row['mode'],
            array_map('intval', self::splitList($row['days'])),
            (int) $row['start_hour'],
            (int) $row['end_hour'],
            (int) $row['capacity'],
            (float) $row['price'],
            (int) $row['min_hours'],
        );
    }

    /** @return string[] napis z string_agg, np. 'work,meetings', jako lista; brak wierszy daje NULL i pustą listę */
    private static function splitList(?string $list): array
    {
        return $list === null ? [] : explode(',', $list);
    }
}
