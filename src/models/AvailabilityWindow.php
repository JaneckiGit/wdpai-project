<?php

declare(strict_types=1);

namespace App\Model;

/* Okno dostępności lokalu: tryb, dni tygodnia, godziny, liczba miejsc (albo osób) i cena za godzinę. */
final class AvailabilityWindow
{
    /** @param int[] $days dni tygodnia: 1 = poniedziałek … 7 = niedziela */
    public function __construct(
        private int $id,
        private string $mode,
        private array $days,
        private HourRange $hours,
        private int $capacity,
        private float $price,
        private int $minHours,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    /** @return int[] */
    public function getDays(): array
    {
        return $this->days;
    }

    public function getHours(): HourRange
    {
        return $this->hours;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getMinHours(): int
    {
        return $this->minHours;
    }

    public function isOpenOn(int $weekday): bool
    {
        return in_array($weekday, $this->days, true);
    }
}
