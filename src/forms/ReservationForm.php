<?php

declare(strict_types=1);

namespace App\Form;

final class ReservationForm extends Form
{
    private const DAYS_AHEAD = 7;
    public const MIN_HOURS = 2;
    // TODO: Laboratorium 8 – limit miejsc liczony z wolnych miejsc w wybranych godzinach.
    public const MAX_SEATS = 4;

    public readonly string $mode;
    public readonly string $purpose;
    public readonly string $date;
    /** @var int[] zaznaczone godziny rozpoczęcia, np. [18, 19, 20] = 18:00–21:00 */
    public readonly array $hours;
    public readonly string $seats;

    /* Lokal podaje okna gospodarza i dozwolone cele, z którymi porównujemy wybór najemcy. */
    public function __construct(private readonly array $venue, array $input = [])
    {
        $this->mode = self::textOr($input, 'mode', array_key_first($venue['windows']));
        $this->purpose = self::textOr($input, 'purpose', $venue['purposes'][0]);
        $this->date = self::text($input, 'date');
        $hours = is_array($input['hours'] ?? null) ? array_map('intval', $input['hours']) : [];
        sort($hours);
        $this->hours = $hours;
        $this->seats = self::textOr($input, 'seats', '1');
    }

    /** @return \DateTimeImmutable[] dzisiaj i kolejne dni, które można zarezerwować */
    public static function bookableDays(): array
    {
        return array_map(
            static fn (int $offset): \DateTimeImmutable => new \DateTimeImmutable('today +' . $offset . ' days'),
            range(0, self::DAYS_AHEAD - 1),
        );
    }

    protected function validate(): void
    {
        $this->requireChoice('purpose', $this->purpose, $this->venue['purposes'], 'Wybierz cel dozwolony w tym lokalu.');
        $window = $this->venue['windows'][$this->mode] ?? null;

        if ($window === null) {
            $this->addError('mode', 'Wybierz tryb rezerwacji.');
            return;
        }

        $this->validateDate($window);
        $this->validateHours($window);

        if ($this->mode === 'seats') {
            $this->validateSeats();
        }
    }

    private function validateDate(array $window): void
    {
        $date = self::parseDate($this->date);
        $bookable = array_map(static fn (\DateTimeImmutable $day): string => $day->format('Y-m-d'), self::bookableDays());

        if ($date === null || !in_array($this->date, $bookable, true)) {
            $this->addError('date', 'Wybierz dzień z najbliższego tygodnia.');
        } elseif (!in_array((int) $date->format('N'), $window['days'], true)) {
            $this->addError('date', 'Gospodarz nie udostępnia lokalu w tym dniu.');
        }
    }

    /* Godziny muszą leżeć w oknie gospodarza i iść jedna po drugiej, np. 18, 19, 20. */
    private function validateHours(array $window): void
    {
        $count = count($this->hours);

        if ($count < self::MIN_HOURS) {
            $this->addError('hours', 'Wybierz co najmniej ' . self::MIN_HOURS . ' godziny.');
        } elseif ($this->hours[0] < $window['from'] || $this->hours[$count - 1] >= $window['to']) {
            $this->addError('hours', 'Wybierz godziny z okna gospodarza.');
        } elseif ($this->hours[$count - 1] - $this->hours[0] + 1 !== $count) {
            $this->addError('hours', 'Wybierz godziny jedna po drugiej.');
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
