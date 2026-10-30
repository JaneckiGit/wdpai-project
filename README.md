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
| Logowanie | Formularz z wyborem typu konta, e-mailem i hasłem; walidacja i przekierowanie zależne od typu konta (bez sprawdzania hasła, które dojdzie w Laboratorium 9) |
| Odkrywaj | Filtry w parametrach adresu (fraza, cel, tryb, data, godziny, liczba osób, cena) z walidacją, legenda mapy i rolki lokali |
| Lokal i rezerwacja | Opis lokalu, cele, oferta, udogodnienia, opinie i formularz prośby o rezerwację z walidacją; id lokalu z adresu trafia do adresu formularza |
| Panel gospodarza | Podsumowanie, prośby o rezerwację z działającymi przyciskami decyzji, kalendarz tygodnia w tabeli, komunikaty po przekierowaniu |
| Dodaj lokal | Formularz w sześciu krokach z walidacją, w tym wykrywaniem nakładających się okien dostępności; szkic i wysłanie do weryfikacji |
| Obsługa błędów | Kontrolowane odpowiedzi 400, 404, 422 i 500 (sekcja „Formularze i obsługa błędów”) |

Widoki to semantyczny HTML. W pełni ostylowany jest na razie widok Logowanie (Laboratorium 3, opis w sekcji „Style CSS”), pozostałe widoki mają tylko style bazowe (kolory, fonty, linki, fokus). Na każdej stronie działa motyw jasny i ciemny (Laboratorium 5, sekcja „Motyw jasny i ciemny”). Dane w widokach są przykładowe, wzięte z prototypu – listy oznaczone komentarzem `TODO` będą wyświetlane w pętli z danych z bazy. Formularze są sprawdzane po stronie serwera, ale aplikacja nie ma jeszcze bazy, więc poprawne dane nie są nigdzie zapisywane, a filtry nie zawężają listy lokali (Laboratoria 7–8). Pliki z formularza „Dodaj lokal” nie są jeszcze przetwarzane (Laboratorium 13), a przycisk „Dodaj okno” zadziała po dodaniu JavaScriptu (Laboratorium 10) – do tego czasu formularz w przeglądarce pokazuje jedno okno, a serwer obsługuje ich dowolnie wiele. Ochrona CSRF i sprawdzanie hasła dojdą w Laboratorium 9. Linki do zaplanowanych, jeszcze nieistniejących stron (np. `/register`, `/reservations`, `/terms`) kończą się odpowiedzią 404. Przełącznika języka jeszcze nie ma.

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
4. Kontroler wywołuje odziedziczoną z `AppController` metodę `render()`, która udostępnia dane widokowi jako zmienne (`extract()`), wykonuje szablon z `public/views/` do bufora (`ob_start()` / `ob_get_clean()`) i wstawia wynik jako `$content` do wspólnego dokumentu `public/views/layout.html.php`. Layout zawiera `<head>`, arkusze stylów, przełącznik motywu i skrypty, więc widoki opisują już tylko treść strony.
5. Gdy żadna trasa nie pasuje, `index.php` ustawia status 404 i wyświetla widok `not-found`.

Klasy nie są ładowane ręcznie przez `require_once`. `index.php` wczytuje tylko `vendor/autoload.php`, a autoloader Composera odnajduje plik klasy przy jej pierwszym użyciu, według reguł PSR-4 z `composer.json`:

