<?php

declare(strict_types=1);

namespace App\Form;

/*
 * Wniosek o rolę gospodarza: telefon, firma, pierwszy lokal z adresem i opis dla administratora.
 * Imię, nazwisko i e-mail pochodzą z konta, więc formularz ich nie przyjmuje.
 */
final class HostApplicationForm extends Form
{
    public readonly string $phone;
    public readonly string $companyName;
    public readonly string $nip;
    public readonly string $venueName;
    public readonly string $venueType;
    public readonly AddressForm $address;
    public readonly string $message;
    public readonly bool $termsAccepted;

    /** @param string[] $venueTypes kody typów lokalu ze słownika */
    public function __construct(array $input, private readonly array $venueTypes)
    {
        $this->phone = self::text($input, 'phone');
        $this->companyName = self::text($input, 'companyName');
        $this->nip = self::text($input, 'nip');
        $this->venueName = self::text($input, 'venueName');
        $this->venueType = self::text($input, 'venueType');
        $this->address = new AddressForm($input);
        $this->message = self::text($input, 'message');
        $this->termsAccepted = self::text($input, 'terms') === '1';
    }

    /* NIP bez myślników i spacji – tak, jak zapisuje go baza (CHAR(10)). */
    public function normalizedNip(): string
    {
        return str_replace(['-', ' '], '', $this->nip);
    }

    protected function validate(): void
    {
        // Pole => [maksymalna długość, komunikat, gdy jest puste].
        $required = [
            'companyName' => [120, 'Podaj nazwę firmy.'],
            'venueName' => [120, 'Podaj nazwę lokalu.'],
            'message' => [600, 'Napisz kilka zdań o sobie i lokalu.'],
        ];

        foreach ($required as $field => [$maxLength, $message]) {
            if ($this->$field === '') {
                $this->addError($field, $message);
            }

            $this->requireMaxLength($field, $this->$field, $maxLength, 'To pole może mieć maksymalnie ' . $maxLength . ' znaków.');
        }

        // Telefon: cyfry, spacje, myślniki i opcjonalny „+”, razem co najmniej 9 cyfr i najwyżej 20 znaków (kolumna VARCHAR(20)).
        if (preg_match('/^\+?[\d -]{9,19}$/', $this->phone) !== 1 || strlen(preg_replace('/\D/', '', $this->phone)) < 9) {
            $this->addError('phone', 'Podaj numer telefonu, np. +48 601 234 567.');
        }

        // NIP: 10 cyfr, myślniki i spacje są dozwolone.
        if (preg_match('/^\d{10}$/', $this->normalizedNip()) !== 1) {
            $this->addError('nip', 'NIP musi mieć 10 cyfr.');
        }

        $this->requireChoice('venueType', $this->venueType, $this->venueTypes, 'Wybierz typ lokalu.');

        $this->address->isValid();
        $this->addErrorsFrom($this->address);

        if (!$this->termsAccepted) {
            $this->addError('terms', 'Potwierdź, że możesz udostępniać lokal, i zaakceptuj regulamin.');
        }
    }
}
