# PoGodzinach

**PoGodzinach** to aplikacja webowa do wynajmu lokali na godziny w czasie, gdy stoją puste albo są zamknięte – kawiarni, barów, restauracji i pracowni. Projekt na przedmiot *Wstęp do Projektowania Aplikacji Internetowych* (Politechnika Krakowska).

## Opis problemu

Wiele lokali przez dużą część dnia stoi pustych: bar rano, kawiarnia wieczorem, pracownia w dni wolne. Właściciel płaci za tę przestrzeń, ale w tych godzinach nic na niej nie zarabia. Jednocześnie wiele osób szuka miejsca na kilka godzin – do pracy zdalnej, na spotkanie lub szkolenie, sesję zdjęciową lub nagranie albo kameralne wydarzenie – a coworkingi i sale konferencyjne są drogie, mało klimatyczne albo wynajmowane na cały dzień.

PoGodzinach łączy obie strony: gospodarz udostępnia lokal w wybranych godzinach, a najemca rezerwuje go na tyle, ile potrzebuje.

## Role

| Rola | Możliwości |
|---|---|
| Najemca | Przegląda lokale w formie rolek i na mapie, filtruje je po celu, terminie i cenie, wysyła prośbę o rezerwację, po wizycie dodaje rolkę „Nagrane tutaj” |
| Gospodarz | Dodaje lokale, ustala okna dostępności, tryby i ceny, minimalny czas rezerwacji i sposób wejścia, akceptuje lub odrzuca prośby o rezerwację |
| Administrator | Moderuje rolki dodane przez gospodarzy i najemców |

Typ konta (najemca albo gospodarz) wybiera się przy rejestracji i jest zapisany w bazie. Przy logowaniu uprawnienia zawsze wynikają z roli zapisanej w bazie.

## Planowane funkcje

| Obszar | Opis |
|---|---|
| Odkrywanie | Pionowe rolki lokali zsynchronizowane z mapą, na której pinezki pokazują ceny; lokale z rolką są wyżej w wynikach |
| Tryby rezerwacji | Gospodarz ustala dla każdego okna dostępności tryb: „Na wyłączność” (cały lokal, stawka za godzinę) albo „Miejsca do pracy” (N miejsc, cena za miejsce za godzinę) |
| Cele | Praca, spotkania i szkolenia, sesje foto i wideo, kameralne wydarzenia – gospodarz zaznacza, na co pozwala, a najemca wybiera cel rezerwacji |
| Okna dostępności | Dni, godziny, tryb, liczba miejsc i cena; okna tego samego lokalu nie mogą się nakładać; minimalny czas rezerwacji ustala gospodarz (domyślnie 2 h) |
| Rezerwacja | Każda rezerwacja, także pojedynczego miejsca, jest prośbą, którą gospodarz akceptuje w ciągu 24 h; do tego czasu termin jest zablokowany, a nieprzyjęte prośby automatycznie anuluje zadanie CRON |
| Wejście do lokalu | Gospodarz wybiera dla lokalu: pracownik na miejscu albo przekazanie klucza z instrukcją widoczną po akceptacji |
| Interfejs | Wersje na telefon, tablet i komputer, tryb jasny i ciemny, język polski i angielski |

Płatności są symulowane – aplikacja wylicza kwotę rezerwacji, ale nie obsługuje płatności online.

## Prototyp

