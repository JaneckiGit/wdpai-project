<?php
// Czas zdarzenia jak w prototypie: „dziś 9:40”, „wczoraj 18:15” albo sama data „12.11”.
// Baza zwraca TIMESTAMPTZ w strefie UTC, więc najpierw przestawiamy go na strefę aplikacji (Europe/Warsaw z index.php).
return static function (DateTimeImmutable $time): string {
    $time = $time->setTimezone(new DateTimeZone(date_default_timezone_get()));
    $today = new DateTimeImmutable('today');

    return match (true) {
        $time >= $today => 'dziś ' . $time->format('G:i'),
        $time >= $today->modify('-1 day') => 'wczoraj ' . $time->format('G:i'),
        default => $time->format('d.m'),
    };
};
