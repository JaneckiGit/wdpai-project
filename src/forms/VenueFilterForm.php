<?php

declare(strict_types=1);

namespace App\Form;

/* Filtry z adresu (GET): fraza z wyszukiwarki w nagłówku, cel (ze słownika celów w bazie) i tryb rezerwacji. */
final class VenueFilterForm extends Form
{
    public readonly string $query;
    public readonly string $purpose;
    public readonly string $mode;

    /** @param string[] $purposes kody celów ze słownika, pierwszy jest domyślny */
    public function __construct(array $input, private readonly array $purposes)
    {
        $this->query = self::text($input, 'q');
        $this->purpose = self::textOr($input, 'purpose', $purposes[0] ?? '');
        $this->mode = self::textOr($input, 'mode', 'seats');
    }

    protected function validate(): void
    {
        $this->requireMaxLength('q', $this->query, 100, 'Wyszukiwana fraza może mieć maksymalnie 100 znaków.');
        $this->requireChoice('purpose', $this->purpose, $this->purposes, 'Wybierz cel z listy.');
        $this->requireChoice('mode', $this->mode, self::MODES, 'Wybierz tryb z listy.');
    }
}
