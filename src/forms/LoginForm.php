<?php

declare(strict_types=1);

namespace App\Form;

final class LoginForm extends Form
{
    private const ACCOUNT_TYPES = ['renter', 'host'];

    public readonly string $accountType;
    public readonly string $email;
    private readonly string $password;

    public function __construct(array $input = [])
    {
        $this->accountType = self::textOr($input, 'accountType', 'renter');
        $this->email = mb_strtolower(self::text($input, 'email'));
        $this->password = self::text($input, 'password', false);
    }

    protected function validate(): void
    {
        $this->requireChoice('accountType', $this->accountType, self::ACCOUNT_TYPES, 'Wybierz, jak chcesz korzystać z PoGodzinach.');

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError('email', 'Podaj poprawny adres e-mail.');
        }

        if ($this->password === '') {
            $this->addError('password', 'Podaj hasło.');
        }
    }
}
