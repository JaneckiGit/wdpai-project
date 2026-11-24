<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ReservationRepository;

final class ReservationController extends AppController
{
    // Anulować można prośbę bez decyzji gospodarza i rezerwację już potwierdzoną.
    private const CANCELLABLE_STATUSES = ['pending', 'accepted'];

    private const STATUS_MESSAGES = [
        'cancelled' => 'Rezerwacja anulowana.',
    ];

    private ReservationRepository $reservations;

    public function __construct()
    {
        $this->reservations = new ReservationRepository($this->database());
    }

    public function index(): void
    {
        // TODO: Laboratorium 9 – tylko rezerwacje zalogowanego użytkownika (user_id z sesji).
        $this->render('reservations', [
            'pageTitle' => 'Moje rezerwacje – PoGodzinach',
            'stylesheet' => 'reservations',
            'activeNav' => 'reservations',
            'reservations' => $this->reservations->findAll(),
            'venues' => array_column(VenueController::SAMPLE_VENUES, null, 'id'),
            'cancellableStatuses' => self::CANCELLABLE_STATUSES,
            'statusMessage' => $this->statusMessage(self::STATUS_MESSAGES),
        ]);
    }

    public function cancel(int $id): void
    {
        $reservation = $this->reservations->find($id);

        if ($reservation === null) {
            $this->renderError(404, 'Nie znaleziono rezerwacji', 'Rezerwacja o podanym numerze nie istnieje.');
            return;
        }

        if (!in_array($reservation->getStatus(), self::CANCELLABLE_STATUSES, true)) {
            $this->renderError(409, 'Nie można anulować', 'Ta rezerwacja jest już odrzucona albo anulowana.');
            return;
        }

        // Rezerwacja zostaje w bazie ze statusem „anulowana”, żeby obie strony widziały historię.
        $reservation->setStatus('cancelled');
        $this->reservations->update($reservation);
        $this->redirect('/reservations?status=cancelled');
    }
}
