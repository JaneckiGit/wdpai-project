# AGENTS.md

## Cel projektu
PoGodzinach to aplikacja webowa do wynajmu lokali na godziny, w czasie gdy stoją puste: kawiarni, barów, restauracji i pracowni. Najemca przegląda lokale w formie rolek i na mapie, a gospodarz akceptuje prośby o rezerwację. Technologie: PHP 8.3 (MVC bez frameworka), PostgreSQL 17, Nginx, Docker Compose.

## Struktura
- `public/index.php` — punkt wejścia aplikacji i rejestracja tras.
- `public/views/layout.html.php` — wspólny dokument HTML, do którego `render()` wstawia widok jako `$content`.
- `public/views/` — widoki HTML z fragmentami PHP; `partials/` zawiera nagłówek i stopkę.
- `public/css/style.css` — tokeny motywu jasnego i ciemnego oraz wspólne komponenty; pozostałe arkusze opisują układ pojedynczych widoków.
- `public/js/` — skrypty przeglądarki.
- `src/Routing.php` — dopasowanie tras i uruchamianie kontrolerów.
- `src/controllers/` — kontrolery w namespace `App\Controller`, dziedziczące po `AppController`.
- `src/forms/` — formularze i ich walidacja w namespace `App\Form`, dziedziczące po `Form`.
- `docker/`, `docker-compose.yaml` — środowisko uruchomieniowe.

Pełny opis architektury, tras i stylów jest w `README.md`.

## Zasady pracy
- Zachowuj `declare(strict_types=1)` w plikach PHP.
- Backend pisz obiektowo; kod proceduralny tylko w punkcie wejścia i konfiguracji.
- Nie dodawaj frameworków ani zależności bez zgody.
- Kontroler przygotowuje dane, a widok odpowiada za prezentację.
- Dane wyświetlane w HTML zabezpieczaj przez `htmlspecialchars()`.
- Dane od użytkownika sprawdzaj po stronie serwera w klasie formularza; po poprawnym `POST` przekierowuj przez `redirect()` (status 303), a przy błędach zwracaj 422 i ten sam widok.
- Kolory, odstępy i cienie zapisuj jako zmienne CSS; nowy kolor musi mieć wartość w obu motywach. Nie używaj stylów inline.
- Kod z instrukcji laboratorium przenoś 1:1, a każde odstępstwo opisz w `README.md` w sekcji „Różnice względem konfiguracji z laboratorium”.
- Teksty interfejsu i komentarze pisz po polsku, nazwy w kodzie po angielsku.
- Wprowadzaj małe zmiany i nie modyfikuj plików niezwiązanych z zadaniem.
- Pracuj na osobnej gałęzi (`feature/...`, `fix/...`, `docs/...`), z małymi commitami w konwencji `feat:`, `fix:`, `docs:`, `refactor:`, `test:`, `chore:`. Zmiany trafiają do `main` przez Pull Request.

## Uruchamianie i weryfikacja
- Start: `docker compose up --build -d`
- Autoloader po sklonowaniu repozytorium: `docker compose exec php composer dump-autoload`
- Składnia: `docker compose exec -T php php -l ścieżka/do/pliku.php`
- Po zmianie trasy sprawdź poprawny adres i odpowiedź 404, np. `curl -i http://localhost:8080/venues/abc`.
- Po zmianie stylów sprawdź szerokości 360, 768 i 1440 px oraz oba motywy.
- Przed zakończeniem pokaż `git diff` i podsumuj zmienione pliki.

## Bezpieczeństwo
- Nie odczytuj ani nie commituj sekretów z `.env`; nowe zmienne dopisuj do `.env.example` bez prawdziwych wartości.
- Nie wykonuj destrukcyjnych poleceń bez potwierdzenia.
- Nie zmieniaj wygenerowanych plików w `vendor/`.
