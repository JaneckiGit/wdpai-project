<?php

declare(strict_types=1);

namespace App\Form;

/* Usunięcie konta wymaga hasła i potwierdzenia, że użytkownik rozumie skutki. */
final class DeleteAccountForm extends Form
{
    public readonly bool $confirmed;
    private readonly string $password;

    public function __construct(array $input = [])
    {
        $this->password = self::text($input, 'password', false);
        $this->confirmed = self::text($input, 'confirmed') === '1';
    }

    protected function validate(): void
    {
        if ($this->password === '') {
            $this->addError('password', 'Podaj aktualne hasło.');
        }

        if (!$this->confirmed) {
            $this->addError('confirmed', 'Potwierdź, że rozumiesz, że usunięcia konta nie da się cofnąć.');
        }
    }
}
