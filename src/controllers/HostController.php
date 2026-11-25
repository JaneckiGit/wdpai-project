<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\AvailabilityWindowForm;
use App\Form\HostApplicationForm;
use App\Form\VenueForm;
use App\Model\AvailabilityWindow;
use App\Model\HostApplication;
use App\Model\Reservation;
use App\Model\Venue;
use App\Repository\DictionaryRepository;
use App\Repository\HostApplicationRepository;
use App\Repository\ReservationRepository;
use App\Repository\VenueRepository;

final class HostController extends AppController
{
    private const REQUEST_DECISIONS = ['accept', 'reject'];
    // Prośba bez decyzji gospodarza wygasa po tylu godzinach od wysłania.
    private const REQUEST_HOURS_TO_DECIDE = 24;

    private const DASHBOARD_STATUS_MESSAGES = [
        'venue-submitted' => 'Lokal wysłany do weryfikacji – pojawi się w Odkrywaj po akceptacji administratora.',
        'venue-draft' => 'Szkic lokalu zapisany – znajdziesz go w „Twoich lokalach”.',
        'request-accept' => 'Rezerwacja zaakceptowana – najemca zobaczy ją jako potwierdzoną.',
        'request-reject' => 'Prośba o rezerwację odrzucona.',
    ];

    private const APPLICATION_STATUS_MESSAGES = [
        'sent' => 'Wniosek wysłany. Administrator sprawdzi go w ciągu 1 dnia roboczego – po akceptacji w zakładce „Dla gospodarzy” otworzy się Twój panel.',
    ];

    private VenueRepository $venues;
    private ReservationRepository $reservations;
    private DictionaryRepository $dictionaries;
    private HostApplicationRepository $applications;

    public function __construct()
    {
        $this->applications = new HostApplicationRepository($this->database());
        $this->venues = new VenueRepository($this->database());
        $this->reservations = new ReservationRepository($this->database());
        $this->dictionaries = new DictionaryRepository($this->database());
    }

