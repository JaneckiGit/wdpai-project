<?php

declare(strict_types=1);

namespace App\Form;

final class AvailabilityWindowForm extends Form
{
    // Górne granice zgodne z kolumnami: capacity SMALLINT, price NUMERIC(8, 2).
    private const MAX_CAPACITY = 1000;
    private const MAX_PRICE = 99999;

    private const DAY_SETS = [
        'mon-fri' => [1, 2, 3, 4, 5],
        'mon-thu' => [1, 2, 3, 4],
        'mon' => [1],
        'tue' => [2],
        'wed' => [3],
        'thu' => [4],
        'fri' => [5],
        'sat' => [6],
        'sun' => [7],
    ];

    public readonly string $days;
    public readonly string $from;
    public readonly string $to;
    public readonly string $mode;
    public readonly string $capacity;
    public readonly string $price;
    public readonly string $minHours;

    public function __construct(array $input = [])
    {
        $this->days = self::textOr($input, 'days', 'mon-fri');
        $this->from = self::text($input, 'from');
        $this->to = self::text($input, 'to');
        $this->mode = self::textOr($input, 'mode', 'seats');
        $this->capacity = self::text($input, 'capacity');
        $this->price = self::text($input, 'price');
        $this->minHours = self::textOr($input, 'minHours', '2');
    }

    /* Wiersz bez godzin, liczby miejsc i ceny – dni, tryb i minimum mają wartości domyślne, więc ich nie liczymy. */
    public function isEmpty(): bool
    {
        return $this->from === '' && $this->to === '' && $this->capacity === '' && $this->price === '';
    }

    /** @return int[] dni tygodnia okna, np. 'mon-fri' → [1, 2, 3, 4, 5] (wiersze tabeli window_days) */
    public function weekdays(): array
    {
        return self::DAY_SETS[$this->days];
    }

    public function startHour(): int
    {
        return self::hour($this->from);
    }

    public function endHour(): int
    {
        return self::hour($this->to);
    }

    /* Cena jako liczba; formularz przyjmuje też przecinek, np. „15,50”. */
    public function priceValue(): float
    {
        return (float) str_replace(',', '.', $this->price);
    }

    /* Okna nakładają się, gdy mają wspólny dzień i ich godziny się przecinają. */
    public function overlaps(self $other): bool
    {
        if (!$this->hasValidHours() || !$other->hasValidHours()) {
            return false;
        }

        $sharedDays = array_intersect(self::DAY_SETS[$this->days] ?? [], self::DAY_SETS[$other->days] ?? []);

        return $sharedDays !== []
            && self::hour($this->from) < self::hour($other->to)
            && self::hour($other->from) < self::hour($this->to);
    }

    public function markOverlapWith(int $otherNumber): void
    {
        $this->addError('from', 'Pokrywa się z oknem ' . $otherNumber . ' – okna tego samego lokalu nie mogą się nakładać.');
    }

    protected function validate(): void
    {
        $this->requireChoice('days', $this->days, array_keys(self::DAY_SETS), 'Wybierz dni.');
        $this->requireHourRange('from', $this->from, 'to', $this->to);
        $this->requireChoice('mode', $this->mode, self::MODES, 'Wybierz tryb.');
        $this->requirePositiveInt('capacity', $this->capacity, 'Podaj liczbę miejsc lub maksymalną liczbę osób.');
        $this->requirePositiveInt('minHours', $this->minHours, 'Podaj minimalną liczbę godzin.');

        if ((int) $this->capacity > self::MAX_CAPACITY) {
            $this->addError('capacity', 'Liczba miejsc może wynosić najwyżej ' . self::MAX_CAPACITY . '.');
        }

        if (preg_match('/^\d+([.,]\d{1,2})?$/', $this->price) !== 1) {
            $this->addError('price', 'Podaj cenę za godzinę, np. 15 albo 15,50.');
        } elseif ($this->priceValue() <= 0 || $this->priceValue() > self::MAX_PRICE) {
            $this->addError('price', 'Cena za godzinę musi być większa od zera i nie większa niż ' . self::MAX_PRICE . ' zł.');
        }

        if ($this->hasValidHours() && (int) $this->minHours > self::hour($this->to) - self::hour($this->from)) {
            $this->addError('minHours', 'Minimalny czas rezerwacji nie może być dłuższy niż okno.');
        }
    }

    private function hasValidHours(): bool
    {
        return self::isFullHour($this->from)
            && self::isFullHour($this->to)
            && self::hour($this->from) < self::hour($this->to);
    }
}
