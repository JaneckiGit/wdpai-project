<?php

declare(strict_types=1);

namespace App\Form;

/* Pola jak w rejestracji z Laboratorium 9, plus zgoda na regulamin. */
final class RegisterForm extends Form
{
    private const MIN_PASSWORD_LENGTH = 8;

    public readonly string $name;
    public readonly string $email;
    public readonly bool $termsAccepted;
    private readonly string $password;
    private readonly string $passwordConfirmation;

    public function __construct(array $input = [])
    {
        $this->name = self::text($input, 'name');
        $this->email = mb_strtolower(self::text($input, 'email'));
        $this->termsAccepted = self::text($input, 'terms') === '1';
        $this->password = self::text($input, 'password', false);
        $this->passwordConfirmation = self::text($input, 'passwordConfirmation', false);
    }

    protected function validate(): void
    {
        if ($this->name === '') {
            $this->addError('name', 'Podaj imię i nazwisko.');
        }

        $this->requireEmail('email', $this->email);

        if (mb_strlen($this->password) < self::MIN_PASSWORD_LENGTH) {
            $this->addError('password', 'Hasło musi mieć co najmniej 8 znaków.');
        }

        if ($this->password !== $this->passwordConfirmation) {
            $this->addError('passwordConfirmation', 'Hasła nie są takie same.');
        }

        if (!$this->termsAccepted) {
            $this->addError('terms', 'Zaakceptuj regulamin, aby założyć konto.');
        }
    }
}