    public function dashboard(): void
    {
        // Panel widzi tylko gospodarz – pozostali najpierw wysyłają wniosek o tę rolę.
        if (!$this->currentUser()->isHost()) {
            $this->redirect('/host/apply');
        }

        $venues = $this->hostVenues();
        $requests = $this->reservations->findPendingForHost($this->currentUser()->getId());

        $this->render('host-dashboard', [
            'pageTitle' => 'Panel gospodarza – PoGodzinach',
            'stylesheet' => 'host',
            'activeNav' => 'host',
            'requests' => array_map(fn (Reservation $reservation): array => $this->requestDetails($reservation, $venues[$reservation->getVenueId()]), $requests),
            'venues' => $venues,
            'purposeNames' => $this->dictionaries->purposes(),
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

    /*
     * Dane prośby do widoku; kwota to miejsca × godziny × cena za miejsce albo godziny × cena za cały lokal.
     * Cenę bierzemy z okna, w którym leży termin; gdy gospodarz zmienił już okna, z pierwszego okna w tym trybie.
     */
    private function requestDetails(Reservation $reservation, Venue $venue): array
    {
        $isSeats = $reservation->getMode() === 'seats';
        $hours = $reservation->getEndHour() - $reservation->getStartHour();
        $window = $venue->findWindow($reservation->getMode(), (int) $reservation->getDate()->format('N'), $reservation->getStartHour())
            ?? $venue->getWindowsByMode()[$reservation->getMode()] ?? null;
        $price = $window?->getPrice() ?? 0.0;
        $count = $isSeats ? (int) $reservation->getSeats() : 1;
        $hoursSinceSent = intdiv(time() - $reservation->getCreatedAt()->getTimestamp(), 3600);

        return [
            'id' => $reservation->getId(),
            'guest' => (string) $reservation->getGuestName(),
            'venueName' => $venue->getName(),
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

    /* Wniosek o rolę gospodarza; imię, nazwisko i e-mail pochodzą z konta. */
    public function apply(): void
    {
        $user = $this->currentUser();

        // Gospodarz ma już panel, więc wniosku nie składa.
        if ($user->isHost()) {
            $this->redirect('/host');
        }

        $venueTypes = $this->dictionaries->venueTypes();
        $form = new HostApplicationForm($this->isPost() ? $_POST : [], array_keys($venueTypes));

        if ($this->isPost()) {
            // Jeden oczekujący wniosek na konto – tak samo pilnuje tego indeks host_applications_one_pending w bazie.
            if ($user->hasPendingApplication()) {
                $this->renderError(409, 'Wniosek już wysłany', 'Twój wniosek czeka na decyzję administratora.');
                return;
            }

            if ($form->isValid()) {
                $this->applications->save($this->newApplication($form));
                $this->redirect('/host/apply?status=sent');
            }

            http_response_code(422);
        }

        $this->render('host-application', [
            'pageTitle' => 'Zostań gospodarzem – PoGodzinach',
            'stylesheet' => 'host-application',
            'activeNav' => 'host',
            'venueTypes' => $venueTypes,
            'hasPendingApplication' => $user->hasPendingApplication(),
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::APPLICATION_STATUS_MESSAGES),
        ]);
    }

    /* Nowy wniosek z poprawnego formularza; pusty numer lokalu zapisujemy jako NULL. */
    private function newApplication(HostApplicationForm $form): HostApplication
    {
        $user = $this->currentUser();
        $address = $form->address;

        return new HostApplication(
            null,
            $user->getId(),
            $user->getFullName(),
            $user->getEmail(),
            $form->phone,
            $form->normalizedNip(),
            $form->companyName,
            $form->venueName,
            $form->venueType,
            $address->street,
            $address->buildingNumber,
            $address->unitNumber === '' ? null : $address->unitNumber,
            $address->postalCode,
            $address->city,
            $form->message,
            'pending',
        );
    }

    public function addVenue(): void
    {
        // Lokale dodaje tylko gospodarz – pozostali najpierw wysyłają wniosek o tę rolę.
        if (!$this->currentUser()->isHost()) {
            $this->redirect('/host/apply');
        }

        $purposeNames = $this->dictionaries->purposes();
        $venueTypes = $this->dictionaries->venueTypes();
        $form = new VenueForm($this->isPost() ? $_POST : [], array_keys($venueTypes), array_keys($purposeNames));

        if ($this->isPost()) {
            if ($form->isValid()) {
                // TODO: Laboratorium 13 – upload zdjęć i rolki.
                $this->venues->save($this->newVenue($form));
                $this->redirect('/host?status=' . ($form->isDraft() ? 'venue-draft' : 'venue-submitted'));
            }

            http_response_code(422);
        }

        $this->render('add-venue', [
            'pageTitle' => 'Dodaj lokal – PoGodzinach',
            'stylesheet' => 'add-venue',
            'activeNav' => 'host',
            'mapbox' => $this->mapbox(),
            'purposeNames' => $purposeNames,
            'venueTypes' => $venueTypes,
            'form' => $form,
            'errors' => $form->errors(),
        ]);
    }

    /*
     * Nowy lokal z poprawnego formularza: szkic albo lokal do weryfikacji (status pending). Puste pola szkicu
     * zapisujemy jako NULL; dzielnicy formularz nie zbiera, a udogodnienia gospodarz doda przy edycji lokalu.
     */
    private function newVenue(VenueForm $form): Venue
    {
        $user = $this->currentUser();
        $address = $form->address;
        $orNull = static fn (string $value): ?string => $value === '' ? null : $value;

        $windows = array_map(static fn (AvailabilityWindowForm $window): AvailabilityWindow => new AvailabilityWindow(
            null,
            $window->mode,
            $window->weekdays(),
            $window->startHour(),
            $window->endHour(),
            (int) $window->capacity,
            $window->priceValue(),
            (int) $window->minHours,
        ), $form->filledWindows());

        return new Venue(
            null,
            $user->getId(),
            $user->getFullName(),
            $form->name,
            $orNull($form->type),
            $orNull($address->street),
            $orNull($address->buildingNumber),
            $orNull($address->unitNumber),
            $orNull($address->postalCode),
            $orNull($address->city),
            null,
            (float) $form->latitude,
            (float) $form->longitude,
            $orNull($form->description),
            $form->entryMode,
            $orNull($form->entryInstructions),
            $form->isDraft() ? 'draft' : 'pending',
            null,
            $form->purposes,
            [],
            $windows,
        );
    }
}
