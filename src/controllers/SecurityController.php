<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginForm;

final class SecurityController extends AppController
{
    public function login(): void
    {
        $form = new LoginForm($this->isPost() ? $_POST : []);

        if ($this->isPost()) {
            if ($form->isValid()) {
                // TODO: Laboratorium 9 – sprawdzenie hasła w bazie i zapis użytkownika w sesji.
                $this->redirect($form->accountType === 'host' ? '/host' : '/venues');
            }

            http_response_code(422);
        }

        $this->render('login', [
            'pageTitle' => 'Logowanie – PoGodzinach',
            'stylesheet' => 'login',
            'form' => $form,
            'errors' => $form->errors(),
        ]);
    }
}