Klikalny prototyp w Figmie: [PoGodzinach – prototyp](https://www.figma.com/design/6hGVw8Jx7AhhGAp2oGK4uL) (podgląd interakcji: przycisk *Present*).

Pięć widoków, każdy w wersji na komputer (1440 px) i na telefon (390 px):

| Widok | Zawartość |
|---|---|
| Logowanie | Wybór typu konta („Szukam przestrzeni” / „Udostępniam lokal”), formularz z komunikatem błędu |
| Odkrywaj | Rolki lokali i mapa z cenami, filtry celu, terminu, liczby osób i ceny |
| Lokal i rezerwacja | Opis lokalu, dozwolone cele, oferta, opinie i formularz prośby o rezerwację z podsumowaniem kwoty |
| Panel gospodarza | Statystyki, prośby o rezerwację do akceptacji, kalendarz tygodnia |
| Dodaj lokal | Formularz w krokach: dane, cele, zdjęcia, rolka, okna dostępności z walidacją nakładania się, wejście do lokalu |

## Stan implementacji

Gotowy jest szkielet MVC z laboratoriów oraz szablony HTML pięciu widoków z prototypu:

| Obszar | Co robi |
|---|---|
| Strona główna | Widok startowy z linkiem do listy projektów (przykład z laboratorium) |
| Projekty | Lista projektów i widok szczegółów projektu o podanym id (przykład z laboratorium) |
| Logowanie | Formularz z wyborem typu konta, e-mailem i hasłem |
| Odkrywaj | Filtry (cel, tryb, data, godziny, liczba osób, cena), legenda mapy i rolki lokali |
| Lokal i rezerwacja | Opis lokalu, cele, oferta, udogodnienia, opinie i formularz prośby o rezerwację; id lokalu z adresu trafia do adresu formularza |
| Panel gospodarza | Podsumowanie, prośby o rezerwację z przyciskami decyzji i kalendarz tygodnia w tabeli |
| Dodaj lokal | Formularz w sześciu krokach, z uploadem zdjęć i rolki oraz oknem dostępności |
| Obsługa błędów | Nieznany adres zwraca widok 404 i status HTTP 404 |

Widoki to semantyczny HTML. Ostylowany jest na razie widok Logowanie (Laboratorium 3, opis w sekcji „Style CSS”), pozostałe widoki nie mają jeszcze stylów. Dane w widokach są przykładowe, wzięte z prototypu – listy oznaczone komentarzem `TODO` będą wyświetlane w pętli z danych z bazy. Formularze nie są jeszcze obsługiwane: trasy `POST` i walidacja powstaną na kolejnych laboratoriach, więc wysłanie formularza kończy się na razie odpowiedzią 404. Tak samo kończą się linki do zaplanowanych, jeszcze nieistniejących stron (np. `/register`, `/reservations`, `/host/requests`, `/terms`). Przełącznik motywu (Laboratorium 5) i języka nie są jeszcze dodane.

## Tech stack

| Warstwa | Technologie |
|---|---|
| Backend | PHP 8.3 (PHP-FPM, Composer 2) |
| Baza danych | PostgreSQL 17, pgAdmin 4 |
| Serwer HTTP | Nginx |
| Poczta (dev) | Mailpit |
| Konteneryzacja | Docker Compose |

## Szybki start

Wymagania: Docker Desktop (z Docker Compose v2) i Git.

```bash
git clone https://github.com/JaneckiGit/wdpai-project.git
cd wdpai-project
cp .env.example .env   # następnie ustaw własne hasła w .env
docker compose up --build -d
docker compose exec php composer dump-autoload   # generuje vendor/autoload.php
```

Katalog `vendor/` nie jest w repozytorium, więc bez `composer dump-autoload` aplikacja kończy się błędem `Fatal error` (brak `vendor/autoload.php`).

Bez pliku `.env` zmienne `POSTGRES_*` i `PGADMIN_*` są puste (Docker Compose pokazuje tylko ostrzeżenia), więc baza i pgAdmin nie wystartują.

## Usługi

| Usługa | Adres na komputerze | Adres w sieci Dockera | Opis |
|---|---|---|---|
| `server` (Nginx) | http://localhost:8080 | `server:80` | Aplikacja |
| `php` (PHP-FPM) | – | `php:9000` | Wykonuje kod PHP przekazany przez Nginx |
| `db` (PostgreSQL) | `localhost:5433` | `db:5432` | Baza danych |
| `pgadmin-wdpai` | http://localhost:5050 | – | Przeglądanie bazy |
| `mailpit` | http://localhost:8025 | SMTP: `mailpit:1025` | Skrzynka przechwytująca maile z aplikacji |

Porty po stronie komputera można zmienić w `.env` (`APP_PORT`, `POSTGRES_PORT`, `PGADMIN_PORT`, `MAILPIT_PORT`).

**pgAdmin** – zaloguj się danymi `PGADMIN_DEFAULT_EMAIL` / `PGADMIN_DEFAULT_PASSWORD` z `.env`, a następnie dodaj serwer: host `db`, port `5432` (port wewnątrz sieci Dockera, nie 5433), baza, użytkownik i hasło z `POSTGRES_*`.

**Skrypty startowe bazy** – pliki `*.sql` i `*.sh` z `docker/db/` są wykonywane przy starcie kontenera z pustą bazą.

**Ollama** – usługa jest zakomentowana w `docker-compose.yaml` (patrz „Różnice względem konfiguracji z laboratorium”). Aby ją włączyć, usuń znaki `#` z bloku `ollama` i z wolumenu `ollama_data`, a następnie uruchom `docker compose up -d`. API będzie dostępne pod http://localhost:11434. Docker Desktop na macOS nie udostępnia kontenerom GPU, więc modele działają na CPU; na Linux/Windows z kartą NVIDIA można dodać `gpus: all`.

Przydatne polecenia:

| Polecenie | Zastosowanie |
|---|---|
| `docker compose ps` | Stan usług |
| `docker compose logs -f` | Logi wszystkich kontenerów na żywo |
| `docker compose exec php php -v` | Wersja PHP w kontenerze |
| `docker compose exec php php -m` | Lista modułów PHP |
| `docker compose exec php composer dump-autoload` | Generuje autoloader Composera – po sklonowaniu repo i po zmianie sekcji `autoload` w `composer.json` |
| `docker compose build --no-cache` | Przebudowa obrazów bez cache |
| `docker compose down` | Zatrzymanie i usunięcie kontenerów oraz sieci (po ponownym `up` baza startuje pusta) |

## Architektura (MVC)

Aplikacja ma jeden punkt wejścia – `public/index.php` (Front Controller). Nginx przekazuje do niego każdy adres, który nie jest istniejącym plikiem (`try_files $uri $uri/ /index.php?$query_string`).

Przepływ żądania:

1. `public/index.php` rejestruje trasy w obiekcie `Routing` i przekazuje mu metodę HTTP oraz adres żądania.
2. `Routing::dispatch()` porównuje adres z listą tras (białą listą). Ścieżka trasy jest zamieniana na wyrażenie regularne, np. `/projects/{id}` → `~^/projects/(?P<id>\d+)$~`, więc `{id}` przyjmuje tylko cyfry.
3. Dla pierwszej pasującej trasy router tworzy obiekt kontrolera i wywołuje jego akcję, przekazując parametry z adresu (`/projects/12` → `ProjectController::show(12)`).
4. Kontroler wywołuje odziedziczoną z `AppController` metodę `render()`, która udostępnia dane widokowi jako zmienne (`extract()`) i dołącza szablon z `public/views/`.
5. Gdy żadna trasa nie pasuje, `index.php` ustawia status 404 i wyświetla widok `not-found`.

Klasy nie są ładowane ręcznie przez `require_once`. `index.php` wczytuje tylko `vendor/autoload.php`, a autoloader Composera odnajduje plik klasy przy jej pierwszym użyciu, według reguł PSR-4 z `composer.json`:

| Namespace | Katalog | Przykład |
|---|---|---|
| `App\Controller\` | `src/controllers/` | `App\Controller\ProjectController` → `src/controllers/ProjectController.php` |
| `App\` | `src/` | `App\Routing` → `src/Routing.php` |

Bardziej szczegółowa reguła (`App\Controller\`) ma pierwszeństwo. Nowa klasa jest odnajdywana bez zmian w `index.php`, jeśli jej namespace i nazwa pliku odpowiadają tym regułom.

| Element | Plik | Odpowiedzialność |
|---|---|---|
| Front Controller | `public/index.php` | Rejestracja tras, uruchomienie routera, obsługa 404 |
| Router | `src/Routing.php` | Dopasowanie metody i adresu do kontrolera i akcji |
| Kontroler bazowy | `src/controllers/AppController.php` | Wspólne renderowanie widoków |
| Kontrolery | `src/controllers/*Controller.php` | Wybór widoku i danych dla danej trasy |
| Widoki | `public/views/*.html.php` | Semantyczny HTML z danymi od kontrolera |
| Wspólne fragmenty widoków | `public/views/partials/*.html.php` | Nagłówek z nawigacją i stopka, dołączane do widoków przez `require` |
| Autoloader | `composer.json`, `vendor/autoload.php` | Wczytywanie plików klas na podstawie namespace |

Nagłówek i stopka są wspólne dla wielu widoków, więc nie są kopiowane do każdego szablonu. Widok dołącza je instrukcją `<?php require __DIR__ . '/partials/header.html.php'; ?>`. Dołączony plik ma dostęp do tych samych zmiennych co widok, dlatego nagłówek korzysta ze zmiennej `$activeNav` przekazanej z kontrolera i oznacza bieżącą pozycję menu atrybutem `aria-current="page"`.

## Routing

| Metoda | Adres | Kontroler i akcja | Widok |
|---|---|---|---|
| GET | `/` | `DefaultController::index` | `home` |
| GET | `/projects` | `ProjectController::index` | `projects` |
| GET | `/projects/{id}` (`id` – tylko cyfry) | `ProjectController::show` | `project` |
| GET | `/login` | `SecurityController::login` | `login` |
| GET | `/venues` | `VenueController::index` | `venues` (Odkrywaj) |
| GET | `/venues/{id}` (`id` – tylko cyfry) | `VenueController::show` | `venue` (Lokal i rezerwacja) |
| GET | `/host` | `HostController::dashboard` | `host-dashboard` (Panel gospodarza) |
| GET | `/host/venues/new` | `HostController::addVenue` | `add-venue` (Dodaj lokal) |
| – | każdy inny adres lub metoda | `DefaultController::notFound` (status 404) | `not-found` |

Sprawdzenie z terminala:

```bash
curl -i http://localhost:8080/projects/abc
curl -i http://localhost:8080/venues/2
curl -i http://localhost:8080/venues/abc
```

Pierwszy i trzeci adres zwracają 404, bo `{id}` przyjmuje tylko cyfry. Drugi wyświetla widok lokalu, a formularz rezerwacji wysyła dane na `/venues/2/reservations`.

## Style CSS

| Plik | Zawartość |
|---|---|
| `public/css/style.css` | Zmienne CSS (tokeny projektu), `box-sizing`, typografia, linki, fokus oraz wspólne komponenty: logo, przycisk, pola formularza, karty wyboru typu konta, stopka |
| `public/css/login.css` | Układ widoku Logowanie |
| `public/images/login-hero.svg` | Ilustracja z prototypu w lewej kolumnie logowania |

Kolory, fonty, odstępy i zaokrąglenia są zapisane raz w `:root` jako zmienne nazwane według roli (np. `--color-brand`, `--color-muted`, `--space-2`, `--radius`), a reguły odwołują się do nich przez `var()`. Wartości pochodzą z prototypu: kolor marki `#B4532A`, tekst `#1C1917`, tło `#F4EFE9`, nagłówki w kroju Newsreader, tekst w Inter (oba z Google Fonts).

Widok Logowanie jest zbudowany mobile first – reguły bez media query opisują telefon, a szersze ekrany je rozszerzają:

| Szerokość | Układ |
|---|---|
| poniżej 48rem (768 px) | Jedna kolumna: ilustracja jako baner z hasłem, pod nią formularz; karty typu konta jedna pod drugą, ikona obok tekstu |
| od 48rem | Karty typu konta obok siebie (Grid, dwie kolumny), ikona nad tytułem |
| od 64rem (1024 px) | Dwie kolumny strony (Grid z `grid-template-areas`): ilustracja z hasłem, opisem i celami po lewej, formularz i stopka po prawej |

Stany interfejsu: `:hover` dla linków, przycisku, pól i kart; `:focus-visible` z obrysem 3 px w kolorze marki dla linków, przycisków i pól. Karty typu konta to etykiety z ukrytymi polami `radio` – zaznaczenie i fokus pokazuje karta dzięki `:has(input:checked)` i `:has(input:focus-visible)`, a wybór działa myszą i strzałkami.

Sprawdzone w przeglądarce na szerokościach 360, 768 i 1440 px – bez poziomego przewijania.

## Konfiguracja PHP

Obraz PHP zawiera moduły `pdo_pgsql`, `pgsql`, `gd` (z JPEG), `zip`, `bcmath`, `opcache` oraz Composer 2. Limity uploadu w PHP: `upload_max_filesize` i `post_max_size` = 512M, `max_file_uploads` = 20 (plik `conf.d/mealplanner.ini`).

## Różnice względem konfiguracji z laboratorium

Pliki `docker-compose.yaml`, `docker/php/Dockerfile` i `docker/nginx/*` są zgodne z instrukcjami z laboratorium, z dwoma wyjątkami:

- z usługi `ollama` usunięto `gpus: all`, bo Docker Desktop na macOS zwraca błąd `could not select device driver "" with capabilities: [[gpu]]` i kontener nie startuje;
- usługa `ollama` i wolumen `ollama_data` są zakomentowane – uruchamianie modeli językowych w kontenerze (na CPU) wymaga kilku GB pamięci RAM, a Docker ma na tym komputerze do dyspozycji 7,7 GB. Usługa została dodana i uruchomiona zgodnie z zadaniem, a następnie wyłączona.

`docker/db/Dockerfile` (`FROM postgres:17-alpine`) jest wymagany przez `docker-compose.yaml`, ale instrukcja nie podaje jego treści.

Szkielet MVC (`src/Routing.php`, `src/controllers/`, `public/views/`, `public/index.php`) i `composer.json` są zgodne 1:1 z instrukcjami z Laboratorium 2. Composer jest w obrazie PHP od Laboratorium 1, a `.gitignore` już zawierał wpis `vendor/`, który obejmuje `/vendor/` z instrukcji.

Kontrolery `SecurityController`, `VenueController` i `HostController`, widoki PoGodzinach i katalog `public/views/partials/` powstały w ramach zadań 2 i 3 z Laboratorium 2 (własny kontroler, trasy, szablony widoków z prototypu). Pliki z instrukcji nie zostały zmienione – w `public/index.php` doszły tylko nowe trasy i instrukcje `use`. Nazwy `SecurityController` i `login.html.php` są takie same jak w Laboratorium 9, w którym powstaje logowanie.

Style z Laboratorium 3 korzystają z przykładów z instrukcji (zmienne w `:root`, `box-sizing`, typografia, linki, `:focus-visible`, Flexbox i Grid, `min()`, `clamp()`, media query `min-width: 48rem`) z wartościami z prototypu. Świadome różnice:

- obrys fokusu ma pełny kolor marki zamiast `color-mix(in srgb, var(--color-brand) 45%, white)` – jaśniejszy kolor miał na kremowym tle kontrast tylko 1,7:1, a pełny ma 4,4:1;
- linki po najechaniu zmieniają kolor na ciemniejszy (jak w prototypie) zamiast `opacity: 0.8`;
- ramki pól formularza są ciemniejsze niż w prototypie (`#8A837C`), żeby pole było widoczne na tle z kontrastem co najmniej 3:1;
- blok `[data-theme="dark"]` z kroku 1 nie jest jeszcze dodany – ciemny motyw powstaje w Laboratorium 5;
- oprócz breakpointu 48rem jest drugi, 64rem, bo dwie kolumny logowania potrzebują więcej miejsca.

## Znane ograniczenia

Wynikają z konfiguracji z laboratorium, do rozwiązania w kolejnych etapach:

| Ograniczenie | Skutek | Rozwiązanie |
|---|---|---|
| Wolumen `pg-data` jest zadeklarowany, ale nie jest podpięty do usługi `db` | Po `docker compose down` i `up` baza jest pusta | Dodać `- pg-data:/var/lib/postgresql/data` do `volumes` usługi `db` |
| Nginx ma domyślny limit 1 MB na żądanie | Upload większego pliku kończy się błędem 413, mimo limitu 512M w PHP | Dodać `client_max_body_size 512M;` w `nginx.conf` |
| Brak `.dockerignore` | `COPY . .` kopiuje `.env` i `.git` do obrazu PHP | Dodać `.dockerignore` przed publikacją obrazu (np. przy deployu) |
| Katalog `vendor/` nie jest w repozytorium i nie jest generowany przy starcie kontenera | Po sklonowaniu repo i `docker compose up` aplikacja zwraca `Fatal error`, dopóki nie uruchomi się `composer dump-autoload` | Uruchamiać `composer dump-autoload` przy starcie kontenera `php` (np. w `command` w `docker-compose.yaml`) |
| Widoki leżą w katalogu publicznym `public/views/` | Szablon można otworzyć bezpośrednio, np. `/views/project.html.php` – PHP wyświetla wtedy ostrzeżenia o brakujących zmiennych razem ze ścieżką pliku | Przenieść widoki poza `public/` (np. do `src/views/`) albo zablokować `/views/` w `nginx.conf` |

## Struktura projektu

```
.
├── docker-compose.yaml   # definicja usług
├── .env.example          # wzór zmiennych środowiskowych
├── composer.json         # reguły autoloadingu PSR-4
├── docker/
│   ├── db/               # obraz PostgreSQL i skrypty startowe bazy
│   ├── nginx/            # obraz i konfiguracja Nginx
│   └── php/              # obraz PHP-FPM z rozszerzeniami
├── public/               # katalog publiczny (document root Nginx)
│   ├── index.php         # Front Controller – jedyny punkt wejścia
│   ├── css/              # arkusze stylów (style.css – wspólny, login.css – logowanie)
│   ├── images/           # obrazy używane w stylach
│   └── views/            # szablony HTML (*.html.php)
│       └── partials/     # wspólny nagłówek i stopka
├── src/
│   ├── Routing.php       # router: trasy i dopasowanie adresów (App\Routing)
│   └── controllers/      # AppController i kontrolery aplikacji (App\Controller)
└── vendor/               # autoloader Composera (generowany, poza repozytorium)
```

Przepływ żądania: przeglądarka → `localhost:8080` → kontener `server` (Nginx, port 80) → `public/index.php` przekazany przez FastCGI do `php:9000` (PHP-FPM) → router → kontroler → widok → wygenerowany HTML wraca przez Nginx do przeglądarki.

## Zasady developmentu

- Branch dla każdej funkcji (`feature/...`, `fix/...`, `docs/...`).
- Małe, logiczne commity w konwencji `feat:`, `fix:`, `docs:`, `refactor:`, `test:`, `chore:`.
- Zmiany trafiają do `main` przez Pull Request z opcją *Create a merge commit* (bez squashowania).
- Sekrety wyłącznie w `.env` – nigdy w repozytorium.

## Autorzy

- Mateusz Janecki – całość projektu
