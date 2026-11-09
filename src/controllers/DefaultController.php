<?php

declare(strict_types=1);

namespace App\Controller;

final class DefaultController extends AppController
{
    /* Strona startowa to Odkrywaj – lista lokali z mapą. */
    public function index(): void
    {
        $this->redirect('/venues');
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => 'Nie znaleziono strony',
        ]);
    }

    public function serverError(): void
    {
        $this->renderError(500, 'Błąd serwera', 'Coś poszło nie tak po naszej stronie. Spróbuj ponownie za chwilę.');
    }
}
