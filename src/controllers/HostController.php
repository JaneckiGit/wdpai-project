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

    // TODO: Laboratorium 8 – oczekujące prośby o rezerwację w lokalach zalogowanego gospodarza z bazy.
    private const SAMPLE_REQUESTS = [
        ['id' => 1, 'guest' => 'Marta Zielińska', 'company' => 'Studio Kadr', 'venueId' => 1, 'date' => 'nd 23.11',
            'from' => 10, 'to' => 14, 'purpose' => 'shoots', 'mode' => 'exclusive', 'people' => 6, 'expiresInHours' => 6],
        ['id' => 2, 'guest' => 'Ola Mazur', 'company' => '', 'venueId' => 1, 'date' => 'wt 18.11',
            'from' => 9, 'to' => 13, 'purpose' => 'work', 'mode' => 'seats', 'people' => 2, 'expiresInHours' => 11],
        ['id' => 3, 'guest' => 'Tomasz Wójcik', 'company' => '', 'venueId' => 2, 'date' => 'pon 24.11',
            'from' => 10, 'to' => 14, 'purpose' => 'meetings', 'mode' => 'exclusive', 'people' => 14, 'expiresInHours' => 15],
        ['id' => 4, 'guest' => 'Kuba Wiśniewski', 'company' => '', 'venueId' => 2, 'date' => 'pt 21.11',
            'from' => 18, 'to' => 21, 'purpose' => 'work', 'mode' => 'seats', 'people' => 3, 'expiresInHours' => 20],
        ['id' => 5, 'guest' => 'Anna Kowalska', 'company' => '', 'venueId' => 2, 'date' => 'czw 20.11',
            'from' => 18, 'to' => 22, 'purpose' => 'work', 'mode' => 'seats', 'people' => 1, 'expiresInHours' => 23],
    ];

    public function dashboard(): void
    {
        $venues = array_column(VenueController::SAMPLE_VENUES, null, 'id');

        $this->render('host-dashboard', [
            'pageTitle' => 'Panel gospodarza – PoGodzinach',
            'stylesheet' => 'host',
            'activeNav' => 'host',
            'requests' => array_map(fn (array $request): array => $this->withPrice($request, $venues[$request['venueId']]), self::SAMPLE_REQUESTS),
            'venues' => array_filter($venues, static fn (array $venue): bool => $venue['host'] === self::CURRENT_USER['name']),
            'statusMessage' => $this->statusMessage(self::DASHBOARD_STATUS_MESSAGES),
        ]);
    }

    /* Kwota prośby: miejsca × godziny × cena za miejsce albo godziny × cena za cały lokal. */
    private function withPrice(array $request, array $venue): array
    {
        $isSeats = $request['mode'] === 'seats';
        $hours = $request['to'] - $request['from'];
        $price = $isSeats ? $venue['seatPrice'] : $venue['exclusivePrice'];
        $count = $isSeats ? $request['people'] : 1;

        return $request + [
            'venueName' => $venue['name'],
            'hours' => $hours,
            'price' => $price,
            'count' => $count,
            'total' => $count * $hours * $price,
        ];
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
            'stylesheet' => 'add-venue',
            'activeNav' => 'host',
            'mapbox' => $this->mapbox(),
            'form' => $form,
            'errors' => $form->errors(),
        ]);
    }
}
