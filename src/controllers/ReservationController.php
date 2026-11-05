<?php

declare(strict_types=1);

namespace App\Controller;

final class ReservationController extends AppController
{
    private const STATUS_MESSAGES = [
        'cancelled' => 'Rezerwacja anulowana. Jej status zmieni się, gdy aplikacja będzie miała bazę danych.',
    ];

    // TODO: Laboratorium 8 – rezerwacje zalogowanego użytkownika z bazy zamiast przykładowych danych.
    private const SAMPLE_RESERVATIONS = [
        ['id' => 1, 'venueId' => 2, 'venueName' => 'Kawiarnia Ziarno', 'date' => 'wt 18.11', 'hours' => '18:00–20:00', 'status' => 'pending'],
        ['id' => 2, 'venueId' => 1, 'venueName' => 'Bar Lampion', 'date' => 'śr 19.11', 'hours' => '10:00–14:00', 'status' => 'accepted'],
        ['id' => 3, 'venueId' => 3, 'venueName' => 'Kawiarnia Pod Zegarem', 'date' => 'pon 10.11', 'hours' => '9:00–12:00', 'status' => 'rejected'],
    ];

    public function index(): void
    {
        $this->render('reservations', [
            'pageTitle' => 'Moje rezerwacje – PoGodzinach',
            'stylesheet' => 'reservations',
            'activeNav' => 'reservations',
            'reservations' => self::SAMPLE_RESERVATIONS,
            'statusMessage' => $this->statusMessage(self::STATUS_MESSAGES),
        ]);
    }

    public function cancel(int $id): void
    {
        if (!in_array($id, array_column(self::SAMPLE_RESERVATIONS, 'id'), true)) {
            $this->renderError(404, 'Nie znaleziono rezerwacji', 'Rezerwacja o podanym numerze nie istnieje.');
            return;
        }

        // TODO: Laboratorium 8 – zmiana statusu rezerwacji w bazie.
        $this->redirect('/reservations?status=cancelled');
    }
}
