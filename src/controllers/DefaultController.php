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

    public function terms(): void
    {
        $this->renderInProgress('Regulamin');
    }

    public function contact(): void
    {
        $this->renderInProgress('Kontakt');
    }

    /* Strona z linku w stopce lub formularzu, której treść jeszcze powstaje. */
    private function renderInProgress(string $heading): void
    {
        $this->render('in-progress', [
            'pageTitle' => $heading . ' – PoGodzinach',
            'activeNav' => '',
            'heading' => $heading,
        ]);
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => 'Nie znaleziono strony',
        ]);
    }

    /* Bez konta w nagłówku: błąd mógł przyjść z bazy, więc ta strona nie może jej potrzebować. */
    public function serverError(): void
    {
        $this->renderError(500, 'Błąd serwera', 'Coś poszło nie tak po naszej stronie. Spróbuj ponownie za chwilę.', ['currentUser' => null]);
    }
}
