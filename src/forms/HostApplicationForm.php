<?php

declare(strict_types=1);

namespace App\Form;

/* Wniosek o rolę gospodarza: dane kontaktowe, firma, pierwszy lokal i opis dla administratora. */
final class HostApplicationForm extends Form
{
    public readonly string $firstName;
    public readonly string $lastName;
    public readonly string $phone;
    public readonly string $companyName;
    public readonly string $nip;
    public readonly string $venueName;
    public readonly string $venueType;
    public readonly string $address;
    public readonly string $message;
    public readonly bool $termsAccepted;

    public function __construct(array $input = [])
    {
        $this->firstName = self::text($input, 'firstName');
        $this->lastName = self::text($input, 'lastName');
        $this->phone = self::text($input, 'phone');
        $this->companyName = self::text($input, 'companyName');
        $this->nip = self::text($input, 'nip');
        $this->venueName = self::text($input, 'venueName');
        $this->venueType = self::text($input, 'venueType');
        $this->address = self::text($input, 'address');
        $this->message = self::text($input, 'message');
        $this->termsAccepted = self::text($input, 'terms') === '1';
    }

    protected function validate(): void
    {
        // Pole => [maksymalna długość, komunikat, gdy jest puste].
        $required = [
            'firstName' => [60, 'Podaj imię.'],
            'lastName' => [60, 'Podaj nazwisko.'],
            'companyName' => [120, 'Podaj nazwę firmy.'],
            'venueName' => [120, 'Podaj nazwę lokalu.'],
            'address' => [255, 'Podaj adres lokalu.'],
            'message' => [600, 'Napisz kilka zdań o sobie i lokalu.'],
        ];

        foreach ($required as $field => [$maxLength, $message]) {
            if ($this->$field === '') {
                $this->addError($field, $message);
            }

            $this->requireMaxLength($field, $this->$field, $maxLength, 'To pole może mieć maksymalnie ' . $maxLength . ' znaków.');
        }

        // Telefon: cyfry, spacje, myślniki i opcjonalny „+”, razem co najmniej 9 cyfr.
        if (preg_match('/^\+?[\d -]+$/', $this->phone) !== 1 || strlen(preg_replace('/\D/', '', $this->phone)) < 9) {
            $this->addError('phone', 'Podaj numer telefonu, np. +48 601 234 567.');
        }

        // NIP: 10 cyfr, myślniki i spacje są dozwolone.
        if (preg_match('/^\d{10}$/', str_replace(['-', ' '], '', $this->nip)) !== 1) {
            $this->addError('nip', 'NIP musi mieć 10 cyfr.');
        }

        $this->requireChoice('venueType', $this->venueType, self::VENUE_TYPES, 'Wybierz typ lokalu.');

        if (!$this->termsAccepted) {
            $this->addError('terms', 'Potwierdź, że możesz udostępniać lokal, i zaakceptuj regulamin.');
        }
    }
}
