<?php

declare(strict_types=1);

namespace App\Form;

/*
Formularz przechowuje dane wpisane przez użytkownika i sprawdza je po stronie serwera.
Podklasa opisuje swoje pola i reguły w validate(), a wspólne reguły są tutaj.
*/
abstract class Form
{
    protected const PURPOSES = ['work', 'meetings', 'shoots', 'events'];
    protected const MODES = ['seats', 'exclusive'];
    protected const VENUE_TYPES = ['cafe', 'bar', 'restaurant', 'workshop', 'studio', 'bookstore', 'gallery'];
    protected const MIN_PASSWORD_LENGTH = 8;

    /** @var array<string, string> pole => komunikat błędu */
    private array $errors = [];

    public function isValid(): bool
    {
        $this->errors = [];
        $this->validate();

        return $this->errors === [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    /* Atrybuty pola dla czytników ekranu: czy jest błędne i który tekst je opisuje (podpowiedź, lista błędów). */
    public function invalidAttributes(string $field, string $hintId = ''): string
    {
        if (!isset($this->errors[$field])) {
            return $hintId === '' ? '' : 'aria-describedby="' . $hintId . '"';
        }

        return 'aria-invalid="true" aria-describedby="' . trim($hintId . ' form-errors') . '"';
    }

    abstract protected function validate(): void;

    protected function addError(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }

    /* Tablica zamiast tekstu (np. email[]=x w żądaniu) jest traktowana jak puste pole. */
    /* Błędy formularza podrzędnego (adres, okno dostępności) stają się błędami tego formularza, np. „windows.0.from”. */
    protected function addErrorsFrom(Form $child, string $fieldPrefix = '', string $messagePrefix = ''): void
    {
        foreach ($child->errors() as $field => $message) {
            $this->addError($fieldPrefix . $field, $messagePrefix . $message);
        }
    }

    protected static function text(array $input, string $key, bool $trim = true): string
    {
        $value = $input[$key] ?? '';

        if (!is_string($value)) {
            return '';
        }

        return $trim ? trim($value) : $value;
    }

    /* Nie używamy ?:, bo tekst "0" też jest dla niego pusty i liczba 0 zniknęłaby przed walidacją. */
    protected static function textOr(array $input, string $key, string $default): string
    {
        $value = self::text($input, $key);

        return $value === '' ? $default : $value;
    }

    protected function requireChoice(string $field, string $value, array $allowed, string $message): void
    {
        if (!in_array($value, $allowed, true)) {
            $this->addError($field, $message);
        }
    }

    protected function requireMaxLength(string $field, string $value, int $max, string $message): void
    {
        if (mb_strlen($value) > $max) {
            $this->addError($field, $message);
        }
    }

    protected function requireEmail(string $field, string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, 'Podaj poprawny adres e-mail.');
        }
    }

    /* Nowe hasło przy rejestracji i zmianie hasła: co najmniej 8 znaków i takie samo powtórzenie. */
    protected function requireNewPassword(string $field, string $password, string $confirmationField, string $confirmation): void
    {
        if (mb_strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $this->addError($field, 'Hasło musi mieć co najmniej ' . self::MIN_PASSWORD_LENGTH . ' znaków.');
        }

        if ($password !== $confirmation) {
            $this->addError($confirmationField, 'Hasła nie są takie same.');
        }
    }

    protected function requirePositiveInt(string $field, string $value, string $message): void
    {
        if (!ctype_digit($value) || (int) $value < 1) {
            $this->addError($field, $message);
        }
    }

    protected function requireHourRange(string $fromField, string $from, string $toField, string $to): void
    {
        if (!self::isFullHour($from)) {
            $this->addError($fromField, 'Podaj pełną godzinę rozpoczęcia, np. 18:00.');
        }

        if (!self::isFullHour($to)) {
            $this->addError($toField, 'Podaj pełną godzinę zakończenia, np. 22:00.');
        }

        if (self::isFullHour($from) && self::isFullHour($to) && self::hour($from) >= self::hour($to)) {
            $this->addError($toField, 'Godzina zakończenia musi być późniejsza niż rozpoczęcia.');
        }
    }

    protected static function isFullHour(string $time): bool
    {
        return preg_match('/^([01]\d|2[0-3]):00$/', $time) === 1;
    }

    protected static function hour(string $time): int
    {
        return (int) substr($time, 0, 2);
    }

    /* Format Y-m-d sprawdzany w obie strony, bo createFromFormat zamienia np. 31 lutego na marzec. */
    protected static function parseDate(string $date): ?\DateTimeImmutable
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date ? $parsed : null;
    }
}