| Namespace | Katalog | Przykład |
|---|---|---|
| `App\Controller\` | `src/controllers/` | `App\Controller\ProjectController` → `src/controllers/ProjectController.php` |
| `App\Form\` | `src/forms/` | `App\Form\LoginForm` → `src/forms/LoginForm.php` |
| `App\` | `src/` | `App\Routing` → `src/Routing.php` |

Bardziej szczegółowe reguły (`App\Controller\`, `App\Form\`) mają pierwszeństwo. Nowa klasa jest odnajdywana bez zmian w `index.php`, jeśli jej namespace i nazwa pliku odpowiadają tym regułom.

| Element | Plik | Odpowiedzialność |
|---|---|---|
| Front Controller | `public/index.php` | Konfiguracja błędów i strefy czasowej, rejestracja tras, uruchomienie routera, obsługa 404 i 500 |
| Router | `src/Routing.php` | Dopasowanie metody i adresu do kontrolera i akcji |
| Kontroler bazowy | `src/controllers/AppController.php` | Wspólne renderowanie widoków i stron błędów, `isPost()`, `redirect()`, komunikaty statusu |
| Kontrolery | `src/controllers/*Controller.php` | Wybór widoku i danych dla danej trasy, koordynacja formularza i przekierowania |
| Formularze | `src/forms/*Form.php` | Dane wpisane przez użytkownika i reguły ich walidacji |
| Layout | `public/views/layout.html.php` | Wspólny dokument HTML: `<head>`, skrypt motywu, arkusze stylów (opcjonalny arkusz widoku ze zmiennej `$stylesheet`), przełącznik motywu, `theme.js` |
| Widoki | `public/views/*.html.php` | Semantyczny HTML z danymi od kontrolera |
| Wspólne fragmenty widoków | `public/views/partials/*.html.php` | Nagłówek z nawigacją, stopka, lista błędów formularza i komunikat statusu, dołączane do widoków przez `require` |
| Autoloader | `composer.json`, `vendor/autoload.php` | Wczytywanie plików klas na podstawie namespace |

Nagłówek i stopka są wspólne dla wielu widoków, więc nie są kopiowane do każdego szablonu. Widok dołącza je instrukcją `<?php require __DIR__ . '/partials/header.html.php'; ?>`. Dołączony plik ma dostęp do tych samych zmiennych co widok, dlatego nagłówek korzysta ze zmiennej `$activeNav` przekazanej z kontrolera i oznacza bieżącą pozycję menu atrybutem `aria-current="page"`.

## Routing

| Metoda | Adres | Kontroler i akcja | Widok |
|---|---|---|---|
| GET | `/` | `DefaultController::index` | `home` |
| GET | `/projects` | `ProjectController::index` | `projects` |
| GET | `/projects/{id}` (`id` – tylko cyfry) | `ProjectController::show` | `project` |
| GET, POST | `/login` | `SecurityController::login` | `login` |
| GET | `/venues` | `VenueController::index` | `venues` (Odkrywaj) |
| GET | `/venues/{id}` (`id` – tylko cyfry) | `VenueController::show` | `venue` (Lokal i rezerwacja) |
| POST | `/venues/{id}/reservations` | `VenueController::requestReservation` | przekierowanie albo `venue` z błędami |
| GET | `/host` | `HostController::dashboard` | `host-dashboard` (Panel gospodarza) |
| GET, POST | `/host/venues/new` | `HostController::addVenue` | `add-venue` (Dodaj lokal) |
| POST | `/host/requests/{id}` | `HostController::decideRequest` | przekierowanie albo `error` (400) |
| – | każdy inny adres lub metoda | `DefaultController::notFound` (status 404) | `not-found` |

Parametry zapytania (query parameters):

| Adres | Parametry | Znaczenie |
|---|---|---|
| `/venues` | `q`, `purpose`, `mode`, `date`, `from`, `to`, `guests`, `maxPrice` | Wyszukiwanie i filtry, np. `/venues?q=Kazimierz&purpose=meetings&guests=4`; niepoprawne wartości dają status 400 i listę błędów |
| `/host`, `/venues/{id}` | `status` | Komunikat po przekierowaniu, wybierany z listy dozwolonych; nieznana wartość jest ignorowana |

Sprawdzenie z terminala:

```bash
curl -i http://localhost:8080/projects/abc
```

```bash
curl -i http://localhost:8080/venues/2
```

```bash
curl -i http://localhost:8080/venues/abc
```

```bash
curl -i "http://localhost:8080/venues?guests=0"
```

Pierwszy i trzeci adres zwracają 404, bo `{id}` przyjmuje tylko cyfry. Drugi wyświetla widok lokalu, a formularz rezerwacji wysyła dane na `/venues/2/reservations`. Czwarty zwraca 400, bo liczba osób musi być dodatnia.

## Formularze i obsługa błędów

Przepływ formularza (wzorzec Post/Redirect/Get):

1. Router kieruje `POST` do akcji kontrolera, a ta tworzy obiekt formularza z danych `$_POST`.
2. `isValid()` uruchamia reguły z klasy formularza.
3. Dane poprawne: `redirect()` odsyła status 303 na inny adres, więc odświeżenie strony nie wyśle formularza drugi raz. Komunikat pokazuje parametr `status`.
4. Dane błędne: status 422 i ten sam widok z listą błędów, czerwonymi ramkami pól (`aria-invalid`) i zachowanymi wartościami. Hasło nigdy nie wraca do formularza.

| Klasa | Formularz | Najważniejsze reguły |
|---|---|---|
| `Form` | Klasa abstrakcyjna | Wspólne reguły: wybór z listy, długość tekstu, dodatnia liczba, zakres pełnych godzin, poprawna data; odporność na tablice zamiast tekstu (`email[]=x`) |
| `LoginForm` | Logowanie | Poprawny e-mail, niepuste hasło, znany typ konta |
| `ReservationForm` | Prośba o rezerwację | Znany tryb i cel, data nie z przeszłości, pełne godziny, koniec po początku, minimum 2 h, 1–4 miejsca w trybie „Miejsca do pracy” |
| `VenueForm` | Dodaj lokal | Szkic wymaga tylko nazwy. Wysłanie do weryfikacji: typ, adres, opis, co najmniej jeden cel, poprawne okna, instrukcja wejścia przy przekazaniu klucza |
| `AvailabilityWindowForm` | Okno dostępności | Dni, godziny, tryb, liczba miejsc, cena, minimum godzin nie dłuższe niż okno; `overlaps()` wykrywa okna o wspólnym dniu i przecinających się godzinach |
| `VenueFilterForm` | Filtry w Odkrywaj (GET) | Sprawdza tylko podane parametry: cel, tryb, data, godziny, liczba osób, cena |

| Status | Kiedy |
|---|---|
| 303 | Poprawny formularz – przekierowanie (`redirect()`) |
| 400 | Nieznana decyzja gospodarza, niepoprawne filtry w adresie |
| 404 | Nieznany adres lub metoda, identyfikator niebędący liczbą albo mniejszy niż 1 |
| 422 | Błędne dane w formularzu `POST` |
| 500 | Nieoczekiwany błąd – strona bez szczegółów technicznych |

Błędy wykonania: `public/index.php` wyłącza `display_errors` i zamienia ostrzeżenia PHP na wyjątki, a blok `try/catch` wokół routera zapisuje wyjątek w logu (`docker compose logs php`), czyści bufor z niedokończonym widokiem i pokazuje stronę 500.

Dane wyświetlane z powrotem w HTML przechodzą przez `htmlspecialchars()`, więc wpisany kod (np. `<script>`) jest pokazywany jako tekst.

## Style CSS

| Plik | Zawartość |
|---|---|
| `public/css/style.css` | Zmienne CSS (tokeny projektu w wariancie jasnym i ciemnym), `box-sizing`, typografia, linki, fokus oraz wspólne komponenty: logo, przycisk, pola formularza, karty wyboru typu konta, stopka, przełącznik motywu |
| `public/css/login.css` | Układ widoku Logowanie (dołączany przez layout, gdy kontroler przekaże `'stylesheet' => 'login'`) |
| `public/js/theme.js` | Przełączanie i zapamiętywanie motywu |
| `public/images/login-hero.svg` | Ilustracja z prototypu w lewej kolumnie logowania |

Kolory, fonty, odstępy i zaokrąglenia są zapisane raz w `:root` jako zmienne nazwane według roli (np. `--color-primary`, `--color-muted`, `--space-2`, `--radius`), a reguły odwołują się do nich przez `var()`. Wartości pochodzą z prototypu: kolor marki `#B4532A`, tekst `#1C1917`, tło `#F4EFE9`, nagłówki w kroju Newsreader, tekst w Inter (oba z Google Fonts).

Widok Logowanie jest zbudowany mobile first – reguły bez media query opisują telefon, a szersze ekrany je rozszerzają:

| Szerokość | Układ |
|---|---|
| poniżej 48rem (768 px) | Jedna kolumna: ilustracja jako baner z hasłem, pod nią formularz; karty typu konta jedna pod drugą, ikona obok tekstu |
| od 48rem | Karty typu konta obok siebie (Grid, dwie kolumny), ikona nad tytułem |
| od 64rem (1024 px) | Dwie kolumny strony (Grid z `grid-template-areas`): ilustracja z hasłem, opisem i celami po lewej, formularz i stopka po prawej |

Stany interfejsu: `:hover` dla linków, przycisku, pól i kart; `:focus-visible` z obrysem 3 px w kolorze marki dla linków, przycisków i pól. Karty typu konta to etykiety z ukrytymi polami `radio` – zaznaczenie i fokus pokazuje karta dzięki `:has(input:checked)` i `:has(input:focus-visible)`, a wybór działa myszą i strzałkami.

Sprawdzone w przeglądarce na szerokościach 360, 768 i 1440 px – bez poziomego przewijania.

## Motyw jasny i ciemny

Motyw to dane: atrybut `data-theme="light"` albo `data-theme="dark"` na `<html>` wybiera zestaw wartości tych samych zmiennych CSS. Komponenty używają tylko ról (`var(--color-surface)`, `var(--color-text)` …), a blok `html[data-theme="dark"]` w `style.css` podmienia ich wartości. Nie ma osobnego arkusza dla ciemnego motywu.

Kolejność wyboru motywu:

1. zapisany wybór użytkownika – `localStorage`, klucz `theme`, wartość `light` albo `dark`;
2. jeśli zapisu nie ma – ustawienie systemu (`prefers-color-scheme`); strona reaguje też na jego zmianę w trakcie oglądania.

| Element | Plik | Działanie |
|---|---|---|
| Skrypt w `<head>` | `public/views/layout.html.php` | Ustawia `data-theme` przed pierwszym arkuszem stylów, więc strona nie miga jasnym motywem przed ciemnym (brak FOUC) |
| Przełącznik | `public/views/layout.html.php`, `.theme-toggle` w `style.css` | Element `<button>` obsługiwany myszą i klawiaturą (Tab, Enter, Spacja); `aria-label` i `aria-pressed` opisują stan, ikona (Font Awesome) pokazuje działanie: księżyc włącza ciemny, słońce jasny |
| Logika | `public/js/theme.js` | Przełącza motyw, zapisuje wybór, synchronizuje przycisk i podąża za systemem, dopóki nie ma zapisu |

Kolor marki, przyciski, logo i ilustracja logowania są takie same w obu motywach. Powrót do ustawienia systemowego (np. przy testach): w konsoli przeglądarki `localStorage.removeItem('theme'); location.reload();`.

Sprawdzone w przeglądarce: start w motywie systemowym bez zapisu, zapis po kliknięciu, zachowanie wyboru po przejściu na inną stronę i po odświeżeniu, obsługa klawiaturą, brak nadpisania zapisanego wyboru przez zmianę motywu systemu.

Kontrast par kolorów (WCAG 2.1, wymagane 4,5:1 dla tekstu i 3:1 dla obrysu fokusu i ramek pól):

| Para | Jasny | Ciemny |
|---|---|---|
| Tekst na tle | 15,3:1 | 16,2:1 |
| Tekst pomocniczy na tle | 6,7:1 | 8,6:1 |
| Tekst pomocniczy na karcie | 7,2:1 | 7,5:1 |
| Link na tle | 6,2:1 | 7,7:1 |
| Komunikat błędu na tle błędu | 5,7:1 | 8,3:1 |
| Obrys fokusu na tle | 4,4:1 | 7,7:1 |
| Ramka pola na tle | 3,3:1 | 5,0:1 |
| Biały tekst na przycisku | 5,0:1 | 5,0:1 |

## Konfiguracja PHP

Obraz PHP zawiera moduły `pdo_pgsql`, `pgsql`, `gd` (z JPEG), `zip`, `bcmath`, `opcache` oraz Composer 2. Limity uploadu w PHP: `upload_max_filesize` i `post_max_size` = 512M, `max_file_uploads` = 20 (plik `conf.d/mealplanner.ini`).

## Różnice względem konfiguracji z laboratorium

Pliki `docker-compose.yaml`, `docker/php/Dockerfile` i `docker/nginx/*` są zgodne z instrukcjami z laboratorium, z dwoma wyjątkami:

- z usługi `ollama` usunięto `gpus: all`, bo Docker Desktop na macOS zwraca błąd `could not select device driver "" with capabilities: [[gpu]]` i kontener nie startuje;
- usługa `ollama` i wolumen `ollama_data` są zakomentowane – uruchamianie modeli językowych w kontenerze (na CPU) wymaga kilku GB pamięci RAM, a Docker ma na tym komputerze do dyspozycji 7,7 GB. Usługa została dodana i uruchomiona zgodnie z zadaniem, a następnie wyłączona.

`docker/db/Dockerfile` (`FROM postgres:17-alpine`) jest wymagany przez `docker-compose.yaml`, ale instrukcja nie podaje jego treści.

Szkielet MVC (`src/Routing.php`, `src/controllers/`, `public/views/`, `public/index.php`) i `composer.json` są zgodne 1:1 z instrukcjami z Laboratorium 2, z wyjątkiem zmian wymaganych przez wspólny layout z Laboratorium 5 (opisanych niżej). Composer jest w obrazie PHP od Laboratorium 1, a `.gitignore` już zawierał wpis `vendor/`, który obejmuje `/vendor/` z instrukcji.

Kontrolery `SecurityController`, `VenueController` i `HostController`, widoki PoGodzinach i katalog `public/views/partials/` powstały w ramach zadań 2 i 3 z Laboratorium 2 (własny kontroler, trasy, szablony widoków z prototypu). Pliki z instrukcji nie zostały zmienione – w `public/index.php` doszły tylko nowe trasy i instrukcje `use`. Nazwy `SecurityController` i `login.html.php` są takie same jak w Laboratorium 9, w którym powstaje logowanie.

Style z Laboratorium 3 korzystają z przykładów z instrukcji (zmienne w `:root`, `box-sizing`, typografia, linki, `:focus-visible`, Flexbox i Grid, `min()`, `clamp()`, media query `min-width: 48rem`) z wartościami z prototypu. Świadome różnice:

- obrys fokusu ma pełny kolor marki zamiast `color-mix(in srgb, var(--color-brand) 45%, white)` – jaśniejszy kolor miał na kremowym tle kontrast tylko 1,7:1, a pełny ma 4,4:1;
- linki po najechaniu zmieniają kolor na ciemniejszy (jak w prototypie) zamiast `opacity: 0.8`;
- ramki pól formularza są ciemniejsze niż w prototypie (`#8A837C`), żeby pole było widoczne na tle z kontrastem co najmniej 3:1;
- oprócz breakpointu 48rem jest drugi, 64rem, bo dwie kolumny logowania potrzebują więcej miejsca.

Motyw z Laboratorium 5 korzysta 1:1 z kodu instrukcji: skrypt w `<head>`, znaczniki i style przełącznika `.theme-toggle`, `public/js/theme.js` oraz nazwy tokenów (`--color-bg`, `--color-primary`, `--color-focus`, `--shadow-soft` … – tokeny z Laboratorium 3 zostały do nich przemianowane). Świadome różnice:

- instrukcja zakłada wspólny `public/views/layout.html.php` z `<?= $content ?>`, którego projekt wcześniej nie miał – powstał, a `AppController::render()` buforuje widok i dołącza layout; widoki z Laboratorium 2 straciły przez to własne `<!doctype>`, `<head>` i `<body>` (treść bez zmian);
- przełącznik używa ikon Font Awesome (`fa-moon`, `fa-sun`), więc layout dołącza Font Awesome 6.7.2 z cdnjs;
- wartości tokenów pochodzą z palety PoGodzinach, nie z projektu referencyjnego; `--color-focus` jest pełnym kolorem zamiast koloru z 35% krycia (ten miał na tle kontrast poniżej 3:1), a `--color-primary` jest taki sam w obu motywach, bo biały tekst na `#B4532A` ma 5:1;
- ogólnej reguły `button { background: var(--color-primary) }` z kroku 4 nie dodano – przyciski są stylowane klasami (`.button`, `.theme-toggle`), a reguła zmieniłaby też np. przycisk wyszukiwarki;
- przełącznik ma na razie `position: fixed` (wariant z instrukcji dla projektu bez ostylowanej nawigacji); po ostylowaniu nagłówka trafi do nawigacji.

Materiały Laboratorium 6 omawiają podstawy PHP i programowanie obiektowe, ale nie zawierają kodu formularzy, więc jego struktura (`src/forms/`, klasa abstrakcyjna `Form`, stałe klas, dziedziczenie) jest własnym rozwiązaniem. Elementy z późniejszych instrukcji użyte wcześniej, żeby nie zmieniać ich potem:

- `isPost()` i `redirect()` w `AppController` oraz wzorzec jednej akcji dla `GET` i `POST` są 1:1 z Laboratorium 9;
- lista błędów w `partials/form-errors.html.php` powstała z bloku z Laboratorium 9, a klasa `auth-form__errors` jest zastąpiona wspólną `form-errors`, bo lista działa w każdym formularzu;
- błędy walidacji dostają status 422, podczas gdy kod z Laboratorium 9 zwraca wtedy domyślne 200;
- katalog `src/forms/` i wpis w `composer.json` naśladują konwencję `src/models/` i `src/repositories/` z Laboratorium 7.

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
├── AGENTS.md             # zasady pracy dla agentów AI w tym repozytorium
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
│   ├── js/               # skrypty (theme.js – motyw jasny i ciemny)
│   ├── images/           # obrazy używane w stylach
│   └── views/            # layout.html.php i szablony widoków (*.html.php)
│       └── partials/     # wspólny nagłówek i stopka
├── src/
│   ├── Routing.php       # router: trasy i dopasowanie adresów (App\Routing)
│   ├── controllers/      # AppController i kontrolery aplikacji (App\Controller)
│   └── forms/            # formularze i ich walidacja (App\Form)
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
