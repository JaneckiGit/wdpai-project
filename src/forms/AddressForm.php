<?php

declare(strict_types=1);

namespace App\Form;

/*
 * Adres lokalu w kolumnach atomowych (ulica, numer budynku, numer lokalu, kod pocztowy, miasto) – wspólny dla
 * „Dodaj lokal” i „Zostań gospodarzem”. Formularz nadrzędny tworzy go z tych samych danych POST i przejmuje jego błędy.
 * Limity długości odpowiadają kolumnom w bazie (VARCHAR(120), VARCHAR(10), CHAR(6), VARCHAR(80)).
 */
final class AddressForm extends Form
{
    public readonly string $street;
    public readonly string $buildingNumber;
    public readonly string $unitNumber;
    public readonly string $postalCode;
    public readonly string $city;

    /* $required = false (szkic lokalu): pusty adres jest dozwolony, ale wpisany częściowo musi być kompletny. */
    public function __construct(array $input, private readonly bool $required = true)
    {
        $this->street = self::text($input, 'street');
        $this->buildingNumber = self::text($input, 'buildingNumber');
        $this->unitNumber = self::text($input, 'unitNumber');
        $this->postalCode = self::text($input, 'postalCode');
        $this->city = self::text($input, 'city');
    }

    public function isEmpty(): bool
    {
        return $this->street === '' && $this->buildingNumber === '' && $this->unitNumber === ''
            && $this->postalCode === '' && $this->city === '';
    }

    protected function validate(): void
    {
        if (!$this->required && $this->isEmpty()) {
            return;
        }

        // Pole => [maksymalna długość, komunikat, gdy jest puste].
        $fields = [
            'street' => [120, 'Podaj ulicę.'],
            'buildingNumber' => [10, 'Podaj numer budynku.'],
            'city' => [80, 'Podaj miasto.'],
        ];

        foreach ($fields as $field => [$maxLength, $message]) {
            if ($this->$field === '') {
                $this->addError($field, $message);
            }

            $this->requireMaxLength($field, $this->$field, $maxLength, 'To pole może mieć maksymalnie ' . $maxLength . ' znaków.');
        }

        $this->requireMaxLength('unitNumber', $this->unitNumber, 10, 'Numer lokalu może mieć maksymalnie 10 znaków.');

        if (preg_match('/^\d{2}-\d{3}$/', $this->postalCode) !== 1) {
            $this->addError('postalCode', 'Podaj kod pocztowy w formacie 00-000.');
        }
    }
}
