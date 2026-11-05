<?php

declare(strict_types=1);

namespace App\Form;

/* Filtry z adresu (GET): fraza z wyszukiwarki w nagłówku, cel i tryb rezerwacji. */
final class VenueFilterForm extends Form
{
    public readonly string $query;
    public readonly string $purpose;
    public readonly string $mode;

    public function __construct(array $input = [])
    {
        $this->query = self::text($input, 'q');
        $this->purpose = self::textOr($input, 'purpose', 'work');
        $this->mode = self::textOr($input, 'mode', 'seats');
    }

    protected function validate(): void
    {
        $this->requireMaxLength('q', $this->query, 100, 'Wyszukiwana fraza może mieć maksymalnie 100 znaków.');
        $this->requireChoice('purpose', $this->purpose, self::PURPOSES, 'Wybierz cel z listy.');
        $this->requireChoice('mode', $this->mode, self::MODES, 'Wybierz tryb z listy.');
    }
}
