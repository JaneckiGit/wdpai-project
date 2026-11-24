<?php
// Dni okna jako tekst: kolejne dni to zakres („pon–pt”), pojedynczy dzień – jego skrót („nd”), inne – lista („pon, śr”).
$dayNames = require __DIR__ . '/day-names.php';

return static function (array $days) use ($dayNames): string {
    $isRange = count($days) > 1 && $days === range($days[0], $days[count($days) - 1]);

    return $isRange
        ? $dayNames[$days[0]] . '–' . $dayNames[$days[count($days) - 1]]
        : implode(', ', array_map(static fn (int $day): string => $dayNames[$day], $days));
};
