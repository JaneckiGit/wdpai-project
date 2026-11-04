<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\VenueForm;

final class HostController extends AppController
{
    private const REQUEST_DECISIONS = ['accept', 'reject'];

    private const DASHBOARD_STATUS_MESSAGES = [
        'venue-submitted' => 'Formularz lokalu jest poprawny. Lokal trafi do weryfikacji, gdy aplikacja będzie miała bazę danych.',
        'venue-draft' => 'Szkic lokalu jest poprawny. Zostanie zapisany, gdy aplikacja będzie miała bazę danych.',
        'request-accept' => 'Decyzja przyjęta: akceptacja. Status prośby zmieni się, gdy aplikacja będzie miała bazę danych.',
        'request-reject' => 'Decyzja przyjęta: odrzucenie. Status prośby zmieni się, gdy aplikacja będzie miała bazę danych.',
    ];

    public function dashboard(): void
    {
        $this->render('host-dashboard', [
            'pageTitle' => 'Panel gospodarza – PoGodzinach',
            'activeNav' => 'host',
            'statusMessage' => $this->statusMessage(self::DASHBOARD_STATUS_MESSAGES),
        ]);
    }

    public function decideRequest(int $id): void
    {
        $decision = $_POST['decision'] ?? null;

        if ($id < 1 || !in_array($decision, self::REQUEST_DECISIONS, true)) {
            $this->renderError(400, 'Nieprawidłowe żądanie', 'Nie rozpoznano decyzji dla tej prośby o rezerwację.');
            return;
        }

        // TODO: Laboratorium 8 – zmiana statusu prośby w bazie.
        $this->redirect('/host?status=request-' . $decision);
    }

    public function addVenue(): void
    {
        $form = new VenueForm($this->isPost() ? $_POST : []);

        if ($this->isPost()) {
            if ($form->isValid()) {
                // TODO: Laboratorium 8 – zapis lokalu i okien w bazie, Laboratorium 13 – upload zdjęć i rolki.
                $this->redirect('/host?status=' . ($form->isDraft() ? 'venue-draft' : 'venue-submitted'));
            }

            http_response_code(422);
        }

        $this->render('add-venue', [
            'pageTitle' => 'Dodaj lokal – PoGodzinach',
            'activeNav' => 'host',
            'form' => $form,
            'errors' => $form->errors(),
        ]);
    }
}
