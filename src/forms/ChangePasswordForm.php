<?php

declare(strict_types=1);

namespace App\Form;

/* Zmiana hasła: aktualne hasło i dwa razy nowe. Hasła nigdy nie wracają do formularza. */
final class ChangePasswordForm extends Form
{
    private readonly string $currentPassword;
    private readonly string $newPassword;
    private readonly string $newPasswordConfirmation;

    public function __construct(array $input = [])
    {
        $this->currentPassword = self::text($input, 'currentPassword', false);
        $this->newPassword = self::text($input, 'newPassword', false);
        $this->newPasswordConfirmation = self::text($input, 'newPasswordConfirmation', false);
    }

    protected function validate(): void
    {
        if ($this->currentPassword === '') {
            $this->addError('currentPassword', 'Podaj aktualne hasło.');
        }

        $this->requireNewPassword('newPassword', $this->newPassword, 'newPasswordConfirmation', $this->newPasswordConfirmation);

        if ($this->currentPassword !== '' && $this->newPassword === $this->currentPassword) {
            $this->addError('newPassword', 'Nowe hasło musi być inne niż aktualne.');
        }
    }
}
