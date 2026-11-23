<?php

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Form\HostApplicationForm;
use App\Form\VenueForm;
use App\Model\Reservation;
use App\Model\Venue;
use App\Repository\ReservationRepository;
use App\Repository\VenueRepository;

final class HostController extends AppController
{
    private const REQUEST_DECISIONS = ['accept', 'reject'];
    // Prośba bez decyzji gospodarza wygasa po tylu godzinach od wysłania.
    private const REQUEST_HOURS_TO_DECIDE = 24;

    private const DASHBOARD_STATUS_MESSAGES = [
        'venue-submitted' => 'Formularz lokalu jest poprawny. Lokal trafi do weryfikacji, gdy aplikacja będzie miała bazę danych.',
        'venue-draft' => 'Szkic lokalu jest poprawny. Zostanie zapisany, gdy aplikacja będzie miała bazę danych.',
        'request-accept' => 'Rezerwacja zaakceptowana – najemca zobaczy ją jako potwierdzoną.',
        'request-reject' => 'Prośba o rezerwację odrzucona.',
    ];

    private const APPLICATION_STATUS_MESSAGES = [
        'sent' => 'Wniosek jest poprawny. Administrator sprawdzi go, gdy aplikacja będzie miała bazę danych – decyzję dostaniesz e-mailem.',
    ];

    private VenueRepository $venues;
    private ReservationRepository $reservations;

    public function __construct()
    {
        $database = (new Database())->connect();
        $this->venues = new VenueRepository($database);
        $this->reservations = new ReservationRepository($database);
    }

    public function dashboard(): void
    {
        // Panel widzi tylko gospodarz – pozostali najpierw wysyłają wniosek o tę rolę.
        if (!$this->currentUser()->isHost()) {
            $this->redirect('/host/apply');
        }

        $venues = $this->hostVenues();
        // Prośby to oczekujące rezerwacje w lokalach zalogowanego gospodarza (repozytorium łączy je z lokalami JOIN-em).
        $requests = $this->reservations->findPendingByHost($this->currentUser()->getId());

        $this->render('host-dashboard', [
            'pageTitle' => 'Panel gospodarza – PoGodzinach',
            'stylesheet' => 'host',
            'activeNav' => 'host',
            'requests' => array_map(fn (Reservation $reservation): array => $this->requestDetails($reservation, $venues[$reservation->getVenueId()]), $requests),
            'venues' => $venues,
            'statusMessage' => $this->statusMessage(self::DASHBOARD_STATUS_MESSAGES),
        ]);
    }

    /** @return array<int, Venue> lokale zalogowanego gospodarza w każdym statusie, kluczem jest id lokalu */
    private function hostVenues(): array
    {
        $venues = [];
        foreach ($this->venues->findByHost($this->currentUser()->getId()) as $venue) {
            $venues[$venue->getId()] = $venue;
        }

        return $venues;
    }

    /* Dane prośby do widoku; kwota to miejsca × godziny × cena okna (za miejsce albo za cały lokal). */
    private function requestDetails(Reservation $reservation, Venue $venue): array
    {
        $isSeats = $reservation->getMode() === 'seats';
        $hours = $reservation->getEndHour() - $reservation->getStartHour();
        $price = $venue->getWindow($reservation->getMode())->getPrice();
        $count = $isSeats ? $reservation->getSeats() : 1;
        $hoursSinceSent = intdiv(time() - $reservation->getCreatedAt()->getTimestamp(), 3600);

        return [
            'id' => $reservation->getId(),
            // TODO: Laboratorium 9 – najemca z konta (user_id); do tego czasu rezerwuje przykładowy użytkownik.
            'guest' => $this->currentUser()->getName(),
            'venueName' => $reservation->getVenueName(),
            'date' => $reservation->getDate(),
            'from' => $reservation->getStartHour(),
            'to' => $reservation->getEndHour(),
            'purpose' => $reservation->getPurpose(),
            'mode' => $reservation->getMode(),
            'expiresInHours' => max(0, self::REQUEST_HOURS_TO_DECIDE - $hoursSinceSent),
            'hours' => $hours,
            'price' => $price,
            'count' => $count,
            'total' => $count * $hours * $price,
        ];
    }

    public function decideRequest(int $id): void
    {
        $decision = $_POST['decision'] ?? null;

        if (!in_array($decision, self::REQUEST_DECISIONS, true)) {
            $this->renderError(400, 'Nieprawidłowe żądanie', 'Nie rozpoznano decyzji dla tej prośby o rezerwację.');
            return;
        }

        $reservation = $this->reservations->find($id);

        // Gospodarz decyduje tylko o oczekujących prośbach w swoich lokalach.
        if ($reservation === null || $reservation->getStatus() !== 'pending' || !isset($this->hostVenues()[$reservation->getVenueId()])) {
            $this->renderError(404, 'Nie znaleziono prośby', 'Prośba o rezerwację nie istnieje albo ma już decyzję.');
            return;
        }

        $reservation->setStatus($decision === 'accept' ? 'accepted' : 'rejected');
        $this->reservations->update($reservation);
        $this->redirect('/host?status=request-' . $decision);
    }

    /* Wniosek o rolę gospodarza; imię i nazwisko podpowiadamy z konta. */
    public function apply(): void
    {
        [$firstName, $lastName] = explode(' ', $this->currentUser()->getName(), 2);
        $form = new HostApplicationForm($this->isPost() ? $_POST : ['firstName' => $firstName, 'lastName' => $lastName]);

        if ($this->isPost()) {
            if ($form->isValid()) {
                // TODO: Laboratorium 8 – zapis wniosku w bazie, żeby zobaczył go administrator.
                $this->redirect('/host/apply?status=sent');
            }

            http_response_code(422);
        }

        $this->render('host-application', [
            'pageTitle' => 'Zostań gospodarzem – PoGodzinach',
            'stylesheet' => 'host-application',
            'activeNav' => 'host',
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::APPLICATION_STATUS_MESSAGES),
        ]);
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
            'stylesheet' => 'add-venue',
            'activeNav' => 'host',
            'mapbox' => $this->mapbox(),
            'form' => $form,
            'errors' => $form->errors(),
        ]);
    }
}
