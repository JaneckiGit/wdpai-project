<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginForm;
use App\Form\RegisterForm;

final class SecurityController extends AppController
{
    private const LOGIN_STATUS_MESSAGES = [
        'registered' => 'Formularz rejestracji jest poprawny. Konto zostanie zapisane, gdy aplikacja będzie miała bazę danych.',
    ];

    public function login(): void
    {
        $form = new LoginForm($this->isPost() ? $_POST : []);

        if ($this->isPost()) {
            if ($form->isValid()) {
                // TODO: Laboratorium 9 – sprawdzenie hasła w bazie i zapis użytkownika w sesji.
                $this->redirect('/venues');
            }

            http_response_code(422);
        }

        $this->render('login', [
            'pageTitle' => 'Logowanie – PoGodzinach',
            'stylesheet' => 'login',
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::LOGIN_STATUS_MESSAGES),
        ]);
    }

    public function register(): void
    {
        $form = new RegisterForm($this->isPost() ? $_POST : []);

        if ($this->isPost()) {
            if ($form->isValid()) {
                // TODO: Laboratorium 9 – password_hash() i zapis konta w bazie (rola: najemca).
                $this->redirect('/login?status=registered');
            }

            http_response_code(422);
        }

        $this->render('register', [
            'pageTitle' => 'Rejestracja – PoGodzinach',
            'stylesheet' => 'login',
            'form' => $form,
            'errors' => $form->errors(),
        ]);
    }
}
