# WDPAI Project

Projekt na przedmiot *Wstęp do Projektowania Aplikacji Internetowych* (Politechnika Krakowska). Temat aplikacji — do ustalenia: jaki problem rozwiązujemy, dla kogo i w jaki sposób.

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
| AI | Ollama |
| Konteneryzacja | Docker Compose |

## Szybki start

Wymagania: Docker Desktop (z Docker Compose v2) i Git.

```bash
git clone https://github.com/JaneckiGit/wdpai-project.git
cd wdpai-project
cp .env.example .env   # następnie ustaw własne hasła w .env
docker compose up --build -d
```

Bez pliku `.env` Docker Compose zatrzyma się z komunikatem, której zmiennej brakuje.

## Usługi

| Usługa | Adres na komputerze | Adres w sieci Dockera | Opis |
|---|---|---|---|
| `server` (Nginx) | http://localhost:8080 | `server:80` | Aplikacja |
| `php` (PHP-FPM) | — | `php:9000` | Wykonuje kod PHP przekazany przez Nginx |
| `db` (PostgreSQL) | `localhost:5433` | `db:5432` | Baza danych, dane w wolumenie `pg-data` |
| `pgadmin-wdpai` | http://localhost:5050 | — | Przeglądanie bazy |
| `mailpit` | http://localhost:8025 | SMTP: `mailpit:1025` | Skrzynka przechwytująca maile z aplikacji |
| `ollama` | http://localhost:11434 | `ollama:11434` | API lokalnych modeli językowych |

Porty po stronie komputera można zmienić w `.env` (`APP_PORT`, `POSTGRES_PORT`, `PGADMIN_PORT`, `MAILPIT_PORT`, `OLLAMA_PORT`).

**pgAdmin** — zaloguj się danymi `PGADMIN_DEFAULT_EMAIL` / `PGADMIN_DEFAULT_PASSWORD` z `.env`, a następnie dodaj serwer: host `db`, port `5432` (port wewnątrz sieci Dockera, nie 5433), baza, użytkownik i hasło z `POSTGRES_*`.

**Skrypty startowe bazy** — pliki `*.sql` i `*.sh` z `docker/db/` są wykonywane tylko przy pierwszym uruchomieniu, gdy wolumen bazy jest pusty. Aby wykonać je ponownie, usuń dane: `docker compose down -v` (kasuje całą zawartość bazy).

**Ollama** — Docker Desktop na macOS nie udostępnia kontenerom GPU, więc modele działają na CPU. Na Linux/Windows z kartą NVIDIA można dodać `gpus: all` do usługi `ollama`. Jeśli lokalnie działa też aplikacja Ollama, zajmuje port 11434 — zmień `OLLAMA_PORT` albo ją wyłącz.

Przydatne polecenia:

| Polecenie | Zastosowanie |
|---|---|
| `docker compose ps` | Stan usług |
| `docker compose logs -f` | Logi wszystkich kontenerów na żywo |
| `docker compose exec php php -v` | Wersja PHP w kontenerze |
| `docker compose exec php php -m` | Lista modułów PHP |
| `docker compose build --no-cache` | Przebudowa obrazów bez cache |
| `docker compose down` | Zatrzymanie i usunięcie kontenerów oraz sieci (dane w wolumenach zostają) |

## Konfiguracja PHP

Obraz PHP zawiera moduły `pdo_pgsql`, `pgsql`, `gd` (z JPEG), `zip`, `bcmath`, `opcache` oraz Composer 2. Limity uploadu: `upload_max_filesize` i `post_max_size` = 512M, `max_file_uploads` = 20; Nginx ma ten sam limit (`client_max_body_size 512M`).

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
- Sekrety wyłącznie w `.env` — nigdy w repozytorium.

## Autorzy

- Mateusz Janecki — całość projektu
