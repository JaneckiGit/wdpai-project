<?php

declare(strict_types=1);

namespace App\Form;

final class VenueForm extends Form
{
    private const ENTRY_MODES = ['staff', 'key'];
    // Pinezka startuje w centrum Krakowa, gospodarz przeciąga ją na swój lokal.
    private const DEFAULT_LATITUDE = '50.0614';
    private const DEFAULT_LONGITUDE = '19.9372';

    public readonly string $name;
    public readonly string $type;
    public readonly AddressForm $address;
    public readonly string $latitude;
    public readonly string $longitude;
    public readonly string $description;
    public readonly string $entryMode;
    public readonly string $entryInstructions;
    public readonly string $action;

    /** @var string[] */
    public readonly array $purposes;

    /** @var AvailabilityWindowForm[] */
    public readonly array $windows;

    /**
     * @param string[] $venueTypes kody typów lokalu ze słownika
     * @param string[] $allowedPurposes kody celów ze słownika
     */
    public function __construct(array $input, private readonly array $venueTypes, private readonly array $allowedPurposes)
    {
        $this->name = self::text($input, 'name');
        $this->type = self::text($input, 'type');
        $this->action = self::text($input, 'action');
        $this->address = new AddressForm($input, !$this->isDraft());
        $this->latitude = self::textOr($input, 'latitude', self::DEFAULT_LATITUDE);
        $this->longitude = self::textOr($input, 'longitude', self::DEFAULT_LONGITUDE);
        $this->description = self::text($input, 'description');
        $this->entryMode = self::textOr($input, 'entryMode', 'staff');
        $this->entryInstructions = self::text($input, 'entryInstructions');
        $this->purposes = self::stringList($input['purposes'] ?? []);
        $this->windows = self::buildWindows($input['windows'] ?? []);
    }

    public function isDraft(): bool
    {
        return $this->action === 'draft';
    }

    /** @return AvailabilityWindowForm[] okna, w których gospodarz coś wpisał – puste wiersze szkicu pomijamy */
    public function filledWindows(): array
    {
        return array_values(array_filter($this->windows, static fn (AvailabilityWindowForm $window): bool => !$window->isEmpty()));
    }

    /*
     * Szkic wymaga tylko nazwy, a wysłanie do weryfikacji – kompletu danych (jak venues_complete_check w bazie).
     * To, co gospodarz wpisał, musi być poprawne także w szkicu, bo baza i tak odrzuci np. zły kod pocztowy.
     */
    protected function validate(): void
    {
        $this->validateTextLengths();

        if ($this->name === '') {
            $this->addError('name', 'Podaj nazwę lokalu.');
        }

        if (!$this->isDraft() || $this->type !== '') {
            $this->requireChoice('type', $this->type, $this->venueTypes, 'Wybierz typ lokalu.');
        }

        $this->address->isValid();
        $this->addErrorsFrom($this->address);

        if (!self::isCoordinate($this->latitude, 90) || !self::isCoordinate($this->longitude, 180)) {
            $this->addError('location', 'Zaznacz lokal pinezką na mapie.');
        }

        if (!$this->isDraft() && $this->description === '') {
            $this->addError('description', 'Dodaj opis lokalu.');
        }

        $this->validatePurposes();
        $this->validateWindows();
        $this->validateEntry();
    }

    private function validateTextLengths(): void
    {
        $this->requireMaxLength('name', $this->name, 120, 'Nazwa lokalu może mieć maksymalnie 120 znaków.');
        $this->requireMaxLength('description', $this->description, 600, 'Opis może mieć maksymalnie 600 znaków.');
        $this->requireMaxLength('entryInstructions', $this->entryInstructions, 1000, 'Instrukcja wejścia może mieć maksymalnie 1000 znaków.');
    }

    private function validatePurposes(): void
    {
        if ((!$this->isDraft() && $this->purposes === []) || array_diff($this->purposes, $this->allowedPurposes) !== []) {
            $this->addError('purposes', 'Zaznacz co najmniej jeden dozwolony cel.');
        }
    }

    /* Wysłanie do weryfikacji sprawdza wszystkie wiersze okien, szkic – tylko te, w których coś wpisano. */
    private function validateWindows(): void
    {
        $windows = $this->isDraft() ? $this->filledWindows() : $this->windows;

        foreach ($windows as $window) {
            $window->isValid();
        }

        foreach ($windows as $index => $window) {
            foreach (array_slice($windows, 0, $index) as $earlierIndex => $earlier) {
                if ($window->overlaps($earlier)) {
                    $window->markOverlapWith($earlierIndex + 1);
                }
            }
        }

        foreach ($windows as $index => $window) {
            $this->addErrorsFrom($window, 'windows.' . $index . '.', 'Okno ' . ($index + 1) . ': ');
        }
    }

    private function validateEntry(): void
    {
        $this->requireChoice('entryMode', $this->entryMode, self::ENTRY_MODES, 'Wybierz sposób wejścia do lokalu.');

        if (!$this->isDraft() && $this->entryMode === 'key' && $this->entryInstructions === '') {
            $this->addError('entryInstructions', 'Przy przekazaniu klucza podaj instrukcję wejścia.');
        }
    }

    private static function isCoordinate(string $value, int $limit): bool
    {
        return is_numeric($value) && abs((float) $value) <= $limit;
    }

    /** @return string[] */
    private static function stringList(mixed $values): array
    {
        return is_array($values) ? array_values(array_filter($values, 'is_string')) : [];
    }

    /** @return AvailabilityWindowForm[] */
    private static function buildWindows(mixed $rows): array
    {
        $rows = is_array($rows) ? array_values(array_filter($rows, 'is_array')) : [];

        if ($rows === []) {
            $rows = [[]];
        }

        return array_map(static fn (array $row): AvailabilityWindowForm => new AvailabilityWindowForm($row), $rows);
    }
}
