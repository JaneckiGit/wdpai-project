<?php

declare(strict_types=1);

namespace App\Controller;

/* Strony z menu konta. TODO: Laboratorium 9 – obsługa formularzy (POST, walidacja, zapis w bazie). */
final class AccountController extends AppController
{
    public function password(): void
    {
        $this->render('account-password', [
            'pageTitle' => 'Zmień hasło – PoGodzinach',
            'activeNav' => '',
        ]);
    }

    public function delete(): void
    {
        $this->render('account-delete', [
            'pageTitle' => 'Usuń konto – PoGodzinach',
            'activeNav' => '',
        ]);
    }
}
