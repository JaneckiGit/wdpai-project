<?php

declare(strict_types=1);

namespace App\Form;

final class ReservationForm extends Form
{
    // TODO: Laboratorium 8 – limity i dozwolone cele z danych konkretnego lokalu.
    private const MIN_HOURS = 2;
    private const MAX_SEATS = 4;

    public readonly string $mode;
    public readonly string $purpose;
    public readonly string $date;
    public readonly string $from;
    public readonly string $to;
    public readonly string $seats;

    public function __construct(array $input = [])
    {
        $this->mode = self::textOr($input, 'mode', 'seats');
        $this->purpose = self::textOr($input, 'purpose', 'work');
        $this->date = self::text($input, 'date');
        $this->from = self::text($input, 'from');
        $this->to = self::text($input, 'to');
        $this->seats = self::textOr($input, 'seats', '1');
    }

    protected function validate(): void
    {
        $this->requireChoice('mode', $this->mode, self::MODES, 'Wybierz tryb rezerwacji.');
        $this->requireChoice('purpose', $this->purpose, self::PURPOSES, 'Wybierz cel rezerwacji.');
        $this->validateDate();
        $this->requireHourRange('from', $this->from, 'to', $this->to);
        $this->validateDuration();

        if ($this->mode === 'seats') {
            $this->validateSeats();
        }
    }

    private function validateDate(): void
    {
        $date = self::parseDate($this->date);

        if ($date === null) {
            $this->addError('date', 'Podaj poprawną datę.');
        } elseif ($date < new \DateTimeImmutable('today')) {
            $this->addError('date', 'Data rezerwacji nie może być w przeszłości.');
        }
    }

    private function validateDuration(): void
    {
        if (!self::isFullHour($this->from) || !self::isFullHour($this->to)) {
            return;
        }

        $hours = self::hour($this->to) - self::hour($this->from);

        if ($hours > 0 && $hours < self::MIN_HOURS) {
            $this->addError('to', 'Minimalny czas rezerwacji to ' . self::MIN_HOURS . ' h.');
        }
    }

    private function validateSeats(): void
    {
        $this->requirePositiveInt('seats', $this->seats, 'Podaj liczbę miejsc.');

        if ((int) $this->seats > self::MAX_SEATS) {
            $this->addError('seats', 'W tym terminie można zarezerwować maksymalnie ' . self::MAX_SEATS . ' miejsca.');
        }
    }
}
