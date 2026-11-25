<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\AvailabilityWindow;
use App\Model\Venue;

final class ReservationForm extends Form
{
    private const DAYS_AHEAD = 7;

    public readonly string $mode;
    public readonly string $purpose;
    public readonly string $date;
    /** @var int[] zaznaczone godziny rozpoczęcia, np. [18, 19, 20] = 18:00–21:00 */
    public readonly array $hours;
    public readonly string $seats;

    /* Lokal podaje okna gospodarza i dozwolone cele, z którymi porównujemy wybór najemcy. */
    public function __construct(private readonly Venue $venue, array $input = [])
    {
        $this->mode = self::textOr($input, 'mode', (string) array_key_first($venue->getWindowsByMode()));
        $this->purpose = self::textOr($input, 'purpose', $venue->getPurposes()[0] ?? '');
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

    /*
     * Okno gospodarza, w którym leży wybrany termin (dzień tygodnia i pierwsza godzina); null, gdy termin jest poza oknami.
     * Kontroler bierze z niego cenę, a walidacja – godziny, minimalny czas i liczbę miejsc.
     */
    public function window(): ?AvailabilityWindow
    {
        $date = self::parseDate($this->date);

        if ($date === null || $this->hours === []) {
            return null;
        }

        return $this->venue->findWindow($this->mode, (int) $date->format('N'), $this->hours[0]);
    }

    protected function validate(): void
    {
        $this->requireChoice('purpose', $this->purpose, $this->venue->getPurposes(), 'Wybierz cel dozwolony w tym lokalu.');

        if (!isset($this->venue->getWindowsByMode()[$this->mode])) {
            $this->addError('mode', 'Wybierz tryb rezerwacji.');
            return;
        }

        if (!$this->validateDate()) {
            return;
        }

        if ($this->hours === []) {
            $this->addError('hours', 'Wybierz godziny rezerwacji.');
            return;
        }

        $window = $this->window();

        if ($window === null) {
            $this->addError('hours', 'Wybierz godziny z okna gospodarza.');
            return;
        }

        $this->validateHours($window);

        if ($this->mode === 'seats') {
            $this->validateSeats($window);
        }
    }

    private function validateDate(): bool
    {
        $date = self::parseDate($this->date);
        $bookable = array_map(static fn (\DateTimeImmutable $day): string => $day->format('Y-m-d'), self::bookableDays());

        if ($date === null || !in_array($this->date, $bookable, true)) {
            $this->addError('date', 'Wybierz dzień z najbliższego tygodnia.');
            return false;
        }

        if (!$this->venue->isOpenOn($this->mode, (int) $date->format('N'))) {
            $this->addError('date', 'Gospodarz nie udostępnia lokalu w tym dniu.');
            return false;
        }

        return true;
    }

    /* Godziny muszą leżeć w oknie gospodarza, iść jedna po drugiej (np. 18, 19, 20) i trwać co najmniej minimum okna. */
    private function validateHours(AvailabilityWindow $window): void
    {
        $count = count($this->hours);

        if ($count < $window->getMinHours()) {
            $this->addError('hours', 'Wybierz co najmniej ' . $window->getMinHours() . ' godziny.');
        } elseif ($this->hours[$count - 1] >= $window->getEndHour()) {
            $this->addError('hours', 'Wybierz godziny z okna gospodarza.');
        } elseif ($this->hours[$count - 1] - $this->hours[0] + 1 !== $count) {
            $this->addError('hours', 'Wybierz godziny jedna po drugiej.');
        }
    }

    // TODO: Laboratorium 8 – limit liczony z wolnych miejsc w wybranych godzinach, a nie z liczby miejsc w oknie.
    private function validateSeats(AvailabilityWindow $window): void
    {
        $this->requirePositiveInt('seats', $this->seats, 'Podaj liczbę miejsc.');

        if ((int) $this->seats > $window->getCapacity()) {
            $this->addError('seats', 'W tym oknie można zarezerwować maksymalnie ' . $window->getCapacity() . ' miejsc.');
        }
    }
}
