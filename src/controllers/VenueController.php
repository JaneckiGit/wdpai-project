<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ReservationForm;

final class VenueController extends AppController
{
    private const VENUE_STATUS_MESSAGES = [
        'request-sent' => 'Formularz jest poprawny. Prośba o rezerwację zostanie zapisana, gdy aplikacja będzie miała bazę danych.',
    ];

    public function index(): void
    {
        $this->render('venues', [
            'pageTitle' => 'Odkrywaj – PoGodzinach',
            'activeNav' => 'venues',
        ]);
    }

    public function show(int $id): void
    {
        if ($id < 1) {
            $this->renderVenueNotFound();
            return;
        }

        $this->renderVenue($id, new ReservationForm());
    }

    public function requestReservation(int $id): void
    {
        if ($id < 1) {
            $this->renderVenueNotFound();
            return;
        }

        $form = new ReservationForm($_POST);

        if ($form->isValid()) {
            // TODO: Laboratorium 8 – zapis prośby w bazie i blokada terminu na 24 h.
            $this->redirect('/venues/' . $id . '?status=request-sent');
        }

        http_response_code(422);
        $this->renderVenue($id, $form);
    }

    private function renderVenue(int $id, ReservationForm $form): void
    {
        $this->render('venue', [
            'pageTitle' => 'Lokal i rezerwacja – PoGodzinach',
            'activeNav' => 'venues',
            'venueId' => $id,
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::VENUE_STATUS_MESSAGES),
        ]);
    }

    // TODO: Laboratorium 8 – 404 także dla identyfikatora, którego nie ma w bazie.
    private function renderVenueNotFound(): void
    {
        $this->renderError(404, 'Nie znaleziono lokalu', 'Taki lokal nie istnieje.');
    }
}
