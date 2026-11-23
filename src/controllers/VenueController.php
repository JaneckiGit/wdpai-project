<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Form\ReservationForm;
use App\Form\VenueFilterForm;
use App\Model\Reservation;
use App\Model\Venue;
use App\Repository\ReservationRepository;
use App\Repository\VenueRepository;
use DateTimeImmutable;

final class VenueController extends AppController
{
    private const VENUE_STATUS_MESSAGES = [
        'request-sent' => 'Prośba o rezerwację wysłana. Gospodarz ma 24 h na odpowiedź – status sprawdzisz w „Moich rezerwacjach”.',
    ];

    private VenueRepository $venues;
    private ReservationRepository $reservations;

    public function __construct()
    {
        // Oba repozytoria korzystają z jednego połączenia z bazą.
        $database = (new Database())->connect();
        $this->venues = new VenueRepository($database);
        $this->reservations = new ReservationRepository($database);
    }

    public function index(): void
    {
        $form = new VenueFilterForm($_GET);
        $isValid = $form->isValid();

        if (!$isValid) {
            http_response_code(400);
        }

        $this->render('venues', [
            'pageTitle' => 'Odkrywaj – PoGodzinach',
            'stylesheet' => 'venues',
            'activeNav' => 'venues',
            'form' => $form,
            'errors' => $form->errors(),
            'searchQuery' => $form->query,
            'venues' => $isValid ? $this->venues->findPublished($form->purpose, $form->mode, $form->query) : [],
            'mapbox' => $this->mapbox(),
        ]);
    }

    public function show(int $id): void
    {
        $venue = $this->findPublishedVenue($id);

        if ($venue === null) {
            $this->renderVenueNotFound();
            return;
        }

        $this->renderVenue($venue, new ReservationForm($venue));
    }

    public function requestReservation(int $id): void
    {
        $venue = $this->findPublishedVenue($id);

        if ($venue === null) {
            $this->renderVenueNotFound();
            return;
        }

        $form = new ReservationForm($venue, $_POST);

        if ($form->isValid()) {
            // TODO: sprawdzenie, czy wybrane godziny nie są już zajęte przez inne rezerwacje (zajętość nie jest jeszcze liczona).
            // Zaznaczone godziny idą jedna po drugiej, np. [18, 19, 20], więc rezerwacja kończy się o 21:00.
            $this->reservations->save(new Reservation(
                null,
                $this->currentUser()->getId(),
                $id,
                $form->mode,
                $form->purpose,
                new DateTimeImmutable($form->date),
                $form->hours[0],
                $form->hours[count($form->hours) - 1] + 1,
                $form->mode === 'seats' ? (int) $form->seats : null,
                'pending',
            ));
            $this->redirect('/venues/' . $id . '?status=request-sent');
        }

        http_response_code(422);
        $this->renderVenue($venue, $form);
    }

    /* Najemca widzi i rezerwuje tylko opublikowane lokale – szkic albo lokal w weryfikacji daje 404. */
    private function findPublishedVenue(int $id): ?Venue
    {
        $venue = $this->venues->find($id);

        return $venue !== null && $venue->isPublished() ? $venue : null;
    }

    private function renderVenue(Venue $venue, ReservationForm $form): void
    {
        $this->render('venue', [
            'pageTitle' => $venue->getName() . ' – PoGodzinach',
            'stylesheet' => 'venue',
            'activeNav' => 'venues',
            'venue' => $venue,
            'days' => ReservationForm::bookableDays(),
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::VENUE_STATUS_MESSAGES),
        ]);
    }

    private function renderVenueNotFound(): void
    {
        $this->renderError(404, 'Nie znaleziono lokalu', 'Taki lokal nie istnieje.');
    }
}
