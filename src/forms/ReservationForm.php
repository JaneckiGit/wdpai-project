<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\AvailabilityWindow;
use App\Model\Venue;

final class ReservationForm extends Form
{
    private const DAYS_AHEAD = 7;
    // TODO: limit miejsc liczony z wolnych miejsc w wybranych godzinach (zajętość nie jest jeszcze liczona).
    public const MAX_SEATS = 4;

    public readonly string $mode;
    public readonly string $purpose;
    public readonly string $date;
    /** @var int[] zaznaczone godziny rozpoczęcia, np. [18, 19, 20] = 18:00–21:00 */
    public readonly array $hours;
    public readonly string $seats;

    /* Lokal podaje okna gospodarza i dozwolone cele, z którymi porównujemy wybór najemcy. */
    public function __construct(private readonly Venue $venue, array $input = [])
    {
        $this->mode = self::textOr($input, 'mode', $venue->getWindows()[0]->getMode());
        $this->purpose = self::textOr($input, 'purpose', $venue->getPurposes()[0]);
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
        $this->requireChoice('purpose', $this->purpose, $this->venue->getPurposes(), 'Wybierz cel dozwolony w tym lokalu.');
        $window = $this->venue->getWindow($this->mode);

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

    private function validateDate(AvailabilityWindow $window): void
    {
        $date = self::parseDate($this->date);
        $bookable = array_map(static fn (\DateTimeImmutable $day): string => $day->format('Y-m-d'), self::bookableDays());

        if ($date === null || !in_array($this->date, $bookable, true)) {
            $this->addError('date', 'Wybierz dzień z najbliższego tygodnia.');
        } elseif (!$window->isOpenOn((int) $date->format('N'))) {
            $this->addError('date', 'Gospodarz nie udostępnia lokalu w tym dniu.');
        }
    }

    /* Godziny muszą leżeć w oknie gospodarza, iść jedna po drugiej, np. 18, 19, 20, i spełniać minimum gospodarza. */
    private function validateHours(AvailabilityWindow $window): void
    {
        $count = count($this->hours);

        if ($count < $window->getMinHours()) {
            $this->addError('hours', 'Wybierz co najmniej ' . $window->getMinHours() . ' godziny.');
        } elseif (!$window->getHours()->includes($this->hours[0]) || !$window->getHours()->includes($this->hours[$count - 1])) {
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
