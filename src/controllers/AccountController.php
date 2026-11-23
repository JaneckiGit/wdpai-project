<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ChangePasswordForm;
use App\Form\DeleteAccountForm;

/* Strony z menu konta: zmiana hasła i usunięcie konta. */
final class AccountController extends AppController
{
    private const PASSWORD_STATUS_MESSAGES = [
        'changed' => 'Formularz jest poprawny. Nowe hasło zapisze się, gdy konta trafią do bazy danych.',
    ];

    public function password(): void
    {
        $form = new ChangePasswordForm($this->isPost() ? $_POST : []);

        if ($this->isPost()) {
            if ($form->isValid()) {
                // TODO: Laboratorium 9 – password_verify() aktualnego hasła i zapis password_hash() nowego.
                $this->redirect('/account/password?status=changed');
            }

            http_response_code(422);
        }

        $this->render('account-password', [
            'pageTitle' => 'Zmień hasło – PoGodzinach',
            'activeNav' => '',
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::PASSWORD_STATUS_MESSAGES),
        ]);
    }

    public function delete(): void
    {
        $form = new DeleteAccountForm($this->isPost() ? $_POST : []);

        if ($this->isPost()) {
            if ($form->isValid()) {
                // TODO: Laboratorium 9 – password_verify(), usunięcie konta z bazy i zakończenie sesji.
                $this->redirect('/login?status=account-deleted');
            }

            http_response_code(422);
        }

        $this->render('account-delete', [
            'pageTitle' => 'Usuń konto – PoGodzinach',
            'activeNav' => '',
            'form' => $form,
            'errors' => $form->errors(),
        ]);
    }
}
