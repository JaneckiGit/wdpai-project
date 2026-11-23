<?php

declare(strict_types=1);

namespace App\Model;

/*
 * Value Object (Laboratorium 8): przedział godzin od–do, np. 9–15 = od 9:00 do 15:00.
 * Nie ma id, liczy się sama wartość; jest niemutowalny, a poprawność sprawdza konstruktor.
 */
final class HourRange
{
    private int $start;
    private int $end;

    public function __construct(int $start, int $end)
    {
        if ($start < 0 || $end > 24 || $start >= $end) {
            throw new \InvalidArgumentException('Invalid hour range: ' . $start . '–' . $end);
        }

        $this->start = $start;
        $this->end = $end;
    }

    public function start(): int
    {
        return $this->start;
    }

    public function end(): int
    {
        return $this->end;
    }

    /* Godzina rozpoczęcia mieści się w przedziale, np. 14 w 9–15 tak, ale 15 już nie (o 15:00 okno się kończy). */
    public function includes(int $hour): bool
    {
        return $hour >= $this->start && $hour < $this->end;
    }
}
