# WDPAI Project

Projekt na przedmiot *Wstęp do Projektowania Aplikacji Internetowych* (Politechnika Krakowska). Temat aplikacji — do ustalenia: jaki problem rozwiązujemy, dla kogo i w jaki sposób.

## Funkcje

| Obszar | Co robi |
|---|---|
| Strona startowa | Wyświetla powitanie wygenerowane przez PHP |

## Tech stack

| Warstwa | Technologie |
|---|---|
| Backend | PHP 8.3 (PHP-FPM) |
| Serwer HTTP | Nginx |
| Konteneryzacja | Docker Compose |

## Szybki start

Wymagania: Docker Desktop (z Docker Compose v2) i Git.

```bash
git clone https://github.com/JaneckiGit/wdpai-project.git
cd wdpai-project
docker compose up --build -d
```

Aplikacja: http://localhost:8080

Przydatne polecenia:

| Polecenie | Zastosowanie |
|---|---|
| `docker compose ps` | Stan usług |
| `docker compose logs -f` | Logi wszystkich kontenerów na żywo |
| `docker compose exec php php -v` | Wersja PHP w kontenerze |
| `docker compose down` | Zatrzymanie i usunięcie kontenerów oraz sieci |

## Struktura projektu

```
.
├── docker-compose.yaml   # definicja usług
├── docker/
│   ├── nginx/            # obraz i konfiguracja Nginx
│   └── php/              # obraz PHP-FPM
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
