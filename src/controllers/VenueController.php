<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ReservationForm;
use App\Form\VenueFilterForm;
use App\Model\Reservation;
use App\Model\Venue;
use App\Repository\DictionaryRepository;
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
    private DictionaryRepository $dictionaries;

    public function __construct()
    {
        $this->venues = new VenueRepository($this->database());
        $this->reservations = new ReservationRepository($this->database());
        $this->dictionaries = new DictionaryRepository($this->database());
    }

    // TODO: Laboratorium 8 – lokale z bazy (repozytorium) zamiast przykładowych danych.
    // Okna gospodarza: dni tygodnia (1 = poniedziałek … 7 = niedziela) i godziny od–do dla każdego trybu.
    // Publiczna, bo panel gospodarza pokazuje te same lokale (do czasu repozytorium z Laboratorium 8).
    public const SAMPLE_VENUES = [
        ['id' => 1, 'name' => 'Bar Lampion', 'district' => 'Stare Miasto', 'address' => 'ul. Szpitalna 9',
            'lat' => 50.0623, 'lng' => 19.9343, 'seatPrice' => 15, 'exclusivePrice' => 160,
            'seatCount' => 10, 'capacity' => 25,
            'windows' => [
                'seats' => ['label' => 'pn–pt', 'days' => [1, 2, 3, 4, 5], 'from' => 9, 'to' => 15],
                'exclusive' => ['label' => 'niedziele', 'days' => [7], 'from' => 10, 'to' => 16],
            ],
            'purposes' => ['work', 'meetings', 'shoots'], 'host' => 'Piotr Nowak', 'reelTitle' => 'Poranek pod lampami',
            'description' => 'Bar z lampami nad długim stołem i ceglaną ścianą. Do popołudnia, zanim zaczyna się wieczorna obsługa, sala jest cicha i jasna, a w niedziele można ją wynająć na wyłączność, np. na sesję zdjęciową.',
            'amenities' => [['fa-wifi', 'Wi-Fi', 'hasło w instrukcji po akceptacji'], ['fa-plug', 'Gniazdka', 'przy stołach'], ['fa-sun', 'Duże okno', 'dobre światło do zdjęć']],
            'entry' => 'klucz w sklepie obok',
            'photos' => ['/images/login-hero.svg', '/images/login-hero.svg', '/images/login-hero.svg', '/images/login-hero.svg']],
        ['id' => 2, 'name' => 'Kawiarnia Ziarno', 'district' => 'Kazimierz', 'address' => 'ul. Estery 8',
            'lat' => 50.0512, 'lng' => 19.9466, 'seatPrice' => 15, 'exclusivePrice' => 150,
            'seatCount' => 12, 'capacity' => 20,
            'windows' => [
                'seats' => ['label' => 'wt–pt', 'days' => [2, 3, 4, 5], 'from' => 18, 'to' => 22],
                'exclusive' => ['label' => 'poniedziałki', 'days' => [1], 'from' => 9, 'to' => 21],
            ],
            'purposes' => ['work', 'meetings', 'events'], 'host' => 'Piotr Nowak', 'reelTitle' => 'Wieczór przy długim stole',
            'description' => 'Kameralna kawiarnia na Kazimierzu z długim dębowym stołem, ceglaną ścianą i oknem na podwórko. Wieczorami, gdy kończymy obsługę gości, oddajemy salę do cichej pracy, a w poniedziałki – gdy kawiarnia jest nieczynna – cały lokal na wyłączność.',
            'amenities' => [['fa-wifi', 'Wi-Fi', 'hasło w instrukcji po akceptacji'], ['fa-plug', 'Gniazdka', 'przy każdym miejscu'], ['fa-display', 'Rzutnik', 'na spotkania i szkolenia'], ['fa-mug-hot', 'Ekspres do kawy', 'do dyspozycji najemców']],
            'entry' => 'na miejscu będzie pracownik',
            'photos' => ['/images/login-hero.svg', '/images/login-hero.svg', '/images/login-hero.svg', '/images/login-hero.svg']],
        ['id' => 3, 'name' => 'Kawiarnia Pod Zegarem', 'district' => 'Kleparz', 'address' => 'ul. Długa 15',
            'lat' => 50.0668, 'lng' => 19.9420, 'seatPrice' => 20, 'exclusivePrice' => null,
            'seatCount' => 8, 'capacity' => null,
            'windows' => [
                'seats' => ['label' => 'pn–sob', 'days' => [1, 2, 3, 4, 5, 6], 'from' => 8, 'to' => 16],
            ],
            'purposes' => ['work', 'meetings'], 'host' => 'Tomasz Wrona', 'reelTitle' => 'Ranek pod zegarem',
            'description' => 'Jasna kawiarnia z dużym zegarem nad barem i stołami przy oknach. Od rana do popołudnia jest tu miejsce do cichej pracy i krótkich spotkań.',
            'amenities' => [['fa-wifi', 'Wi-Fi', 'hasło w instrukcji po akceptacji'], ['fa-plug', 'Gniazdka', 'przy oknach']],
            'entry' => 'na miejscu będzie pracownik',
            'photos' => ['/images/login-hero.svg', '/images/login-hero.svg', '/images/login-hero.svg', '/images/login-hero.svg']],
    ];

    public function index(): void
    {
        $purposeNames = $this->dictionaries->purposes();
        $form = new VenueFilterForm($_GET, array_keys($purposeNames));
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
            'purposeNames' => $purposeNames,
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
            // TODO: Laboratorium 8 – sprawdzenie, czy wybrane godziny nie są już zajęte przez inne rezerwacje.
            // Zaznaczone godziny idą jedna po drugiej, np. [18, 19, 20], więc rezerwacja kończy się o 21:00.
            $this->reservations->save(new Reservation(
                null,
                $id,
                $this->currentUser()->getId(),
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

    /* Odkrywaj i strona lokalu pokazują tylko lokale opublikowane – szkic albo lokal w weryfikacji daje 404. */
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
            'purposeNames' => $this->dictionaries->purposes(),
            'amenityNames' => $this->dictionaries->amenities(),
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
