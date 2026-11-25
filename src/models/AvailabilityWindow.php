<?php

declare(strict_types=1);

namespace App\Model;

/* Okno dostępności lokalu: tryb, dni tygodnia, godziny od–do, liczba miejsc (albo osób), cena za godzinę i minimalny czas. */
final class AvailabilityWindow
{
    /** @param int[] $days dni tygodnia: 1 = poniedziałek … 7 = niedziela */
    public function __construct(
        private ?int $id,
        private string $mode,
        private array $days,
        private int $startHour,
        private int $endHour,
        private int $capacity,
        private float $price,
        private int $minHours,
    ) {
    }

    public function getId(): ?int
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

    public function getStartHour(): int
    {
        return $this->startHour;
    }

    public function getEndHour(): int
    {
        return $this->endHour;
    }

    /* Liczba miejsc do pracy albo – przy wyłączności – maksymalna liczba osób. */
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

    /* Godzina rozpoczęcia mieści się w oknie, np. 14 w oknie 9–15 tak, ale 15 już nie (o 15:00 okno się kończy). */
    public function includesHour(int $hour): bool
    {
        return $hour >= $this->startHour && $hour < $this->endHour;
    }
}
