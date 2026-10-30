<?php

declare(strict_types=1);

namespace App\Form;

final class VenueForm extends Form
{
    private const TYPES = ['cafe', 'bar', 'restaurant', 'studio'];
    private const ENTRY_MODES = ['staff', 'key'];

    public readonly string $name;
    public readonly string $type;
    public readonly string $address;
    public readonly string $description;
    public readonly string $entryMode;
    public readonly string $entryInstructions;
    public readonly string $action;

    /** @var string[] */
    public readonly array $purposes;

    /** @var AvailabilityWindowForm[] */
    public readonly array $windows;

    public function __construct(array $input = [])
    {
        $this->name = self::text($input, 'name');
        $this->type = self::text($input, 'type');
        $this->address = self::text($input, 'address');
        $this->description = self::text($input, 'description');
        $this->entryMode = self::text($input, 'entryMode') ?: 'staff';
        $this->entryInstructions = self::text($input, 'entryInstructions');
        $this->action = self::text($input, 'action');
        $this->purposes = self::stringList($input['purposes'] ?? []);
        $this->windows = self::buildWindows($input['windows'] ?? []);
    }

    public function isDraft(): bool
    {
        return $this->action === 'draft';
    }

    /* Szkic wymaga tylko nazwy lokalu, resztę gospodarz może uzupełnić przed wysłaniem do weryfikacji. */
    protected function validate(): void
    {
        $this->validateTextLengths();

        if ($this->name === '') {
            $this->addError('name', 'Podaj nazwę lokalu.');
        }

        if ($this->isDraft()) {
            return;
        }

        $this->requireChoice('type', $this->type, self::TYPES, 'Wybierz typ lokalu.');

        if ($this->address === '') {
            $this->addError('address', 'Podaj adres lokalu.');
        }

        if ($this->description === '') {
            $this->addError('description', 'Dodaj opis lokalu.');
        }

        $this->validatePurposes();
        $this->validateWindows();
        $this->validateEntry();
    }

    private function validateTextLengths(): void
    {
        $this->requireMaxLength('name', $this->name, 120, 'Nazwa lokalu może mieć maksymalnie 120 znaków.');
        $this->requireMaxLength('address', $this->address, 255, 'Adres może mieć maksymalnie 255 znaków.');
        $this->requireMaxLength('description', $this->description, 600, 'Opis może mieć maksymalnie 600 znaków.');
        $this->requireMaxLength('entryInstructions', $this->entryInstructions, 1000, 'Instrukcja wejścia może mieć maksymalnie 1000 znaków.');
    }

    private function validatePurposes(): void
    {
        if ($this->purposes === [] || array_diff($this->purposes, self::PURPOSES) !== []) {
            $this->addError('purposes', 'Zaznacz co najmniej jeden dozwolony cel.');
        }
    }

    private function validateWindows(): void
    {
        foreach ($this->windows as $window) {
            $window->isValid();
        }

        foreach ($this->windows as $index => $window) {
            foreach (array_slice($this->windows, 0, $index) as $earlierIndex => $earlier) {
                if ($window->overlaps($earlier)) {
                    $window->markOverlapWith($earlierIndex + 1);
                }
            }
        }

        foreach ($this->windows as $index => $window) {
            foreach ($window->errors() as $field => $message) {
                $this->addError('windows.' . $index . '.' . $field, 'Okno ' . ($index + 1) . ': ' . $message);
            }
        }
    }

    private function validateEntry(): void
    {
        $this->requireChoice('entryMode', $this->entryMode, self::ENTRY_MODES, 'Wybierz sposób wejścia do lokalu.');

        if ($this->entryMode === 'key' && $this->entryInstructions === '') {
            $this->addError('entryInstructions', 'Przy przekazaniu klucza podaj instrukcję wejścia.');
        }
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
