<?php

declare(strict_types=1);

namespace App\Form;

/* Filtry z parametrów adresu (GET): puste pole oznacza brak filtra, więc tylko podane wartości są sprawdzane. */
final class VenueFilterForm extends Form
{
    public readonly string $query;
    public readonly string $purpose;
    public readonly string $mode;
    public readonly string $date;
    public readonly string $from;
    public readonly string $to;
    public readonly string $guests;
    public readonly string $maxPrice;

    public function __construct(array $input = [])
    {
        $this->query = self::text($input, 'q');
        $this->purpose = self::textOr($input, 'purpose', 'work');
        $this->mode = self::textOr($input, 'mode', 'seats');
        $this->date = self::text($input, 'date');
        $this->from = self::text($input, 'from');
        $this->to = self::text($input, 'to');
        $this->guests = self::textOr($input, 'guests', '1');
        $this->maxPrice = self::text($input, 'maxPrice');
    }

    protected function validate(): void
    {
        $this->requireMaxLength('q', $this->query, 100, 'Wyszukiwana fraza może mieć maksymalnie 100 znaków.');
        $this->requireChoice('purpose', $this->purpose, self::PURPOSES, 'Wybierz cel z listy.');
        $this->requireChoice('mode', $this->mode, self::MODES, 'Wybierz tryb z listy.');
        $this->requirePositiveInt('guests', $this->guests, 'Liczba osób musi być dodatnią liczbą całkowitą.');

        if ($this->date !== '' && self::parseDate($this->date) === null) {
            $this->addError('date', 'Podaj poprawną datę.');
        }

        if ($this->from !== '' || $this->to !== '') {
            $this->requireHourRange('from', $this->from, 'to', $this->to);
        }

        if ($this->maxPrice !== '' && !ctype_digit($this->maxPrice)) {
            $this->addError('maxPrice', 'Cena maksymalna musi być liczbą całkowitą.');
        }
    }
}
