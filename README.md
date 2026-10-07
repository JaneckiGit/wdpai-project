# WDPAI Project

Projekt na przedmiot *Wstęp do Projektowania Aplikacji Internetowych* (Politechnika Krakowska). Temat aplikacji – do ustalenia: jaki problem rozwiązujemy, dla kogo i w jaki sposób.

## Funkcje

| Obszar | Co robi |
|---|---|
| Strona startowa | Wyświetla powitanie wygenerowane przez PHP |

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
```

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
| `docker compose build --no-cache` | Przebudowa obrazów bez cache |
| `docker compose down` | Zatrzymanie i usunięcie kontenerów oraz sieci (po ponownym `up` baza startuje pusta) |

## Konfiguracja PHP

Obraz PHP zawiera moduły `pdo_pgsql`, `pgsql`, `gd` (z JPEG), `zip`, `bcmath`, `opcache` oraz Composer 2. Limity uploadu w PHP: `upload_max_filesize` i `post_max_size` = 512M, `max_file_uploads` = 20 (plik `conf.d/mealplanner.ini`).

## Różnice względem konfiguracji z laboratorium

Pliki `docker-compose.yaml`, `docker/php/Dockerfile` i `docker/nginx/*` są zgodne z instrukcjami z laboratorium, z dwoma wyjątkami:

- z usługi `ollama` usunięto `gpus: all`, bo Docker Desktop na macOS zwraca błąd `could not select device driver "" with capabilities: [[gpu]]` i kontener nie startuje;
- usługa `ollama` i wolumen `ollama_data` są zakomentowane – uruchamianie modeli językowych w kontenerze (na CPU) wymaga kilku GB pamięci RAM, a Docker ma na tym komputerze do dyspozycji 7,7 GB. Usługa została dodana i uruchomiona zgodnie z zadaniem, a następnie wyłączona.

`docker/db/Dockerfile` (`FROM postgres:17-alpine`) jest wymagany przez `docker-compose.yaml`, ale instrukcja nie podaje jego treści.

## Znane ograniczenia

Wynikają z konfiguracji z laboratorium, do rozwiązania w kolejnych etapach:

| Ograniczenie | Skutek | Rozwiązanie |
|---|---|---|
| Wolumen `pg-data` jest zadeklarowany, ale nie jest podpięty do usługi `db` | Po `docker compose down` i `up` baza jest pusta | Dodać `- pg-data:/var/lib/postgresql/data` do `volumes` usługi `db` |
| Nginx ma domyślny limit 1 MB na żądanie | Upload większego pliku kończy się błędem 413, mimo limitu 512M w PHP | Dodać `client_max_body_size 512M;` w `nginx.conf` |
| Brak `.dockerignore` | `COPY . .` kopiuje `.env` i `.git` do obrazu PHP | Dodać `.dockerignore` przed publikacją obrazu (np. przy deployu) |

## Struktura projektu

```
.
├── docker-compose.yaml   # definicja usług
├── .env.example          # wzór zmiennych środowiskowych
├── docker/
│   ├── db/               # obraz PostgreSQL i skrypty startowe bazy
│   ├── nginx/            # obraz i konfiguracja Nginx
│   └── php/              # obraz PHP-FPM z rozszerzeniami
└── public/               # katalog publiczny (document root Nginx)
    └── index.php
```

Przepływ żądania: przeglądarka → `localhost:8080` → kontener `server` (Nginx, port 80) → pliki `.php` przekazywane przez FastCGI do `php:9000` (PHP-FPM) → wygenerowany HTML wraca przez Nginx do przeglądarki.

## Zasady developmentu

- Branch dla każdej funkcji (`feature/...`, `fix/...`, `docs/...`).
- Małe, logiczne commity w konwencji `feat:`, `fix:`, `docs:`, `refactor:`, `test:`, `chore:`.
- Zmiany trafiają do `main` przez Pull Request z opcją *Create a merge commit* (bez squashowania).
- Sekrety wyłącznie w `.env` – nigdy w repozytorium.

## Autorzy

- Mateusz Janecki – całość projektu
