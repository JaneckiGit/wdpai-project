<?php

declare(strict_types=1);

namespace App\Form;

final class LoginForm extends Form
{
    public readonly string $email;
    private readonly string $password;

    public function __construct(array $input = [])
    {
        $this->email = mb_strtolower(self::text($input, 'email'));
        $this->password = self::text($input, 'password', false);
    }

    protected function validate(): void
    {
        $this->requireEmail('email', $this->email);

        if ($this->password === '') {
            $this->addError('password', 'Podaj hasło.');
        }
    }
}
