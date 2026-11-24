<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

/*
 * Słowniki z bazy: kod => nazwa w kolejności sort_order, np. ['work' => 'Praca', 'meetings' => 'Spotkania i szkolenia'].
 * Każda metoda ma stałe zapytanie (nazwy tabeli nie da się przekazać jako parametru), więc wystarcza query().
 */
final class DictionaryRepository
{
    public function __construct(private PDO $database)
    {
    }

    /** @return array<string, string> */
    public function purposes(): array
    {
        return $this->database->query('SELECT code, name FROM purposes ORDER BY sort_order')->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /** @return array<string, string> */
    public function venueTypes(): array
    {
        return $this->database->query('SELECT code, name FROM venue_types ORDER BY sort_order')->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /** @return array<string, string> */
    public function amenities(): array
    {
        return $this->database->query('SELECT code, name FROM amenities ORDER BY sort_order')->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
