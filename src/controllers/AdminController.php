<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserFilterForm;

// TODO: Laboratorium 9 – panel tylko dla zalogowanego administratora (rola z sesji).
final class AdminController extends AppController
{
    private const APPLICATION_DECISIONS = ['approve', 'reject'];
    private const VENUE_DECISIONS = ['publish', 'reject'];
    private const USER_ACTIONS = ['block', 'unblock', 'revoke-host'];

    // TODO: Laboratorium 8 – decyzje zapisane w bazie zamiast samego komunikatu.
    private const STATUS_MESSAGES = [
        'application-approve' => 'Wniosek zatwierdzony. Użytkownik dostanie rolę gospodarza, gdy wnioski trafią do bazy danych.',
        'application-reject' => 'Wniosek odrzucony. Decyzja zapisze się, gdy wnioski trafią do bazy danych.',
        'venue-publish' => 'Lokal opublikowany. Pojawi się w Odkrywaj, gdy lokale trafią do bazy danych.',
        'venue-reject' => 'Lokal odrzucony. Decyzja zapisze się, gdy lokale trafią do bazy danych.',
    ];

    // Akcje na kontach przekierowują do tabeli użytkowników (#users), więc ich komunikat pokazuje się przy tabeli.
    private const USER_STATUS_MESSAGES = [
        'user-block' => 'Użytkownik zablokowany. Blokada zapisze się, gdy konta trafią do bazy danych.',
        'user-unblock' => 'Użytkownik odblokowany. Zmiana zapisze się, gdy konta trafią do bazy danych.',
        'user-revoke-host' => 'Rola gospodarza odebrana. Zmiana zapisze się, gdy konta trafią do bazy danych.',
    ];

    // TODO: Laboratorium 8 – wnioski, lokale do akceptacji i użytkownicy z bazy zamiast przykładowych danych.
    private const SAMPLE_APPLICATIONS = [
        ['id' => 1, 'name' => 'Anna Kowalska', 'email' => 'anna.kowalska@example.com', 'sent' => 'dziś 9:40', 'waitingHours' => 2,
            'phone' => '+48 601 234 567', 'company' => 'Witraż Anna Kowalska', 'nip' => '676-248-10-35',
            'venue' => 'Pracownia Szkła Witraż', 'venueDetails' => 'ul. Józefińska 12, Podgórze · Pracownia',
            'message' => 'Prowadzę pracownię witrażu w Podgórzu. Od poniedziałku do środy po 16:00 pracownia stoi pusta – chcę ją udostępniać na warsztaty, sesje zdjęciowe i spotkania do 10 osób.'],
        ['id' => 2, 'name' => 'Paweł Kruk', 'email' => 'pawel.kruk@example.com', 'sent' => 'wczoraj 18:15', 'waitingHours' => 17,
            'phone' => '+48 512 880 341', 'company' => 'Echo Studio sp. z o.o.', 'nip' => '945-221-77-60',
            'venue' => 'Studio Nagrań Echo', 'venueDetails' => 'ul. Grzegórzecka 21, Grzegórzki · Studio',
            'message' => 'Studio nagrań z salą 40 m². W weekendy do 14:00 stoi wolne – na podcasty, sesje i małe szkolenia.'],
        ['id' => 3, 'name' => 'Monika Sowa', 'email' => 'monika.sowa@example.com', 'sent' => 'wczoraj 11:02', 'waitingHours' => 31,
            'phone' => '+48 698 117 402', 'company' => 'Księgarnia Strona s.c.', 'nip' => '677-239-51-08',
            'venue' => 'Księgarnia Strona', 'venueDetails' => 'ul. Miodowa 9, Kazimierz · Księgarnia z kawiarnią',
            'message' => 'Po 19:00 księgarnia jest zamknięta. Chcemy wynajmować antresolę na spotkania autorskie i kluby książki.'],
    ];

    private const SAMPLE_PENDING_VENUES = [
        ['id' => 4, 'name' => 'Piwnica Lampion', 'host' => 'Piotr Nowak', 'address' => 'ul. Szewska 14, Stare Miasto', 'sent' => 'dziś 8:15',
            'purposes' => ['meetings', 'events'], 'seatPrice' => null, 'seatCount' => null, 'exclusivePrice' => 180, 'capacity' => 24,
            'hasReel' => false],
        ['id' => 5, 'name' => 'Atelier Len', 'host' => 'Joanna Wrona', 'address' => 'ul. Krakowska 30, Kazimierz', 'sent' => 'wczoraj 20:30',
            'purposes' => ['work', 'shoots'], 'seatPrice' => 16, 'seatCount' => 8, 'exclusivePrice' => 140, 'capacity' => 12,
            'hasReel' => true],
    ];

    private const SAMPLE_USERS = [
        ['id' => 1, 'name' => 'Anna Kowalska', 'email' => 'anna.kowalska@example.com', 'isHost' => false, 'status' => 'application', 'lastSeen' => 'dziś 9:40'],
        ['id' => 2, 'name' => 'Piotr Nowak', 'email' => 'piotr.nowak@example.com', 'isHost' => true, 'status' => 'active', 'lastSeen' => 'dziś 8:15'],
        ['id' => 3, 'name' => 'Tomasz Wrona', 'email' => 'tomasz.wrona@example.com', 'isHost' => true, 'status' => 'active', 'lastSeen' => 'wczoraj 21:40'],
        ['id' => 4, 'name' => 'Marta Zielińska', 'email' => 'marta.zielinska@example.com', 'isHost' => false, 'status' => 'active', 'lastSeen' => 'wczoraj 21:05'],
        ['id' => 5, 'name' => 'Tomasz Wójcik', 'email' => 'tomasz.wojcik@example.com', 'isHost' => false, 'status' => 'active', 'lastSeen' => 'wczoraj 16:30'],
        ['id' => 6, 'name' => 'Adam Nowicki', 'email' => 'adam.nowicki@example.com', 'isHost' => false, 'status' => 'blocked', 'lastSeen' => '12.11'],
    ];

    public function dashboard(): void
    {
        $form = new UserFilterForm($_GET);
        $isValid = $form->isValid();

        if (!$isValid) {
            http_response_code(400);
        }

        $this->render('admin', [
            'pageTitle' => 'Panel administratora – PoGodzinach',
            'stylesheet' => 'admin',
            'activeNav' => '',
            'applications' => self::SAMPLE_APPLICATIONS,
            'pendingVenues' => self::SAMPLE_PENDING_VENUES,
            'users' => $isValid ? $this->findUsers($form) : [],
            'groups' => UserFilterForm::GROUPS,
            'form' => $form,
            'errors' => $form->errors(),
            'statusMessage' => $this->statusMessage(self::STATUS_MESSAGES),
            'userStatusMessage' => $this->statusMessage(self::USER_STATUS_MESSAGES),
        ]);
    }

    public function decideApplication(int $id): void
    {
        $decision = $_POST['decision'] ?? null;

        if (!in_array($decision, self::APPLICATION_DECISIONS, true)) {
            $this->renderError(400, 'Nieprawidłowe żądanie', 'Nie rozpoznano decyzji dla tego wniosku.');
            return;
        }

        if (!in_array($id, array_column(self::SAMPLE_APPLICATIONS, 'id'), true)) {
            $this->renderError(404, 'Nie znaleziono wniosku', 'Wniosek o podanym numerze nie istnieje.');
            return;
        }

        $this->redirect('/admin?status=application-' . $decision);
    }

    public function decideVenue(int $id): void
    {
        $decision = $_POST['decision'] ?? null;

        if (!in_array($decision, self::VENUE_DECISIONS, true)) {
            $this->renderError(400, 'Nieprawidłowe żądanie', 'Nie rozpoznano decyzji dla tego lokalu.');
            return;
        }

        if (!in_array($id, array_column(self::SAMPLE_PENDING_VENUES, 'id'), true)) {
            $this->renderError(404, 'Nie znaleziono lokalu', 'Lokal o podanym numerze nie czeka na akceptację.');
            return;
        }

        $this->redirect('/admin?status=venue-' . $decision);
    }

    /* Blokada, odblokowanie albo odebranie roli gospodarza; akcja musi pasować do obecnego stanu konta. */
    public function updateUser(int $id): void
    {
        $action = $_POST['action'] ?? null;

        if (!in_array($action, self::USER_ACTIONS, true)) {
            $this->renderError(400, 'Nieprawidłowe żądanie', 'Nie rozpoznano akcji dla tego użytkownika.');
            return;
        }

        $user = array_column(self::SAMPLE_USERS, null, 'id')[$id] ?? null;

        if ($user === null) {
            $this->renderError(404, 'Nie znaleziono użytkownika', 'Użytkownik o podanym numerze nie istnieje.');
            return;
        }

        $isAllowed = match ($action) {
            'block' => $user['status'] !== 'blocked',
            'unblock' => $user['status'] === 'blocked',
            'revoke-host' => $user['isHost'],
        };

        if (!$isAllowed) {
            $this->renderError(409, 'Nie można wykonać tej akcji', 'Konto jest już w tym stanie albo nie ma roli gospodarza.');
            return;
        }

        $this->redirect('/admin?status=user-' . $action . '#users');
    }

    /* Użytkownik pasuje, gdy należy do wybranej grupy i ma szukaną frazę w imieniu albo e-mailu. */
    private function findUsers(UserFilterForm $form): array
    {
        return array_values(array_filter(
            self::SAMPLE_USERS,
            static fn (array $user): bool => ($form->group !== 'hosts' || $user['isHost'])
                && ($form->group !== 'blocked' || $user['status'] === 'blocked')
                && ($form->query === '' || mb_stripos($user['name'] . ' ' . $user['email'], $form->query) !== false),
        ));
    }
}
